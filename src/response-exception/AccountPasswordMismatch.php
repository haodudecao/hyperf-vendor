<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class AccountPasswordMismatch extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Account Password Mismatch';
    }

    public function getErrno(): int
    {
        return -402;
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