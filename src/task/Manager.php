<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task;

use Closure;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Swoole\Server as SwooleServer;

class Manager
{
    /**
     * @var ContainerInterface
     * @Inject
     */
    protected ContainerInterface $container;

    /**
     * @param $task
     * @param callable|null $finishCallback
     * @param int $dstWorkerId
     * @return int
     * @throws Exception
     */
    public function async($task, ?callable $finishCallback = null, int $dstWorkerId = -1) : int
    {
        if ($task instanceof Closure){
            $task = new TaskHandlerClosure($task);
        }

        if ($finishCallback instanceof Closure){
            $finishCallback = new FinishHandlerClosuer($finishCallback);
        }

        $package = new Package();
        $package->setType($package::ASYNC)
            ->setTaskHandler($task)
            ->setFinishHandler($finishCallback);
        $ret = $this->container->get(SwooleServer::class)->task($package, $dstWorkerId);
        if (false === $ret){
            throw new Exception();
        }
        return $ret;
    }
}