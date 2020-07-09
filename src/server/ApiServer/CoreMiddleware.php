<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\ApiServer;

use Closure;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SmallSung\Hyperf\Logger\LoggerFactory;
use SmallSung\Hyperf\Server\HttpServer\CoreMiddleware as ParentCoreMiddleware;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Contracts\Jsonable;
use SmallSung\Hyperf\Response\Exception\AbstractInterface\ExceptionInterface;

class CoreMiddleware extends ParentCoreMiddleware
{
    protected function handleNotFound(ServerRequestInterface $request)
    {
        return $this->response()->withStatus(404)->withBody(new  SwooleStream('Not Found'));
    }

    protected function handleFound(Dispatched $dispatched, ServerRequestInterface $request)
    {
        if ($dispatched->handler->callback instanceof Closure) {
            $response = call($dispatched->handler->callback);
        } else {
            [$controller, $action] = $this->prepareHandler($dispatched->handler->callback);
            $controllerInstance = $this->container->get($controller);
            if (! method_exists($controller, $action)) {
                // Route found, but the handler does not exist.
                return $this->handleNotFound($request);
            }
            try {
                $parameters = $this->parseParameters($controller, $action, $dispatched->params);
                $response = $controllerInstance->{$action}(...$parameters);
            }catch (ExceptionInterface $exception){
                $response = $this->response()->withStatus($exception->getHttpStatusCode())
                    ->withAddedHeader('content-type', 'application/json;charset=utf-8')
                    ->withBody(new SwooleStream(
                        ResponseFormatter::create()->setError($exception->toError())->format()
                    ));
                $logLevel = $exception->getLogLevel();
                if (!is_null($logLevel)){
                    $formatLog = $exception->getFormatLog();
                    $this->container->get(LoggerFactory::class)->get()->{$logLevel}($formatLog);
                }
            }
        }
        return $response;
    }

    protected function transferToResponse($response, ServerRequestInterface $request): ResponseInterface
    {
        if (is_string($response) || is_object($response) || is_array($response) || is_float($response)  || is_int($response) || is_null($response) || is_bool($response)

            #todo
            || $response instanceof Arrayable
            || $response instanceof Jsonable
        ){
            return $this->response()
                ->withAddedHeader('content-type', 'application/json;charset=utf-8')
                ->withBody(new SwooleStream(
                    ResponseFormatter::create()->setResult($response)->format()
                ));
        }
        return $this->response()->withAddedHeader('content-type', 'text/plain')->withBody(new SwooleStream((string) $response));
    }
}