<?php


namespace EasyShortVideo\Ixigua\OpenPlatform\Video;

use  EasyShortVideo\Kernel\BaseClient;
use  EasyShortVideo\Kernel\Exceptions\BadRequestException;
use  EasyShortVideo\Kernel\Exceptions\HttpException;
use  EasyShortVideo\Kernel\Exceptions\InvalidArgumentException;
use  EasyShortVideo\Kernel\Exceptions\InvalidConfigException;
use  EasyShortVideo\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client.
 *
 */
class Client extends BaseClient
{

    protected $postAccessToken = false;
    protected $baseUri = 'https://open.douyin.com/'; #傻逼平台 上传是这个域名

    /**
     * 视频上传
     * @param string $filePath
     * @return array|Collection|object|ResponseInterface|string
     * @throws HttpException
     * @throws InvalidConfigException
     * @throws GuzzleException
     */
    public function uploadVideo(string $filePath)
    {
        $filesize = filesize($filePath);
        if ($filesize <= 1024 * 1024 * 5) {
            return $this->httpUpload('video/upload/', ['video' => $filePath]);
        }else{
            return $this->partUpload($filePath);
        }
    }

    /**
     * 分片上传视频
     * @param string $filePath
     * @return array
     * @throws BadRequestException
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException|InvalidArgumentException
     */
    public function partUpload(string $filePath): array
    {
        $initInfo = $this->httpPostJson('xigua/video/part/init/');
        if ($initInfo['data']['error_code'] !== 0) {
            throw new BadRequestException($initInfo['data']['description']);
        }

        $this->httpChunkUpload('xigua/video/part/upload/', $filePath, [
            'upload_id' => $initInfo['data']['upload_id']
        ]);

        return $this->httpPostJson('xigua/video/part/complete/', [], [
            'upload_id' => $initInfo['data']['upload_id']
        ]);
    }

    /**
     * 创建视频
     * @param string $videoId
     * @param array $body
     * @return array
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     * video_id    string    video_id, 通过上传视频接口得到    v0201f510000smhdsr0ggl1v4a2b2ps1    true
     * text    string    标题长度应该在5-30字之间    分享你此刻的心情~ #头条#    true
     * praise    bool    是否给视频开通可以赞赏的入口（授权账号需要在西瓜视频端内开通「实名认证」）        true
     * claim_origin    bool    是否声明原创（授权账号需要在西瓜视频端内开通「实名认证」）        true
     * abstract    string    视频简介，400字以内    简介    true
     */
    public function publishVideo(string $videoId, array $body = []): array
    {
        $body['video_id'] = $videoId;

        return $this->httpPostJson('xigua/video/create/', $body);
    }

    /**
     * 获取用户视频列表
     * @param int $cursor
     * @param int $count
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function getList(int $cursor, int $count = 10)
    {
        return $this->httpGet('xigua/video/list/', [
            'cursor' => $cursor,
            'count' => $count
        ]);
    }

    /**
     * 查询特定视频数据
     * @param array $item_ids
     * @return array|Collection|object|ResponseInterface|string
     * @throws GuzzleException
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function getData(array $item_ids)
    {
        return $this->httpPost('xigua/video/data/', $item_ids);
    }
}
