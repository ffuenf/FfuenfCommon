<?php
/**
 * Class Shopware_Controllers_Frontend_Cspreport
 */

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_Cspreport extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    private $config;
    private $logger;

    /**
     * {@inheritdoc}
     *
     * @throws \Enlight_Controller_Exception
     */
    public function preDispatch()
    {
        $this->config = $this->get('ffuenf_common.config');
        $this->logger = $this->get('pluginlogger');
        if (!empty(trim($config['whitelistIP']))) {
            $isIPWhitelisted = in_array($this->get('front')->Request()->getClientIp(), explode("\n", $config['whitelistIP']), false);
            if (!$isIPWhitelisted) {
                throw new Enlight_Controller_Exception(
                    'Controller "' . $this->Request()->getControllerName() . '" not found',
                    Enlight_Controller_Exception::Controller_Dispatcher_Controller_Not_Found
                );
            }
        }
    }

    public function indexAction()
    {
        if (strtoupper($this->Request()->getMethod()) === 'POST') {
            $this->logger->log(300, $this->Request()->getRawBody());
        }
        die();
    }

    /**
     * function to deactivate CSRF token
     * validation for specified actions
     * @return array of ignored actions
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'index'
        ];
    }
}
