<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class AlreadyExist extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Already Exist';
    }

    public function getErrno(): int
    {
        return -702;
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