<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\HttpServer;

use SmallSung\Hyperf\Core\Override\HttpServer\Exception\Handler\HttpExceptionHandler;

use Hyperf\HttpServer\Server as ParentServer;

class Server extends ParentServer
{
    /**
     * 将 HttpExceptionHandler 替换为当前命名空间内 HttpExceptionHandler
     */
    protected function getDefaultExceptionHandler(): array
    {
        return [
            HttpExceptionHandler::class
        ];
    }
}