<?php declare(strict_types=1);
/**
 *
 * class FfuenfCommon
 *
 * @category   Shopware
 * @package    Shopware\Plugins\FfuenfCommon
 * @author     Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR
 * @copyright  Copyright (c) 2022, Achim Rosenhagen / ffuenf - Pra & Rosenhagen GbR (https://www.ffuenf.de)
 *
 */

class BackendTest extends Enlight_Components_Test_Controller_TestCase
{
    public function setUp()
    {
        parent::setUp();

        // disable auth and acl
        Shopware()->Plugins()->Backend()->Auth()->setNoAuth();
        Shopware()->Plugins()->Backend()->Auth()->setNoAcl();

        $this->dispatch('backend');
    }

    public function testSmartyVariables()
    {
        $smartyAssigns = $this->View()->getAssign();
        $this->assertArrayHasKey('environment', $smartyAssigns);
        $this->assertArrayHasKey('base64logo', $smartyAssigns);
    }
}
