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

use Enlight\Event\SubscriberInterface;
use FfuenfCommon\Service\AbstractService;
use Enlight_Event_EventArgs;

class Cronjob extends AbstractService implements SubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Components_CronJob_FfuenfCacheWarmUpCron' => 'onRunWarmUpCache',
            'Shopware_Components_CronJob_FfuenfCronMonitoring'  => 'onCronMonitoring'
        ];
    }

    /**
     * Method that gets all ShopIDs and warms all URLs assigned to them.
     *
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public function onRunWarmUpCache(Enlight_Event_EventArgs $args)
    {
        $stacksize = $this->config['cachewarmStacksize'];
        try {
            $time_start = microtime(true);
            /*
             * warm up cache with CLI command
             */
            $command = Shopware()->DocPath() . "bin/console sw:warm:http:cache -b=" . $stacksize;
            $session = $this->container->get('session');
            exec($command, $retArr, $retVal);
            system($command);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return 'Cache wurde erfolgreich aufgewärmt!' . PHP_EOL . 'Dauer: ' . round($time, 0) . 'Sekunden';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param Enlight_Event_EventArgs $args
     * @return string
     */
    public function onCronMonitoring(Enlight_Event_EventArgs $args)
    {
        try {
            $time_start = microtime(true);
            $session = $this->container->get('session');
            $command = Shopware()->DocPath() . 'bin/console ffuenf:cronmonitoring:check';
            exec($command, $retArr, $retVal);
            $time_end = microtime(true);
            $time = $time_end - $time_start;
            return 'CronMonitoring erfolgreich ausgeführt!' . PHP_EOL . 'Dauer: ' . round($time, 0) . 'Sekunden';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
