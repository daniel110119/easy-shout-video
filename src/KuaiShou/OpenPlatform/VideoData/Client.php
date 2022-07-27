<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\VideoData;

use EasyShortVideo\Kernel\BaseClient;
use EasyShortVideo\Kernel\Exceptions\HttpException;
use EasyShortVideo\Kernel\Support\Collection;

class Client extends BaseClient
{
    protected $postAccessToken = false;

    protected $needOpenid = false;

    /**
     * 查询单一视频详情
     * @param $url
     * @param string $photo_id
     * @return array|\EasyShortVideo\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     * @throws \EasyShortVideo\Kernel\Exceptions\HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function info(string $photo_id)
    {
        return $this->httpGet("openapi/photo/info", [
            'photo_id' => $photo_id
        ]);
    }

    public function list($url, int $count = 20, string $photo_id = null,$result = [])
    {
        $query = ['count' => $count];
        if ($photo_id) $query['photo_id'] = $photo_id;
        /**
         * @var Collection $list
         */
        $list = $this->httpGet("openapi/photo/list", $query);
        if ($list['result'] != 1) {
            throw new HttpException(json_encode($list));
        }
        $video_list = $list['video_list'];
        $result = array_merge($result,$video_list);
        if (count($video_list) >= $count) {
            usort($video_list, function ($a, $b) {
                $a_time = $a['create_time'];
                $b_time = $b['create_time'];
                if ($a_time == $b_time) return 0;
                return $a_time > $b_time ? 1 : -1;
            });
            $photo_id = $video_list[0]['photo_id'];
            return $this->list($url,$count,$photo_id,$result);
        }
        return $result;
    }
}