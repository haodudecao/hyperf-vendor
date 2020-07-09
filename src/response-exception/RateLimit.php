<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class RateLimit extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Rate Limit';
    }

    public function getErrno(): int
    {
        return -601;
    }

    public function getLogLevel(): ?string
    {
        return null;
    }

    public function getFormatLog(): string
    {
        return '';
    }
}