<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\VideoData;

use EasyShortVideo\Kernel\BaseClient;

class Client extends BaseClient
{
    protected $postAccessToken = false;

    protected $baseUri = 'https://open.douyin.com/';

    /**
     * 获取视频基础数据
     * @param string $item
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function base(string $item)
    {
        return $this->httpGet('data/external/item/base/', ['item_id' => $item]);
    }

    /**
     * 获取视频点赞数据
     * @param string $item
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function like(string $item, int $date_type = 7)
    {
        return $this->httpGet('data/external/item/like/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     * 获取视频评论数据
     * @param string $item
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function comment(string $item, int $date_type = 7)
    {
        return $this->httpGet('data/external/item/comment/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     *获取视频分享数据
     * @param string $item
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function share(string $item, int $date_type = 7)
    {
        return $this->httpGet('data/external/item/share/', ['item_id' => $item, 'date_type' => $date_type]);
    }

    /**
     * 获取视频播放数据
     * @param string $item
     * @param int $date_type
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function play(string $item, int $date_type = 7)
    {
        return $this->httpGet('data/external/item/play/', ['item_id' => $item, 'date_type' => $date_type]);
    }

}