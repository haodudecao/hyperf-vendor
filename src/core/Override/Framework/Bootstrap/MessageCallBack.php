<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Bootstrap;

use Psr\EventDispatcher\EventDispatcherInterface;
use SmallSung\Hyperf\Core\Override\Framework\Event\OnMessage;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class MessageCallBack
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function onMessage(Server $server, Frame $frame)
    {
        $this->dispatcher->dispatch(new OnMessage($server, $frame));
    }
}