<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task\AbstractInterface;

use Psr\Container\ContainerInterface;
use Swoole\Server as SwooleServer;

abstract class FinishHandlerAbstract
{
    protected $taskId;
    protected SwooleServer $server;
    protected ContainerInterface $container;

    abstract public function handle() :void ;

    public function getTaskId()
    {
        return $this->taskId;
    }

    public function setTaskId($taskId): self
    {
        $this->taskId = $taskId;
        return $this;
    }

    /**
     * @return SwooleServer
     */
    public function getServer(): SwooleServer
    {
        return $this->server;
    }

    public function setServer(SwooleServer $server): self
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function setContainer(ContainerInterface $container): self
    {
        $this->container = $container;
        return $this;
    }

}