<?php declare(strict_types=1);
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

use Shopware\Models\Shop\Repository;
use Shopware\Models\Shop\Shop;

require __DIR__ . '/../../../../../autoload.php';

class TestKernel extends \Shopware\Kernel
{
    /**
     * Static method to start boot kernel without leaving local scope in test helper.
     */
    public static function start()
    {
        $kernel = new self('testing', true);
        $kernel->boot();
        $container = $kernel->getContainer();
        /** @var $repository \Shopware\Models\Shop\Repository */
        $repository = $container->get('models')->getRepository(Shop::class);
        $shop = $repository->getActiveDefault();
        $shop->registerResources();
        $_SERVER['HTTP_HOST'] = $shop->getHost();
    }

    protected function getConfigPath()
    {
        return __DIR__ . '/config.php';
    }
}
TestKernel::start();
