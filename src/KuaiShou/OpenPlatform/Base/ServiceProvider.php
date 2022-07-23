<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\Base;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['base'] = static function($app){
            return new Client($app);
        };
    }
}