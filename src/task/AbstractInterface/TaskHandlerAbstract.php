<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task\AbstractInterface;

use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\Task\FinishHandlerClosuer;
use Swoole\Server as SwooleServer;

abstract class TaskHandlerAbstract
{

    protected int $workerId;
    protected int $id;
    protected $flags;
    protected ContainerInterface $container;
    protected SwooleServer $server;

    abstract public function handle() : void ;

    protected final function finish($data)
    {
        if (is_null($data)){
            return;
        }
        if ($data instanceof \Closure){
            $data = new FinishHandlerClosuer($data);
        }
        $this->server->finish($data);
    }

    /**
     * @return int
     */
    public function getWorkerId() : int
    {
        return $this->workerId;
    }

    /**
     * @param int $workerId
     * @return $this
     */
    public function setWorkerId(int $workerId): self
    {
        $this->workerId = $workerId;
        return $this;
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFlags() : int
    {
        return $this->flags;
    }

    /**
     * @param int $flags
     * @return $this
     */
    public function setFlags(int $flags): self
    {
        $this->flags = $flags;
        return $this;
    }


    public function getServer() : SwooleServer
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