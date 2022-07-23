<?php



namespace  EasyShortVideo\Tiktok\OpenPlatform\Base;

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
        $pimple['base'] = static function($app) {
            return new Client($app);
        };
    }
}
