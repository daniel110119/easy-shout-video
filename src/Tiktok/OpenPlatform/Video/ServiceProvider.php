<?php


namespace  EasyShortVideo\Tiktok\OpenPlatform\Video;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 *
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $pimple): void
    {
        $pimple['video'] = static function ($app) {
            return new Client($app);
        };
    }
}
