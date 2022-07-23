<?php



namespace  EasyShortVideo\Tiktok\MiniProgram\QrCode;

use  EasyShortVideo\Kernel\BaseClient;
use  EasyShortVideo\Kernel\Exceptions\HttpException;
use  EasyShortVideo\Kernel\Exceptions\InvalidConfigException;
use  EasyShortVideo\Kernel\Http\StreamResponse;
use  EasyShortVideo\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 *
 */
class Client extends BaseClient {
    /**
     * Get QrCode unLimit.【获取抖音小程序码，目前只支持永久码】
     * @param string $appName
     * @param array $optional
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException

     */
    public function getUnLimit(string $appName, array $optional = []) {
        if (!empty($optional['path'])) {
            $optional['path'] = urlencode($optional['path']);
        }
        $params = array_merge([
            'set_icon' => true,
            'appname'  => $appName
        ], $optional);

        return $this->getStream('apps/qrcode', $params);
    }

    /**
     * Get stream.
     *
     * @param string $endpoint
     * @param array $params
     *
     * @return array|Collection|object|ResponseInterface|string
     *
     * @throws InvalidConfigException
     * @throws GuzzleException|HttpException
     */
    protected function getStream(string $endpoint, array $params) {
        $response = $this->requestRaw($endpoint, 'POST', ['json' => $params]);

        if (false === stripos($response->getHeaderLine('Content-Type'), 'application/json')) {
            return StreamResponse::buildFromPsrResponse($response);
        }

        return $this->castResponseToType($response, $this->app['config']->get('response_type'));
    }
}
