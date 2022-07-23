<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\Video;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['video'] = static function($app){
            return new Client($app);
        };
    }
}