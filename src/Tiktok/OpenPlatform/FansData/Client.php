<?php



namespace  EasyShortVideo\Tiktok\OpenPlatform\FansData;

use  EasyShortVideo\Kernel\BaseClient;
use  EasyShortVideo\Kernel\Exceptions\HttpException;
use  EasyShortVideo\Kernel\Exceptions\InvalidConfigException;
use  EasyShortVideo\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 */
class Client extends BaseClient {

    protected $postAccessToken = false;

    /**
     * 获取用户粉丝数据
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function data() {
        return $this->httpGet('fans/data/');
    }

}
