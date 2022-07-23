<?php



namespace  EasyShortVideo\Tiktok\OpenPlatform\FansData;

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
        $pimple['fans_data'] = static function($app) {
            return new Client($app);
        };
    }
}
