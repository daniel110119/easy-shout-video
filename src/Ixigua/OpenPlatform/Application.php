<?php

namespace EasyShortVideo\Ixigua\OpenPlatform;


use EasyShortVideo\Ixigua\OpenPlatform\Video\Client;
use EasyShortVideo\Kernel\ServiceContainer;
use EasyShortVideo\Kernel\Traits\ResponseCastable;

/**
 * Class Application.
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\Auth\AccessToken $access_token
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\Auth\Client $auth
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\Base\Client $base
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\UserData\Client $user_data
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\FansData\Client $fans_data
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\Video\Client $video
 * @property  \EasyShortVideo\Ixigua\OpenPlatform\VideoData\Client $video_data
 */
class Application extends ServiceContainer
{

    use ResponseCastable;

    /**
     * @var array
     */
    protected $providers = [
        \EasyShortVideo\Ixigua\OpenPlatform\Auth\ServiceProvider::class,
//         \EasyShortVideo\Ixigua\OpenPlatform\FansData\ServiceProvider::class,
//         \EasyShortVideo\Ixigua\OpenPlatform\UserData\ServiceProvider::class,
         \EasyShortVideo\Ixigua\OpenPlatform\Video\ServiceProvider::class,
//         \EasyShortVideo\Ixigua\OpenPlatform\VideoData\ServiceProvider::class,
//         \EasyShortVideo\Ixigua\OpenPlatform\Base\ServiceProvider::class
    ];

    /**
     * 初始化开放平台的基础接口
     * @var array|\string[][]
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://open-api.ixigua.com/',
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
    public function __call(string $method, array $args)
    {
        return $this->base->$method(...$args);
    }
}
