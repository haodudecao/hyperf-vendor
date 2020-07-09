<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class RequestParameterFormatError extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Request parameter format error。Must "Content-Type:application/json"';
    }

    public function getErrno(): int
    {
        return -20;
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