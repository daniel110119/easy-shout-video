<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform;

use EasyShortVideo\Kernel\ServiceContainer;
use EasyShortVideo\Kernel\Traits\ResponseCastable;
use EasyShortVideo\KuaiShou\OpenPlatform\Auth\AccessToken;

/**
 * @property \EasyShortVideo\KuaiShou\OpenPlatform\Auth\AccessToken $access_token
 * @property \EasyShortVideo\KuaiShou\OpenPlatform\Base\ServiceProvider $base
 * @property \EasyShortVideo\KuaiShou\OpenPlatform\Video\ServiceProvider $video
 * @property \EasyShortVideo\KuaiShou\OpenPlatform\VideoData\ServiceProvider $video_data
 * @property \EasyShortVideo\KuaiShou\OpenPlatform\UserData\ServiceProvider $user_data
 */
class Application extends ServiceContainer
{
    use ResponseCastable;

    protected $providers=[
        \EasyShortVideo\KuaiShou\OpenPlatform\Auth\ServiceProvider::class,
        \EasyShortVideo\KuaiShou\OpenPlatform\Video\ServiceProvider::class,
        \EasyShortVideo\KuaiShou\OpenPlatform\VideoData\ServiceProvider::class,
        \EasyShortVideo\KuaiShou\OpenPlatform\UserData\ServiceProvider::class,
    ];


    protected $defaultConfig = [
        'http'=>[
            'base_uri'=>'https://open.kuaishou.com/'
        ]
    ];

    public function __call(string $method, array $args)
    {
        return $this->base->$method(...$args);
    }
}