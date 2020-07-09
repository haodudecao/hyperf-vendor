<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Server;

use Hyperf\Contract\MiddlewareInitializerInterface;
use Hyperf\Framework\Event\BeforeMainServerStart;
use Hyperf\Framework\Event\BeforeServerStart;
use Hyperf\Server\Server as ParentServer;
use Hyperf\Server\ServerConfig;
use Hyperf\Server\ServerManager;
use RuntimeException;
use Swoole\Server\Port;
use Swoole\Server as SwooleServer;

class Server extends ParentServer
{
    protected function initServers(ServerConfig $config)
    {
        $servers = $this->sortServers($config->getServers());

        foreach ($servers as $server) {
            $name = $server->getName();
            $type = $server->getType();
            $host = $server->getHost();
            $port = $server->getPort();
            $sockType = $server->getSockType();
            $callbacks = $server->getCallbacks();

            if (!$this->server instanceof SwooleServer) {
                $this->server = $this->makeServer($type, $host, $port, $config->getMode(), $sockType);
                $callbacks = array_replace($this->defaultCallbacks(), $config->getCallbacks(), $callbacks);
                $this->registerSwooleEvents($this->server, $callbacks, $name);
                $this->server->set(array_replace($config->getSettings(), $server->getSettings()));
                ServerManager::add($name, [$type, current($this->server->ports)]);

                if (class_exists(BeforeMainServerStart::class)) {
                    // Trigger BeforeMainServerStartListener event, this event only trigger once before main server start.
                    $this->eventDispatcher->dispatch(new BeforeMainServerStart($this->server, $config->toArray()));
                }
            } else {
                /** @var Port $slaveServer */
                $slaveServer = $this->server->addlistener($host, $port, $sockType);
                if (!$slaveServer) {
                    throw new RuntimeException("Failed to listen server port [{$host}:{$port}]");
                }
                $server->getSettings() && $slaveServer->set(array_replace($config->getSettings(), $server->getSettings()));
                $callbacks = array_replace($this->defaultCallbacks(), $config->getCallbacks(), $callbacks);
                $this->registerSwooleEvents($slaveServer, $callbacks, $name);
                ServerManager::add($name, [$type, $slaveServer]);
            }

            // Trigger beforeStart event.
            if (isset($callbacks[SwooleEvent::ON_BEFORE_START])) {
                [$class, $method] = $callbacks[SwooleEvent::ON_BEFORE_START];
                if ($this->container->has($class)) {
                    $this->container->get($class)->{$method}();
                }
            }

            if (class_exists(BeforeServerStart::class)) {
                // Trigger BeforeServerStart event.
                $this->eventDispatcher->dispatch(new BeforeServerStart($name));
            }
        }
    }

    protected function registerSwooleEvents($server, array $events, string $serverName): void
    {
        foreach ($events as $event => $callback) {
            if (! SwooleEvent::isSwooleEvent($event)) {
                continue;
            }
            if ($server instanceof Port && !SwooleEvent::isSwoolePortEvent($event)){
                continue;
            }
            if (is_array($callback)) {
                [$className, $method] = $callback;
                if (array_key_exists($className . $method, $this->onRequestCallbacks)) {
                    $this->logger->warning(sprintf('%s will be replaced by %s. Each server should have its own onRequest callback. Please check your configs.', $this->onRequestCallbacks[$className . $method], $serverName));
                }

                $this->onRequestCallbacks[$className . $method] = $serverName;
                $class = $this->container->get($className);
                if (method_exists($class, 'setServerName')) {
                    // Override the server name.
                    $class->setServerName($serverName);
                }
                if ($class instanceof MiddlewareInitializerInterface) {
                    $class->initCoreMiddleware($serverName);
                }
                $callback = [$class, $method];
            }
            $server->on($event, $callback);
        }
    }
}