<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class NetworkBusy extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'NetworkBusy';
    }

    public function getErrno(): int
    {
        return -3;
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