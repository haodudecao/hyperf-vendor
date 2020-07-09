<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Bootstrap;

use Psr\EventDispatcher\EventDispatcherInterface;
use SmallSung\Hyperf\Core\Override\Framework\Event\OnOpen;
use Swoole\Http\Request;
use Swoole\WebSocket\Server;

class OpenCallBack
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function OnOpen(Server $server, Request $request)
    {
        $this->dispatcher->dispatch(new OnOpen($server, $request));
    }
}