<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception;

use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionAbstract;

class NormalError extends ExceptionAbstract
{
    protected int $errno = -2;
    protected string $error = 'Normal Error';

    public function __construct(int $errno, string $error)
    {
        $this->errno = $errno;
        $this->error = $error;
        parent::__construct();
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrno(): int
    {
        return $this->errno;
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