<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\Auth;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        !isset($pimple['access_token']) && $pimple['access_token'] = static function($app) {
            return new AccessToken($app);
        };

        !isset($pimple['auth']) && $pimple['auth'] = static function($app) {
            return new Client($app);
        };
    }
}