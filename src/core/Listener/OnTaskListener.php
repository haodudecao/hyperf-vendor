<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\OnTask;
use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\Task\AbstractInterface\TaskHandlerAbstract;
use SmallSung\Hyperf\Task\FinishHandlerClosuer;
use SmallSung\Hyperf\Task\Package;
use Swoole\Server\Task;

class OnTaskListener implements ListenerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            OnTask::class,
        ];
    }

    public function process(object $event)
    {
        /** @var OnTask $event */
        /** @var Task $task */
        $task = $event->task;
        if (!($task->data instanceof Package)){
            return $event;
        }
        /** @var Package $package */
        $package = $task->data;
        $taskHandle = $package->getTaskHandler();
        if ($taskHandle instanceof TaskHandlerAbstract){
            /** @var TaskHandlerAbstract $taskHandle */
            $taskHandle->setServer($event->server)
                ->setContainer($this->container)
                ->setFlags($task->flags)
                ->setId($task->id)
                ->setWorkerId($task->worker_id)
                ->handle();
            $finishHandler = $package->getFinishHandler();
            if ($finishHandler instanceof FinishHandlerClosuer){
                /** @var FinishHandlerClosuer $finishHandler */
                #todo bug
                #$task->finish($finishHandler);
                $event->server->finish($finishHandler);
            }

        }
        return null;
    }
}