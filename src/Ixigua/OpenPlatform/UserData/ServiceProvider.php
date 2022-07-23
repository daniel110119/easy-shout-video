<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\UserData;

use EasyShortVideo\Ixigua\OpenPlatform\UserData\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['user_data'] = static function($app){
            return new Client($app);
        };
    }
}