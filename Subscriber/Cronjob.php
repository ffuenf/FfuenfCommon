<?php
/**
 *
 * class FfuenfCommon
 *
 * @category   Shopware
 * @package    Shopware\Plugins\FfuenfCommon
 * @author     Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR
 * @copyright  Copyright (c) 2019, Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR (https://www.ffuenf.de)
 *
 */

namespace FfuenfCommon\Subscriber;

use Doctrine\DBAL\Connection;
use Enlight\Event\SubscriberInterface;
use PDO;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\HttpCache\CacheWarmer;
use Shopware\Components\HttpCache\UrlProvider\UrlProviderInterface;
use Shopware\Components\HttpCache\UrlProviderFactoryInterface;
use Shopware\Components\Routing\Context;
use Shopware\Models\Shop\Shop;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

class Cronjob extends AbstractService implements SubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_CronJob_FfuenfCacheWarmUpCron' => 'onRunWarmUpCache'
        ];
    }

    /**
     * Method that gets all ShopIDs and warms all URLs assigned to them.
     *
     * @param \Enlight_Event_EventArgs $args
     * @return string
     */
    public function onRunWarmUpCache(\Enlight_Event_EventArgs $args)
    {
        $stacksize = $this->config['cachewarmStacksize'];
        try {
            $time_start = microtime(true);
            /*
             * warm up cache with CLI command
             */
            $command = Shopware()->DocPath() . "bin/console sw:warm:http:cache -b=" . $stacksize;
            system($command);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return 'Cache wurde erfolgreich aufgewärmt!' . PHP_EOL . 'Dauer: ' . round($time, 0) . 'Sekunden';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
