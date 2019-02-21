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

class Backend extends AbstractService implements SubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatch_Backend_Index' => 'onPostDispatchBackendIndex'
        ];
    }

    /**
     * @param Enlight_Event_EventArgs $args
     */
    public function onPostDispatchBackendIndex(Enlight_Event_EventArgs $args)
    {
        if ((bool)$this->config['backend_design_enabled']) {
            $subject = $args->getSubject();
            $subject->View()->addTemplateDir($this->viewDirectory);
            $subject->View()->extendsTemplate('backend/index/ffuenf_common/index.tpl');
            $environment = getenv($this->config['environment_variable']);
            $subject->View()->assign('environment', $environment);
            $subject->View()->assign('base64logo', $this->config['base64logo']);
        }
    }
}
