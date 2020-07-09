<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Server;

use Hyperf\Server\ServerFactory as ParentServerFactory;
use Hyperf\Server\ServerInterface;

class ServerFactory extends ParentServerFactory
{
    public function getServer(): ServerInterface
    {
        if (! $this->server instanceof ServerInterface) {
            $this->server = new Server(
                $this->container,
                $this->getLogger(),
                $this->getEventDispatcher()
            );
        }

        return $this->server;
    }
}