<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Push\Exception;

use Psr\Log\LogLevel;
use SmallSung\Hyperf\Push\Exception\AbstractInterface\ExceptionAbstract;
use function json_encode;

class DataNotAllowed extends ExceptionAbstract
{
    protected string $params;
    protected int $fd = -1;

    public function __construct(string $params, int $fd = -1)
    {
        $this->params = $params;
        $this->fd = $fd;
        parent::__construct();
    }

    /**
     * @param int $fd
     * @return $this
     */
    public function setFd(int $fd): self
    {
        $this->fd = $fd;
        return $this;
    }

    public function getError(): string
    {
        return sprintf("Request format error。\r\nContent-Type:application/json\r\n%s", $this->getFormatLog());
    }

    public function getErrno(): int
    {
        return -20;
    }

    public function getLogLevel(): ?string
    {
        return LogLevel::NOTICE;
    }

    public function getFormatLog(): string
    {
        return sprintf('WebSocket:fd[%s]发送错误数据格式：%s', $this->fd, $this->params);
    }
    private function getFormat() : string
    {
        return json_encode([
            'id'=>'/^[0-9a-z]{13,}$/i',
            'jsonrpc'=>'2.0',
            'method'=>' not empty string',
            'params'=>[]
        ], JSON_UNESCAPED_UNICODE);
    }
}