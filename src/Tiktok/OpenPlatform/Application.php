<?php

namespace  EasyShortVideo\Tiktok\OpenPlatform;

use  EasyShortVideo\Kernel\ServiceContainer;
use  EasyShortVideo\Kernel\Traits\ResponseCastable;

/**
 * Class Application.
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\Auth\AccessToken $access_token
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\Auth\Client $auth
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\Base\Client $base
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\UserData\Client $user_data
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\FansData\Client $fans_data
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\Video\Client $video
 * @property  \EasyShortVideo\Tiktok\OpenPlatform\VideoData\Client $video_data
 */
class Application extends ServiceContainer {

    use ResponseCastable;

    /**
     * @var array
     */
    protected $providers = [
         \EasyShortVideo\Tiktok\OpenPlatform\Auth\ServiceProvider::class,
         \EasyShortVideo\Tiktok\OpenPlatform\FansData\ServiceProvider::class,
         \EasyShortVideo\Tiktok\OpenPlatform\UserData\ServiceProvider::class,
         \EasyShortVideo\Tiktok\OpenPlatform\Video\ServiceProvider::class,
         \EasyShortVideo\Tiktok\OpenPlatform\VideoData\ServiceProvider::class,
         \EasyShortVideo\Tiktok\OpenPlatform\Base\ServiceProvider::class
    ];

    /**
     * 初始化开放平台的基础接口
     * @var array|\string[][]
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://open.douyin.com/',
        ],
    ];

    /**
     * Handle dynamic calls.
     *
     * @param string $method
     * @param array $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args) {
        return $this->base->$method(...$args);
    }
}
