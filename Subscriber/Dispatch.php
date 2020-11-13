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

namespace FfuenfCommon\Subscriber;

use Enlight\Event\SubscriberInterface;
use FfuenfCommon\Service\AbstractService;
use Enlight_Event_EventArgs;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Dispatch extends AbstractService implements SubscriberInterface
{

    /** @var  Logger */
    protected $logger;

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure' => 'onPostDispatch',
            'Enlight_Controller_Action_PostDispatch_Api'   => 'onPostDispatchApi',
            'Enlight_Controller_Action_PreDispatch_Api'    => 'onPreDispatchApi'
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

    /**
     * @return Logger
     */
    private function getLogger()
    {
        if ($this->logger) {
            return $this->logger;
        }
        return $this->logger = new Logger('apiLogger', [new RotatingFileHandler(Shopware()->Container()->getParameter('kernel.logs_dir') . '/api.log', 7)]);
    }

    public function onPostDispatchApi(\Enlight_Event_EventArgs $args)
    {
        if ($this->config['api_log'] != 1) {
            return;
        }
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        if ($this->config['api_log_verbose'] == 1) {
            $this->getLogger()->info('RESPONSE: ' . $controller->Response()->getBody());
        } else {
            $this->getLogger()->info('RESPONSE-CODE: ' . $controller->Response()->getHttpResponseCode());
        }
    }

    public function onPreDispatchApi(\Enlight_Event_EventArgs $args)
    {
        if ($this->config['api_log'] != 1) {
            return;
        }
        /** @var $controller \Enlight_Controller_Action */
        $controller = $args->getSubject();
        $view = $controller->View();
        if ($this->config['api_log_verbose'] == 1) {
            $this->getLogger()->info('PAYLOAD: ' . $controller->Request()->getPost());
        } else {
            $this->getLogger()->info('REQUEST: ' . $controller->Request()->getMethod() . ' - ' . $controller->Request()->getRequestUri());
        }
    }
}
