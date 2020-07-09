<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class Unauthorized extends ExceptionAbstract
{
    public function getHttpStatusCode(): int
    {
        return 401;
    }

    public function getError(): string
    {
        return 'unauthorized';
    }

    public function getErrno(): int
    {
        return  -401;
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