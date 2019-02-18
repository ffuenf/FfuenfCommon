<?php declare(strict_types=1);
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

use FfuenfCommon\Components\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    public function testAddRecordAtLevelDebugStoresMessageInDebug()
    {
        $logger = new Logger('someChannel');
        $logger->log(100, 'A message with level 100');
        $this->assertEquals(
            [
                'DEBUG' => [[
                        'DEBUG',
                        'LoggerTest:testAddRecordAtLevelDebugStoresMessageInDebug(21) A message with level 100',
                        [],
                        time(),
                        'SomeChannel',
                    ]],
                'OTHER' => [],
            ],
            $logger->getLoggedMessages()
        );
    }
}
