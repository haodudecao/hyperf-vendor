<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Push\Exception;

use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LogLevel;
use SmallSung\Hyperf\Push\Exception\AbstractInterface\ExceptionAbstract;

class Unknown extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'unknown error';
    }

    public function getErrno(): int
    {
        return -1;
    }

    public function getLogLevel(): ?string
    {
        return LogLevel::ERROR;
    }

    public function getFormatLog(): string
    {
        $formatter = ApplicationContext::getContainer()->get(FormatterInterface::class);
        return $formatter->format($this);
    }
}