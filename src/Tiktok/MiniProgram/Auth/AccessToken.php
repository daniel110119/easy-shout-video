<?php



namespace  EasyShortVideo\Tiktok\MiniProgram\Auth;

use  EasyShortVideo\Tiktok\MiniProgram\Kernel\AccessToken as BaseAccessToken;

/**
 * Class AccessToken.
 *
 */
class AccessToken extends BaseAccessToken {

    /**
     * @return array
     */
    protected function getCredentials(): array {
        return [
            'appid'      => $this->app['config']['app_id'],
            'secret'     => $this->app['config']['secret'],
            'grant_type' => 'client_credential',
        ];
    }
}
