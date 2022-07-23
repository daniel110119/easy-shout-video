<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\Auth;

use EasyShortVideo\KuaiShou\OpenPlatform\Kernel\AccessToken as BaseAccessToken;

class AccessToken extends BaseAccessToken
{

    protected $endpointToGetToken = '/oauth2/access_token';

    /**
     * 配置AccessToken的
     * @param string $code
     * @return array

     */
    protected function getCredentials(string $code): array {
        return [
            'app_id'    => $this->app['config']['app_id'],
            'app_secret' => $this->app['config']['secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ];
    }
}