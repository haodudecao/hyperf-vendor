<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\WebSocketServer;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\CoreMiddlewareInterface;
use Hyperf\WebSocketServer\Collector\FdCollector;
use Hyperf\WebSocketServer\CoreMiddleware;
use Hyperf\WebSocketServer\Exception\Handler\WebSocketExceptionHandler;
use Hyperf\WebSocketServer\Server as ParentServer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

abstract class Server extends ParentServer
{
    public function onMessage(WebSocketServer $server, Frame $frame): void
    {
        $fdObj = FdCollector::get($frame->fd);
        if (! $fdObj) {
            $this->logger->warning(sprintf('WebSocket: fd[%d] does not exist.', $frame->fd));
            return;
        }

        $instance = $this->container->get($fdObj->class);

        if (! $instance instanceof Dispatcher) {
            $this->logger->warning("{$instance} is not instanceof " . Dispatcher::class);
            return;
        }
        /** @var Dispatcher $instance */
        $instance->setServerName($this->serverName);
        $instance->onMessage($server, $frame);
    }

    public function initCoreMiddleware(string $serverName): void
    {
        $this->serverName = $serverName;
        $this->coreMiddleware = $this->createCoreMiddleware();
        $config = $this->container->get(ConfigInterface::class);
        $this->middlewares = $config->get('middlewares.' . $serverName, []);
        $this->exceptionHandlers = $config->get('exceptions.handler.' . $serverName, [
            WebSocketExceptionHandler::class,
        ]);
    }

    /**
     * 将 CoreMiddleware 替换为当前命名空间内 CoreMiddleware
     */
    protected function createCoreMiddleware(): CoreMiddlewareInterface
    {
        return make(CoreMiddleware::class, [$this->container, $this->serverName]);
    }
}