<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Push\Exception;

use Psr\Log\LogLevel;
use SmallSung\Hyperf\Push\Exception\AbstractInterface\ExceptionAbstract;

class ApiNotFound extends ExceptionAbstract
{
    protected string $params;
    protected int $fd;

    public function __construct(string $params, int $fd)
    {
        $this->params = $params;
        $this->fd = $fd;
        parent::__construct();
    }

    public function getError(): string
    {
        return 'API Not Found';
    }

    public function getErrno(): int
    {
        return -404;
    }

    public function getLogLevel(): ?string
    {
        return LogLevel::NOTICE;
    }

    public function getFormatLog(): string
    {
        return sprintf('WebSocket:fd[%s]ApiNotFound：%s', $this->fd, $this->params);
    }

    /**
     * @return string
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }
}