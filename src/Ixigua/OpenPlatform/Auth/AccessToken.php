<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\Auth;

use EasyShortVideo\Tiktok\OpenPlatform\Kernel\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{

    /**
     * @var string
     */
    protected $endpointToGetToken = 'oauth/access_token/';

    /**
     * 配置AccessToken的
     * @param string $code
     * @return array

     */
    protected function getCredentials(string $code): array {
        return [
            'client_key'    => $this->app['config']['app_id'],
            'client_secret' => $this->app['config']['secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ];
    }
}