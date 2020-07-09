<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Logger\Handler;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;

class ConsoleHandler extends StreamHandler
{
    public function __construct($level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct(STDOUT, $level, $bubble, null, false);
        $this->pushProcessor(new PsrLogMessageProcessor());
    }
}