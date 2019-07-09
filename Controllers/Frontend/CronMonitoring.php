<?php
class Shopware_Controllers_Frontend_CronMonitoring extends Enlight_Controller_Action
{
    public function indexAction()
    {
        Shopware()->Plugins()->Controller()->ViewRenderer()->setNoRender();
        $this->Front()->Plugins()->Json()->setRenderer(false);
        if (Shopware()->Container()->get('ffuenf_common.service.cron_monitoring')->check()) {
            echo('run cron monitoring');
            return;
        }
        echo('skip cron monitoring');
        return;
    }
}