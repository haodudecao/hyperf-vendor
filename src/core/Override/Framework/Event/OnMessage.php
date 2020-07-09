<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Event;

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class OnMessage
{
    public Server $server;
    public Frame $frame;

    public function __construct(Server $server, Frame $frame)
    {
        $this->server = $server;
        $this->frame = $frame;
    }
}