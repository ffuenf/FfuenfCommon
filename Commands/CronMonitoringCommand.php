<?php
/**
 *
 * class FfuenfCommon
 *
 * @category   Shopware
 * @package    Shopware\Plugins\FfuenfCommon
 * @author     Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR
 * @copyright  Copyright (c) 2020, Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR (https://www.ffuenf.de)
 *
 */

namespace FfuenfCommon\Commands;

use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CronMonitoringCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('ffuenf:cronmonitoring:check');
        $this->setDescription('Checks for deactivated CronJobs and send warning mail.');
        $this->setHelp('The <info>%command.name%</info> find out deactivated CronJobs and send a warning mail. Use plugin configuration for selecting options.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $style = new SymfonyStyle($input, $output);
        $check = $this->container->get('ffuenf_common.service.cron_monitoring');
        if ($check->check()) {
            $style->success('Successfully executed cron monitoring');
            return;
        }
        $style->success('Skipped cron monitoring');
        return;
    }
}