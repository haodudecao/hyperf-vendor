<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Event;

use Swoole\Http\Request;
use Swoole\WebSocket\Server;

class OnOpen
{
    public Server $server;
    public Request $request;

    public function __construct(Server $server, Request $request)
    {
        $this->$server = $server;
        $this->request = $request;
    }
}