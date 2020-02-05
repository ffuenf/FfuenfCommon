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

class Dispatch extends AbstractService implements SubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure' => 'onPostDispatch'
        ];
    }

    public function onPostDispatch(\Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $request = $args->getRequest();
        $response = $args->getResponse();
        if (!$request->isDispatched() || $response->isException()) {
            return;
        }
        if ($this->config['monitoredCronsOnDispatch']) {
            Shopware()->Container()->get('ffuenf_common.service.cron_monitoring')->check();
        }
        if ($this->config['datadog_frontend_logging_enabled'] == 1 || $this->config['datadog_real_user_monitoring_enabled'] == 1) {
            $view->assign('datadogLogsUrl', $this->config['datadog_logs_url']);
            $view->assign('datadogRumUrl', $this->config['datadog_rum_url']);
            $view->assign('datadogClientToken', $this->config['datadog_client_token']);
            $view->assign('datadogApplicationId', $this->config['datadog_application_id']);
        }
    }
}
