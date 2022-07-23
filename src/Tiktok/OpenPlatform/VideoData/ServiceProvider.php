<?php



namespace  EasyShortVideo\Tiktok\OpenPlatform\VideoData;

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
        $pimple['video_data'] = static function($app) {
            return new Client($app);
        };
    }
}
