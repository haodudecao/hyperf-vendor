<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task;

use SmallSung\Hyperf\Task\AbstractInterface\FinishHandlerAbstract;

class Package
{
    const ASYNC = 1;
    const SYNC = 0;
    protected $type;
    protected $taskHandler;
    protected $finishHandler;

    public function getType() : int
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTaskHandler()
    {
        return $this->taskHandler;
    }

    public function setTaskHandler($taskCallBack): self
    {
        $this->taskHandler = $taskCallBack;
        return $this;
    }

    public function getFinishHandler() :? FinishHandlerAbstract
    {
        return $this->finishHandler;
    }

    public function setFinishHandler(?FinishHandlerAbstract $finishCallBack): self
    {
        $this->finishHandler = $finishCallBack;
        return $this;
    }
}