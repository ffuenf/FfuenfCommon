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

class Frontend extends AbstractService implements SubscriberInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'Theme_Inheritance_Template_Directories_Collected'      => 'onTemplateDirectoriesCollect',
            'Enlight_Controller_Action_PostDispatchSecure_Frontend' => 'preloadFonts',
            'Shopware_Modules_Order_SendMail_FilterVariables'       => 'onSendMailFilterVariables'
        ];
    }

    /**
     * Modify mail variables
     *
     * @param Enlight_Event_EventArgs $args
     * @return array
     */
    public function onSendMailFilterVariables(Enlight_Event_EventArgs $args)
    {
        $return = $args->getReturn();
        try {
            $newValues = array();
            foreach ($return['sOrderDetails'] as $orderDetail) {
                foreach ($orderDetail as $key => $value) {
                    if ($key == 'articleID') {
                        $q = "SELECT path
                              FROM s_core_rewrite_urls
                              WHERE org_path = 'sViewport=detail&sArticle=" . $value . "'
                              AND subshopID = " . Shopware()->Shop()->getId();
                        $path = Shopware()->Db()->query($q)->fetchAll(\PDO::FETCH_COLUMN);
                        $orderDetail['additional_details']['articlelink'] = $path[0];
                    }
                }
                $newValues[] = $orderDetail;
            }
            $return['sOrderDetails'] = $newValues;
        } catch (\Exception $ex) {
            if ($this->config['debug']) {
                $this->logger->log(100, $ex->getMessage());
            }
        }
        return $return;
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     */
    public function onTemplateDirectoriesCollect(Enlight_Event_EventArgs $args)
    {
        $dirs = $args->getReturn();
        $dirs[] = $this->viewDirectory;
        $args->setReturn($dirs);
    }

    public function preloadFonts(Enlight_Event_EventArgs $args)
    {
        $view = $args->getSubject()->View();
        if ($this->config['preloadFonts_enabled']) {
            $preloadFonts = [];
            if (in_array('shopware', $this->config['preloadFonts_standard'])) {
                $preloadFonts[] = ['as' => 'font', 'url'  => 'themes/Frontend/Responsive/frontend/_public/src/fonts/shopware.woff2'];
                $preloadFonts[] = ['as' => 'font', 'url'  => 'themes/Frontend/Responsive/frontend/_public/src/fonts/shopware.woff'];
                $preloadFonts[] = ['as' => 'font', 'url'  => 'themes/Frontend/Responsive/frontend/_public/src/fonts/shopware.ttf'];
                $preloadFonts[] = ['as' => 'image', 'url' => 'themes/Frontend/Responsive/frontend/_public/src/fonts/shopware.svg'];
            }
            if (in_array('captcha', $this->config['preloadFonts_standard'])) {
                $preloadFonts[] = ['as' => 'font', 'url' => 'themes/Frontend/Responsive/frontend/_public/src/fonts/captcha.ttf'];
            }
            $customFonts = explode(PHP_EOL, $this->config['preloadFonts_custom']);
            $customFonts = array_filter($customFonts);
            foreach ($customFonts as $customFont) {
                $mimeType = $this->guessMimeType($customFont);
                if (!$mimeType)
                    continue;
                $preloadFonts[] = [
                    'as'  => $mimeType,
                    'url' => $customFont
                ];
            }
            $view->assign('preloadFonts_enabled', true);
            $view->assign('ffuenfFontPreload', $preloadFonts);
        }
    }

    private function guessMimeType(string $customFont) : string
    {
        if (strrpos($customFont, '.woff2') !== false) {
            return 'font';
        } elseif (strrpos($customFont, '.woff') !== false) {
            return 'font';
        } elseif(strrpos($customFont, '.ttf') !== false) {
            return 'font';
        } elseif (strrpos($customFont, '.eot') !== false) {
            return 'font';
        } elseif (strrpos($customFont, '.svg') !== false) {
            return 'image';
        } elseif (strrpos($customFont, '.otf') !== false) {
            return 'font';
        }
        return '';
    }
}
