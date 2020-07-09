<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer;

use Psr\EventDispatcher\EventDispatcherInterface;
use SmallSung\Hyperf\Controller\ApiServer\ControllerAbstract as ParentControllerAbstract;

abstract class ControllerAbstract extends ParentControllerAbstract
{
    abstract protected function hashPassword(string $str) : string ;

    protected function emit(object $event)
    {
        return $this->container->get(EventDispatcherInterface::class)->dispatch($event);
    }
}