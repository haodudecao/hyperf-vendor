<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Bootstrap;

use Psr\EventDispatcher\EventDispatcherInterface;
use SmallSung\Hyperf\Core\Override\Framework\Event\OnRequest;
use Swoole\Http\Request;
use Swoole\Http\Response;

class RequestCallback
{
    protected EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function onRequest(Request $request, Response $response)
    {
        $this->dispatcher->dispatch(new OnRequest($request, $response));
    }
}