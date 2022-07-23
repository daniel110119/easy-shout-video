<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\Kernel;

use EasyShortVideo\Kernel\Contracts\AccessTokenInterface;
use EasyShortVideo\Kernel\Exceptions\AccessTokenException;
use EasyShortVideo\Kernel\Exceptions\HttpException;
use EasyShortVideo\Kernel\Exceptions\InvalidArgumentException;
use EasyShortVideo\Kernel\Exceptions\RuntimeException;
use EasyShortVideo\Kernel\ServiceContainer;
use EasyShortVideo\Kernel\Traits\HasHttpRequests;
use EasyShortVideo\Kernel\Traits\InteractsWithCache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AccessToken implements AccessTokenInterface
{
    use HasHttpRequests, InteractsWithCache;

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var string
     */
    protected $endpointToGetToken;

    /**
     * @var string
     */
    protected $queryName;

    /**
     * @var array
     */
    protected $token;

    /**
     * @var string
     */
    protected $tokenKey = 'access_token';


    /**
     * 刷新 access_token
     * @var string
     */
    protected $endpointToRefresh = 'oauth2/refresh_token';

    /**
     * @var string
     */
    protected $refreshKey = 'refresh_token';

    /**
     * @var string
     */
    protected $cachePrefix = 'kuaishou_open_platform.';

    /**
     * @var string 用户授权后的ticket
     */
    protected $code;

    /**
     * @var string 用户唯一标识
     */
    protected $openid;

    /**
     * @var int 最大允许刷新RefreshToken的次数
     */
    protected $refreshReTokenLimit = 5;

    /**
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->openid = $app['config']->get('openid');
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): AccessToken
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @param string $openid
     * @return $this
     */
    public function setOpenid(string $openid): AccessToken
    {
        $this->openid = $openid;
        return $this;
    }

    /**
     * @param string $code
     * @return array
     * @throws AccessTokenException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setAuth(string $code): array
    {
        $code = $code ?: $this->code;
        if (empty($code)) {
            throw new AccessTokenException('code cannot bt empty');
        }
        $token = $this->requestToken($this->getCredentials($code), true);

        if (empty($this->openid)) {
            $this->setOpenid($token['open_id']);
        }
        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 1296000);
        $this->setReToken($token[$this->refreshKey], $token['refresh_expires_in'] ?? 2592000);

        return $token;
    }

    /**
     * @param string $token
     * @param int $lifetime
     *
     * @return AccessTokenInterface
     *
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|InvalidArgumentException
     */
    public function setToken(string $token, int $lifetime): AccessTokenInterface
    {
        $this->getCache()->set($this->getCacheKey($this->tokenKey), [
            $this->tokenKey => $token,
            'expires_in' => $lifetime
        ], $lifetime);

        if (!$this->getCache()->has($this->getCacheKey($this->tokenKey))) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
    }

    /**
     *
     * @param string $token
     * @param int $lifetime
     * @param int $times
     * @return AccessTokenInterface
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setReToken(string $token, int $lifetime, int $times = 0): AccessTokenInterface
    {
        $this->getCache()->set($this->getCacheKey($this->refreshKey), [
            're_times' => $times,
            $this->refreshKey => $token,
            'refresh_deadline' => time() + $lifetime - 86400
        ], $lifetime);

        if (!$this->getCache()->has($this->getCacheKey($this->tokenKey))) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
    }

    /**
     * 获取access_token的key
     * @param string $type
     * @return string
     */
    protected function getCacheKey(string $type): string
    {
        return $this->cachePrefix . $type . '.' . md5(json_encode($this->getCredentials($this->openid)));
    }

    /**
     * @return false|Response
     */
    protected function test()
    {
        if ($this->app['config']->get('test')) {
            $response = <<<json
{
   "result": 1,
   "access_token": "kuaishou_access_token_12312321321",
   "expires_in": 3600,
   "refresh_token": "kuaishou_refresh_token_12312321321",
   "refresh_token_expires_in":6480000,
   "open_id": "kuaishou_openid",
   "scopes": ["user_info"]

}
json;
            return new Response(200, [], $response);
        } else {
            return false;
        }
    }


    /**
     * @param array $credentials
     * @param bool $toArray
     * @return array|\EasyShortVideo\Kernel\Support\Collection|\EasyShortVideo\Kernel\Traits\string|mixed|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     */
    protected function requestToken(array $credentials, bool $toArray)
    {
        if ($this->test() instanceof ResponseInterface) {
            $response = $this->test();
        } else {
            $response = $this->setHttpClient($this->app['http_client'])->request(
                $this->getEndpoint(), 'POST', [
                    'multipart' => $this->getPostFormData($credentials)
                ]
            );
        }

        $result = json_decode($response->getBody()->getContents(), true);
        $formatted = $this->castResponseToType($response, $this->app['config']->get('response_type'));

        if ($result['result'] != 1) {
            throw new HttpException('Request access_token fail: ' . json_encode($result, JSON_UNESCAPED_UNICODE), $response, $formatted);
        }
        return $toArray ? $result : $formatted;
    }

    /**
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function getEndpoint(): string
    {
        if (empty($this->endpointToGetToken)) {
            throw new InvalidArgumentException('No endpoint for access token request.');
        }

        return $this->endpointToGetToken;
    }

    /**
     * 构建post数据
     * @param array $credentials
     * @return array
     */
    private function getPostFormData(array $credentials): array
    {
        $multipartData = [];
        foreach ($credentials as $key => $value) {
            $multipartData[] = [
                'name' => $key,
                'contents' => $value
            ];
        }

        return $multipartData;
    }

    /**
     * @return array
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(): array
    {
        if (empty($this->openid)) {
            throw new AccessTokenException('missing openid parameter');
        }

        $cacheKey = $this->getCacheKey($this->tokenKey);
        $cache = $this->getCache();

        if ($cache->has($cacheKey) && $result = $cache->get($cacheKey)) {
            $token = $result;
        } else {
            $reCacheKey = $this->getCacheKey($this->refreshKey);
            if ($cache->has($reCacheKey) && $reResult = $cache->get($reCacheKey)) {
                $token = $this->refreshToken($reResult);
            } else {
                throw new AccessTokenException('token is timeout!');
            }
        }

        return $token;
    }

    /**
     * 刷新Token
     * @param $reResult
     * @return mixed
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException|AccessTokenException
     */
    public function refreshToken($reResult)
    {
        $response = $this->setHttpClient($this->app['http_client'])->request(
            $this->endpointToRefresh, 'POST', [
                'multipart' => $this->getPostFormData([
                    'app_id' => $this->app['config']['app_id'],
                    'app_secret' => $this->app['config']['secret'],
                    'grant_type' => 'refresh_token',
                    $this->refreshKey => $reResult[$this->refreshKey]
                ])
            ]
        );
        $result = json_decode($response->getBody()->getContents(), true);
        if ($result['result'] != 1) {
            throw new AccessTokenException('Refresh access_token fail: ' . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        $this->setToken($result[$this->tokenKey], $result['expires_in'] ?? 3600);
        $this->setReToken($result[$this->refreshKey], $result['refresh_token_expires_in'] ?? 64800);
        return $result;
    }

    /**
     * @return AccessTokenInterface
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function refresh(): AccessTokenInterface
    {
        $this->getCache()->delete($this->getCacheKey($this->tokenKey));
        $this->getToken();

        return $this;
    }

    /**
     * @return array
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getQuery(): array
    {
        return [
            $this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey],
            'openid'=>$this->openid
        ];
    }


    /**
     * @param RequestInterface $request
     * @param array $requestOptions
     * @return RequestInterface
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(),$query);

        $query = http_build_query(array_merge($this->getQuery(),$query));

        return $request->withUri($request->getUri()->withQuery($query));
    }


    /**
     * 不添加openid 添加 appid
     * @param RequestInterface $request
     * @param array $requestOptions
     * @return RequestInterface
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function applyNeedOpenIdToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(),$query);
        $query = array_merge($this->getQuery(),$query);
        unset($query['openid']);
        $query['app_id'] = $this->app->config['app_id'];
        $query = http_build_query($query);
        return $request->withUri($request->getUri()->withQuery($query));
    }


    /**
     * @param RequestInterface $request
     * @param array $requestOptions
     * @return RequestInterface
     * @throws AccessTokenException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function applyToPostRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        $query = $request->getBody()->getContents();
        $request->getBody()->rewind();
        $query = array_merge($this->getQuery(),\GuzzleHttp\json_decode($query,true));
        return $request->withBody(Utils::streamFor(json_encode($query)));
    }

    /**
     * Credential for get token.
     * @param string $code
     * @return array
     */
    abstract protected function getCredentials(string $code): array;
}