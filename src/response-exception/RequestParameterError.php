<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class RequestParameterError extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Request Parameter Error';
    }

    public function getErrno(): int
    {
        return -21;
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