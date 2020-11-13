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

namespace FfuenfCommon\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Shopware_Components_Config;
use Enlight_Template_Manager;
use FfuenfCommon\Components\Logger;

abstract class AbstractService
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
     * @var string
     */
    protected $viewDirectory;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Enlight_Template_Manager
     */
    protected $templateManager;

    /**
     * @var Shopware_Components_Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param string $pluginName
     * @param string $pluginDirectory
     * @param ContainerInterface $container
     */
    public function __construct(string $pluginName, string $pluginDirectory, string $viewDirectory, ContainerInterface $container)
    {
        $this->pluginName           = $pluginName;
        $this->pluginDirectory      = $pluginDirectory;
        $this->viewDirectory        = $viewDirectory;
        $this->container            = $container;
        $this->templateManager      = $this->container->get('template');
        $this->config               = $this->container->get('shopware.plugin.config_reader')->getByPluginName($this->pluginName);
        $this->logger               = $this->container->get('pluginlogger');
    }
}