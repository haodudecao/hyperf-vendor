<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class VerificationCodeError extends ExceptionAbstract
{
    public function getError(): string
    {
        return 'Verification Code Error';
    }

    public function getErrno(): int
    {
        return -451;
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