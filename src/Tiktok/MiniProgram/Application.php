<?php

namespace  EasyShortVideo\Tiktok\MiniProgram;

use  EasyShortVideo\Kernel\ServiceContainer;
use  EasyShortVideo\Kernel\Traits\ResponseCastable;

/**
 * Class Application.
 * @property  \EasyShortVideo\Tiktok\MiniProgram\Base\Client $base
 * @property  \EasyShortVideo\Tiktok\MiniProgram\Auth\AccessToken $access_token
 * @property  \EasyShortVideo\Tiktok\MiniProgram\Auth\Client $auth
 * @property  \EasyShortVideo\Tiktok\MiniProgram\QrCode\Client $qr_code
 * @property  \EasyShortVideo\Tiktok\MiniProgram\Server\Encryptor $encryptor
 */
class Application extends ServiceContainer {

    use ResponseCastable;

    /**
     * @var array
     */
    protected $providers = [
         \EasyShortVideo\Tiktok\MiniProgram\Base\ServiceProvider::class,
         \EasyShortVideo\Tiktok\MiniProgram\Auth\ServiceProvider::class,
         \EasyShortVideo\Tiktok\MiniProgram\QrCode\ServiceProvider::class,
         \EasyShortVideo\Tiktok\MiniProgram\Server\ServiceProvider::class
    ];

    /**
     * 初始化小程序的基础接口
     * @var array|\string[][]
     */
    protected $defaultConfig = [
        'http' => [
            'base_uri' => 'https://developer.toutiao.com/api/',
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
