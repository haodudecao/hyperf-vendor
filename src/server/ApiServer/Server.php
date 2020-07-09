<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\ApiServer;

use Hyperf\Contract\Sendable;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\HttpServer\Contract\CoreMiddlewareInterface;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use SmallSung\Hyperf\Core\Override\HttpServer\Server as ParentServer;
use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Throwable;

abstract class Server extends ParentServer
{
    public function onRequest(SwooleRequest $request, SwooleResponse $response): void
    {
        try {
            /**
             * @var RequestInterface $psr7Request
             * @var ResponseInterface $psr7Response
             */
            [$psr7Request, $psr7Response] = $this->initRequestAndResponse($request, $response);

            //国际化
            $this->container->get(TranslatorInterface::class)
                ->setLocale(explode(',', explode(";", $psr7Request->getHeaderLine('Accept-Language'), 2)[0],2)[0]);

            $psr7Request = $this->coreMiddleware->dispatch($psr7Request);
            /** @var Dispatched $dispatched */
            $dispatched = $psr7Request->getAttribute(Dispatched::class);
            $middlewares = $this->middlewares;
            if ($dispatched->isFound()) {
                $registedMiddlewares = MiddlewareManager::get($this->serverName, $dispatched->handler->route, $psr7Request->getMethod());
                $middlewares = array_merge($middlewares, $registedMiddlewares);
            }

            $psr7Response = $this->dispatcher->dispatch($psr7Request, $middlewares, $this->coreMiddleware);
        } catch (Throwable $throwable) {
            // Delegate the exception to exception handler.
            $psr7Response = $this->exceptionHandlerDispatcher->dispatch($throwable, $this->exceptionHandlers);
        } finally {
            // Send the Response to client.
            if (! isset($psr7Response) || ! $psr7Response instanceof Sendable) {
                return;
            }
            $psr7Response->send();
        }
    }

    /**
     * 将 CoreMiddleware 替换为当前命名空间内 CoreMiddleware
     * @return CoreMiddlewareInterface
     */
    protected function createCoreMiddleware(): CoreMiddlewareInterface
    {
        return make(CoreMiddleware::class, [$this->container, $this->serverName]);
    }
}