<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\Video;

use EasyShortVideo\Kernel\BaseClient;
use EasyShortVideo\Kernel\Exceptions\BadRequestException;
use EasyShortVideo\Kernel\Exceptions\HttpException;
use EasyShortVideo\Kernel\Exceptions\InvalidArgumentException;
use EasyShortVideo\Kernel\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;

class Client extends BaseClient
{
    protected $postAccessToken = false;
    protected $needOpenid = true;

    protected function createVideo()
    {

        /**
         * {
         *"result": 1,
         *"upload_token": "3xwn3kkerxj6g9n",上传令牌
         *"endpoint" : "uploader.test.gifshow.com" 上传网关的域名
         *}
         */
        $data = $this->httpPost('openapi/photo/start_upload/');
        if ($data['result'] != 1) {
            throw new HttpException(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 返回视频id
     * @param array $form
     * @param string $cover_path
     * @param string $upload_token
     * @return mixed
     * @throws GuzzleException
     * @throws HttpException
     * @throws \EasyShortVideo\Kernel\Exceptions\InvalidConfigException
     */
    public function publishVideo(array $form, string $cover_path, string $upload_token)
    {
        $multipart[] = [
            'name' => 'cover',
            'contents' => fopen($cover_path, 'r'),
        ];

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        $response = $this->request(
            'openapi/photo/publish',
            'POST',
            ['query' => ['upload_token'=>$upload_token], 'multipart' => $multipart, 'connect_timeout' => 300, 'timeout' => 300, 'read_timeout' => 300]
        );
        if ($response['result'] == 1) {
            return $response['video_info']['photo_id'];
        } else {
            throw new HttpException(json_encode($response));
        }
    }

    public function uploadVideo(string $filePath)
    {
        $filesize = filesize($filePath);
        $data = $this->createVideo();
        $endpoint = $data['endpoint'];
        $upload_token = $data['upload_token'];
        if ($filesize <= 1024 * 1024 * 10) {
            return $this->kuaiShouHttpUpload($endpoint, $upload_token, $filePath);
        } else {
            return $this->partUpload($endpoint, $upload_token, $filePath);
        }
    }

    /**
     * @param string $url
     * @param array $files
     * @param array $form
     * @param array $query
     * @return array|Collection|object|ResponseInterface|string
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException
     */
    protected function kuaiShouHttpUpload(string $url, string $upload_token, string $filepath)
    {

        $complete = $this->request(
            "http://{$url}/api/upload",
            'POST',
            [
                'query' => [
                    'upload_token' => $upload_token
                ],
                'headers' => [
                    'Content-Type' => 'video/mp4'
                ],
                'body' => fopen($filepath, 'rb'),
                'connect_timeout' => 300,
                'timeout' => 300,
                'read_timeout' => 300
            ]
        );
        if ($complete['result'] == 1) {
            return $upload_token;
        } else {
            throw new HttpException(json_encode($complete));
        }
    }

    /**
     * 分片上传视频
     */
    protected function partUpload(string $endpoint, string $upload_token, string $filePath): string
    {
        $checkNumber = $this->KuaiShouChunkUpload("http://{$endpoint}/api/upload/fragment", $filePath, [
            'upload_token' => $upload_token
        ]);
        $complete = $this->httpPostJson("http://{$endpoint}/api/upload/complete", [], [
            'upload_token' => $upload_token,
            'fragment_count' => $checkNumber
        ]);
        if ($complete['result'] == 1) {
            return $upload_token;
        } else {
            throw new HttpException(json_encode($complete));
        }
    }


    /**
     * 快手分片上传文件
     * @param string $url
     * @param string $file
     * @param array $query
     * @param int|float $chunkSize 每个分片的大小，不建议修改
     * @return int
     * @throws Exceptions\HttpException
     * @throws Exceptions\InvalidConfigException
     * @throws GuzzleException|BadRequestException|InvalidArgumentException
     */
    public function KuaiShouChunkUpload(string $url, string $file, array $query = [], int $chunkSize = 1024 * 1024 * 10): int
    {
        $result = [];
        $fh = Utils::tryFopen($file, 'rb');
        $filesize = filesize($file);
        if ($filesize <= 1024 * 1024 * 10) {
            throw new InvalidArgumentException('The file size cannot be less than 5M');
        }
        $chunkNum = (int)round($filesize / $chunkSize);

        rewind($fh);
        $chunkIndex = 1;
        $tempPart = md5($query['upload_token']) . '.part';

        while ($chunkIndex <= $chunkNum) {
            $left = $filesize - ($chunkIndex - 1) * $chunkSize;
            $tempPartFile = dirname($file) . DIRECTORY_SEPARATOR . $tempPart . $chunkIndex;
            file_put_contents($tempPartFile, $chunkIndex === $chunkNum ? fread($fh, $left) : fread($fh, $chunkSize));

            if (!is_writable($tempPartFile)) {
                throw new InvalidArgumentException("{$tempPartFile} can not be write");
            }
            $query['fragment_id'] = $chunkIndex - 1;
            $response = $this->request(
                $url,
                'POST',
                [
                    'query' => $query,
                    'headers' => [
                        'Content-Type' => 'video/mp4'
                    ],
                    'body' => fopen($tempPartFile, 'rb'),
                    'connect_timeout' => 300,
                    'timeout' => 300,
                    'read_timeout' => 300
                ]
            );
            if ($response['result'] !== 1) {
                throw new BadRequestException(json_encode($response));
            }
            @unlink($tempPartFile);
            $result[$chunkIndex] = $response;
            $chunkIndex++;
        }

        return $chunkNum - 1;
    }
}