<?php

namespace  EasyShortVideo\Tiktok\OpenPlatform\UserData;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 */
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
