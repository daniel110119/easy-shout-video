<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\UserData;

use EasyShortVideo\Kernel\BaseClient;

class Client extends BaseClient
{
    protected $postAccessToken = false;

    public function getFansList(int $cursor = 0)
    {
        return $this->httpGet('fans/list/', ['cursor' => $cursor, 'count' => 20]);
    }

    /**
     * 获取用户视频情况
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function item(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/item/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户粉丝数
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fans(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/fans/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户点赞数
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function like(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/like/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户评论数
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function comment(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/comment/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户分享数
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function share(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/share/', ['date_type' => $date_type]);
    }

    /**
     * 获取用户主页访问数
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function profile(int $date_type = 7)
    {
        return $this->httpGet('data/external/user/profile/', ['date_type' => $date_type]);
    }
}