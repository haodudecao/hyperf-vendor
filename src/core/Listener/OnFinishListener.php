<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\OnFinish;
use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\Core\Component\Task\AbstractInterface\FinishHandlerAbstract;

class OnFinishListener implements ListenerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            OnFinish::class,
        ];
    }

    public function process(object $event)
    {
        /** @var OnFinish $event*/
        if ($event->data instanceof FinishHandlerAbstract){
            /** @var FinishHandlerAbstract $finishData*/
            $finishData = $event->data;
            $finishData->setContainer($this->container)
                ->setServer($event->server)
                ->setTaskId($event->taskId)
                ->handle();
        }
    }
}