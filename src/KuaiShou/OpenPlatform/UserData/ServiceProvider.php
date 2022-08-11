<?php

namespace EasyShortVideo\KuaiShou\OpenPlatform\UserData;

use EasyShortVideo\KuaiShou\OpenPlatform\UserData\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void {
        $pimple['user_data'] = static function($app) {
            return new Client($app);
        };
    }
}