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

namespace FfuenfCommon;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;
use Shopware\Components\Plugin\Context\InstallContext;
use Shopware\Components\Plugin\Context\UninstallContext;
use Shopware\Components\Plugin\Context\UpdateContext;
use Shopware\Components\Plugin\Context\EnableContext;
use Shopware\Components\Plugin\Context\DisableContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use FfuenfCommon\Bootstrap\Installer;

class FfuenfCommon extends Plugin
{

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('ffuenf_common.plugin_dir', $this->getPath());
        $container->setParameter('ffuenf_common.view_dir', $this->getPath() . '/Resources/views');
        parent::build($container);
    }

    /**
     * @param ActivateContext $context
     */
    public function activate(ActivateContext $context)
    {
        $this->updateCronInfo();
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_ALL);
    }

    /**
     * @param DeactivateContext $context
     */
    public function deactivate(DeactivateContext $context)
    {
        if ($this->isDependentPluginsActive()) {
            throw new \Exception("Unable to deactivate plugin! There are Plugins installed which may depend on this plugin! Please uninstall them first!", 1);
        }
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_ALL);
    }

    /**
     * @param InstallContext $context
     */
    public function install(InstallContext $context)
    {
        $installer = new Installer(
            $this->getPath(),
            $this->container
        );
        $installer->install();
        $context->scheduleClearCache(InstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UninstallContext $context
     */
    public function uninstall(UninstallContext $context)
    {
        if ($this->isDependentPluginsActive()) {
            throw new \Exception("Unable to uninstall plugin! There are Plugins installed which may depend on this plugin! Please uninstall them first!", 1);
        }
        Shopware()->Db()->query("DELETE FROM s_core_snippets WHERE namespace = 'frontend/plugins/".$this->pluginName."';");
        $context->scheduleClearCache(UninstallContext::CACHE_LIST_ALL);
    }

    /**
     * @param UpdateContext $context
     */
    public function update(UpdateContext $context)
    {
        $installer = new Installer(
            $this->getPath(),
            $this->container
        );
        $currentVersion = $context->getCurrentVersion();
        $updateVersion = $context->getUpdateVersion();
        if (version_compare($currentVersion, '2.0.0', '<')) {
            $installer->update200();
        }
        $context->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    /**
     * @param EnableContext $context
     */
    public function enable(EnableContext $context)
    {
        $context->scheduleClearCache(EnableContext::CACHE_LIST_ALL);
    }

    /**
     * @param DisableContext $context
     */
    public function disable(DisableContext $context)
    {
        $context->scheduleClearCache(DisableContext::CACHE_LIST_ALL);
    }

    /**
     * @return bool
     */
    public function isDependentPluginsActive() {
        $modelManager = $this->container->get('models');
        $pluginRepository = $modelManager->getRepository(Plugin::class);
        $plugins = $pluginRepository->findAll();
        foreach($plugins as $plugin) {
            if($plugin->getName() != 'FfuenfCommon') {
                if(($plugin->getInstalled() || $plugin->getActive()) && strpos(strtolower($plugin->getName()), 'Ffuenf') !== false) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Method that reads the next time and interval of execution of the ClearHttpCache CronJob and sets these values
     * in the CacheWarmUp CronJob initially. These values can be changed by the backend admin.
     * @throws \Enlight_Exception
     */
    private function updateCronInfo()
    {
        $ClearHttpCacheJob = $this->container->get('cron')->getJobByAction('Shopware_CronJob_ClearHttpCache');
        if (null === $ClearHttpCacheJob) {
            return;
        }
        $FfuenfCacheWarmUpJob = $this->container->get('cron')->getJobByAction('Shopware_Components_CronJob_FfuenfCacheWarmUpCron');
        $ClearHttpCacheJobNext = $ClearHttpCacheJob->getNext();
        $ClearHttpCacheJobInterval = $ClearHttpCacheJob->getInterval();
        if ($ClearHttpCacheJobInterval) {
            $FfuenfCacheWarmUpJob->setInterval($ClearHttpCacheJobInterval);
        }
        if ($ClearHttpCacheJobNext) {
            $FfuenfCacheWarmUpJob->setNext($ClearHttpCacheJobNext);
        }
        $this->container->get('cron')->updateJob($FfuenfCacheWarmUpJob);
    }
}
