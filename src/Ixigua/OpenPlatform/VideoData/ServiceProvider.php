<?php

namespace EasyShortVideo\Ixigua\OpenPlatform\VideoData;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['video_data'] = static function($app){
            return new Client($app);
        };
    }
}