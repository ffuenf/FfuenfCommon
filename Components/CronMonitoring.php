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

namespace FfuenfCommon\Components;

use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Doctrine\DBAL\Connection;

class CronMonitoring
{
    /**
     * @var string
     */
    protected $pluginName;

    /**
     * @var string
     */
    protected $pluginDirectory;

    /**
     * @var ModelManager
     */
    private $connection;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Shopware_Components_Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(string $pluginName, string $pluginDirectory, Container $container, ModelManager $modelManager, Connection $connection)
    {
        $this->connection      = $connection;
        $this->modelManager    = $modelManager;
        $this->container       = $container;
        $this->pluginName      = $pluginName;
        $this->pluginDirectory = $pluginDirectory;
        $this->config          = $this->container->get('shopware.plugin.config_reader')->getByPluginName($this->pluginName);
        $this->logger          = $this->container->get('pluginlogger');
    }

    public function check()
    {
        if ($this->isLastRunOld(30)) {
            $this->checkCrons();
            return true;
        }
        return false;
    }

    private function checkCrons()
    {
        $downtime = $this->config['monitoredCronsDowntime'];
        $crons = $this->config['monitoredCronsList'];
        $downCrons = $this->connection->executeQuery(
            'SELECT * FROM s_crontab WHERE id IN (:cronIds) AND ((active != 1 AND end < NOW() - INTERVAL :downtime MINUTE) OR (end IS NULL AND start < NOW() - INTERVAL :downtime MINUTE))',
            [
                ':cronIds'  => $crons,
                ':downtime' => $downtime
            ],
            [
                ':cronIds' => Connection::PARAM_INT_ARRAY
            ])->fetchAll();
        if (is_array($downCrons) && count($downCrons) > 0) {
            //don't send warn mail every minute
            if (!isset($this->config['monitoredCronsWarningCount'])) {
                $this->config['monitoredCronsWarningCount'] = 0;
            }
            $reminderGap = $this->config['monitoredCronsWarningCount'] * $this->config['monitoredCronsWarningCount'] * 60;
            if ($reminderGap > 3600) {
                $reminderGap = 3600; //max reminder gap one hour
            }
            if (!isset($this->config['monitoredCronsLastWarning']) || $this->config['monitoredCronsLastWarning'] + $reminderGap < time()) {
                if ($this->config['monitoredCronsMail'] != '') {
                    $this->sendCronDownMail($downCrons);
                }
                $this->logDownCrons($downCrons);
                $this->writeConfigValue('monitoredCronsWarningCount', ++$this->config['monitoredCronsWarningCount']);
                $this->writeConfigValue('monitoredCronsLastWarning', time());
            }
        } else {
            //reset variables for reminder gap
            $this->writeConfigValue('monitoredCronsWarningCount', 0);
            $this->writeConfigValue('monitoredCronsLastWarning', 0);
        }
    }

    private function isLastRunOld($seconds)
    {
        if (!isset($this->config['monitoredCronsLastRun']) || $this->config['monitoredCronsLastRun'] + $seconds < time()) {
            $this->writeConfigValue('monitoredCronsLastRun', time());
            return true;
        }
        return false;
    }

    private function logDownCrons($downCrons)
    {
        foreach ($downCrons as $cron) {
            $this->logger->warning(
                'CronJob inactive: ' . $cron['name'] . ' (' . $cron['action'] . ') - last run: ' . $cron['end'] . ' # ',
                array(
                    'plugin'   => 'FfuenfCommon',
                    'methode'  => 'checkCrons',
                    'job'      => $cron['name'],
                    'action'   => $cron['action'],
                    'last_run' => $cron['end']
                )
            );
        }
    }

    private function sendCronDownMail($downCrons)
    {
        $addresses = explode(';', $this->config['monitoredCronsMail']);
        if (is_array($addresses) && count($addresses) > 0) {
            $mail = Shopware()->TemplateMail()->createMail(
                'sCRONWARNING',
                array(
                    'downCrons' => $downCrons
                )
            );
            foreach ($addresses AS $address) {
                $mail->addTo(trim($address));
            }
            $mail->send();
        } else {
            $this->logger->warning(
                'Keine Empfänger gefunden',
                array(
                    'plugin'  => 'FfuenfCommon',
                    'methode' => 'sendCronDownMail'
                )
            );
        }
    }

    private function writeConfigValue($name, $value)
    {
        $plugin = $this->modelManager->getRepository('\Shopware\Models\Plugin\Plugin')->findOneBy(array('name' => $this->pluginName));
        $mainShop = $this->modelManager->getRepository('\Shopware\Models\Shop\Shop')->findOneBy(array('default' => 1, 'active' => 1));
        $this->container->get('shopware.plugin.config_writer')->saveConfigElement($plugin, $name, $value, $mainShop);
    }
}