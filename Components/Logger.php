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

namespace FfuenfCommon\Components;

use Monolog\Handler\HandlerInterface;
use Shopware\Components\Logger as BaseLogger;

class Logger extends BaseLogger
{
    /**
     * @var string
     */
    private $channelName;

    /**
     * @var array
     */
    private $messages = ['DEBUG' => [], 'OTHER' => []];

    /**
     * @param string             $name       The logging channel
     * @param HandlerInterface[] $handlers   optional stack of handlers, the first one in the array is called first, etc
     * @param callable[]         $processors Optional array of processors
     */
    public function __construct($name, $handlers = [], $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
        $this->channelName = ucfirst($name);
    }

    /**
     * @return array
     */
    public function getLoggedMessages()
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        $stacktrace = debug_backtrace();
        $message = $stacktrace[1]['class'] . ':' . $stacktrace[1]['function'] . '(' . $stacktrace[0]['line'].') ' . $message;
        $this->storeMessage((int) $level, $message, $context);
        parent::log($level, $message, $context);
    }

    /**
     * @param int    $level
     * @param string $message
     * @param array  $context
     */
    private function storeMessage($level, $message, array $context = [])
    {
        $this->messages[$level > 100 ? 'OTHER' : 'DEBUG'][] =
            [
                self::getLevelName($level),
                $message,
                $context,
                time(),
                $this->channelName,
            ];
    }
}
