<?php
/**
 *
 * class FfuenfCommon
 *
 * @category   Shopware
 * @package    Shopware\Plugins\FfuenfCommon
 * @author     Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR
 * @copyright  Copyright (c) 2021, Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR (https://www.ffuenf.de)
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
            'Enlight_Controller_Action_PreDispatch'        => 'onPreDispatch',
            'Enlight_Controller_Action_PostDispatch_Api'   => 'onPostDispatchApi',
            'Enlight_Controller_Action_PreDispatch_Api'    => 'onPreDispatchApi'
        ];
    }

    public function onPreDispatch(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        $view->assign('sReleaseCommit', $this->config['releasecommit']);
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

    public function onPostDispatchApi(Enlight_Event_EventArgs $args)
    {
        if ($this->config['api_log'] != 1) {
            return;
        }
        $controller = $args->getSubject();
        if ($this->config['api_log_verbose'] == 1) {
            $this->getLogger()->info('RESPONSE-CODE: ' . $controller->Response()->getHttpResponseCode() . ' RESPONSE: ' . $controller->Response()->getBody());
        } else {
            $this->getLogger()->info('RESPONSE-CODE: ' . $controller->Response()->getHttpResponseCode());
        }
    }

    public function onPreDispatchApi(Enlight_Event_EventArgs $args)
    {
        if ($this->config['api_log'] != 1) {
            return;
        }
        $controller = $args->getSubject();
        $view = $controller->View();
        if ($this->config['api_log_verbose'] == 1) {
            if (is_array($controller->Request()->getPost())) {
                $payload = json_encode($controller->Request()->getPost());
            } else {
                $payload = var_export($controller->Request()->getPost(), true);
            }
            $this->getLogger()->info('REQUEST: ' . $controller->Request()->getMethod() . ' - ' . $controller->Request()->getRequestUri() . ' PAYLOAD: ' . $payload);
        } else {
            $this->getLogger()->info('REQUEST: ' . $controller->Request()->getMethod() . ' - ' . $controller->Request()->getRequestUri());
        }
    }

}
