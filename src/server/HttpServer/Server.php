<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\HttpServer;

use Hyperf\HttpServer\Contract\CoreMiddlewareInterface;
use SmallSung\Hyperf\Core\Override\HttpServer\Server as ParentServer;

abstract class Server extends ParentServer
{
    /**
     * 将 CoreMiddleware 替换为当前命名空间内 CoreMiddleware
     * @return CoreMiddlewareInterface
     */
    protected function createCoreMiddleware(): CoreMiddlewareInterface
    {
        return make(CoreMiddleware::class, [$this->container, $this->serverName]);
    }
}