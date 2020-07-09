<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\WebSocketServer;

use Closure;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Utils\Context;
use Psr\Container\ContainerInterface;
use ReflectionException;
use ReflectionMethod;
use SmallSung\Hyperf\Logger\LoggerFactory;
use SmallSung\Hyperf\Push\Exception\AbstractInterface\ExceptionInterface;
use SmallSung\Hyperf\Push\Exception\ApiNotFound;
use Swoole\Server;
use Swoole\Websocket\Frame;
use Swoole\WebSocket\Server as WebSocketServer;

class Dispatcher implements OnOpenInterface, OnCloseInterface, OnMessageInterface
{
    /**
     * @Inject()
     */
    protected ContainerInterface $container;

    /**
     * @Inject()
     */
    protected LoggerFactory $loggerFactory;

    protected string $serverName = 'websocket';

    public function onOpen(WebSocketServer $server, \Swoole\Http\Request $request): void
    {
        $this->loggerFactory->get()->debug('WebSocket::onOpen-{fd}', [
            'fd'=>$request->fd
        ]);
    }

    public function onClose(Server $server, int $fd, int $reactorId): void
    {
        $this->loggerFactory->get()->debug('WebSocket::onClose-{fd}-{reactorId}', [
            'fd'=>$fd,
            'reactorId'=>$reactorId,
        ]);
    }

    public function onMessage(WebSocketServer $server, Frame $frame): void
    {
        $this->loggerFactory->get()->debug('WebSocket::onMessage-{fd}-{data}', [
            'fd'=>$frame->fd,
            'data'=>strval($frame->data),
        ]);

        if ($frame->opcode !== SWOOLE_WEBSOCKET_OPCODE_TEXT){
            $this->loggerFactory->get()->warning(sprintf('错误的Opcode。Fd:%s\tOpcode:%s', $frame->fd, $frame->opcode));
            return;
        }
        if ($frame->data === 'PING++'){
            $server->push($frame->fd, 'PONG++');
            return;
        }
        if ($frame->data === 'PONG++'){
            return;
        }

        $request = Request::create();
        $response = null;
        try {
            $request->setFd($frame->fd);
            $request->setPushFd($frame->fd);
            $request->setFrameOpcode($frame->opcode);
            $request->setFrameData($frame->data);

            $request->setJsonRpcFromJson($frame->data);
            $route = $this->createDispatcher()->dispatch('SEND', $request->getJsonRpc()->getMethod());
            if (\FastRoute\Dispatcher::FOUND !== $route[0]){
                throw new ApiNotFound($request->getFrameData(), $request->getFd());
            }
            $request->setHandler($route[1]->callback);
            Context::set(Request::class, $request);
            $handler = $request->getHandler();
            if ($handler instanceof Closure) {
                $response = call($request->getHandler());
            }else {
                [$controller, $action] = $handler;
                if (!$this->container->has($controller)){
                    throw new ApiNotFound($request->getFrameData(), $request->getFd());
                }
                if (!method_exists($controller, $action)) {
                    throw new ApiNotFound($request->getFrameData(), $request->getFd());
                }
                try {
                    $reflectionMethod = new ReflectionMethod($controller, $action);
                } catch (ReflectionException $reflectionException) {
                    $response = $request->response()->setException(new ApiNotFound($request->getFrameData(), $request->getFd()));
                    throw $reflectionException;
                }
                if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic()){
                    throw new ApiNotFound($request->getFrameData(), $request->getFd());
                }

                $controllerInstance = $this->container->get($controller);
                $response = $controllerInstance->{$action}();
            }

            if (is_subclass_of($response, Request::class)){
                /** @var Request $response */
                $response = $response->response();
            }else{
                $response = $request->response()->getJsonRpc()->setResult($response);
            }

        } catch (ExceptionInterface $exception){
            is_null($response) and $response = $request->response();
            $response->setException($exception);
        } finally {
            if (!is_null($exception = $response->getException())){
                if (!is_null($logLevel = $response->getException()->getLogLevel())){
                    $formatLog = $exception->getFormatLog();
                    $this->loggerFactory->get()->{$logLevel}($formatLog);
                }
            }
            if ($response->isFinish()){
                return;
            }
            $clientInfo = $server->getClientInfo($response->getPushFd());
            if (empty($clientInfo)){
                return;
            }
            if ($clientInfo['websocket_status'] !== SWOOLE_WEBSOCKET_STATUS_ACTIVE){
                return;
            }
            $server->push($response->getPushFd(), $response->getJsonRpc()->formatResponse());
        }

    }

    protected function createDispatcher(): \FastRoute\Dispatcher
    {
        $factory = $this->container->get(DispatcherFactory::class);
        return $factory->getDispatcher($this->serverName);
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }

    /**
     * @param string $serverName
     */
    public function setServerName(string $serverName): void
    {
        $this->serverName = $serverName;
    }
}