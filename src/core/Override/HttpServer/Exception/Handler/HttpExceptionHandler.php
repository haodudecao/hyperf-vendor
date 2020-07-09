<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\HttpServer\Exception\Handler;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler as ParentHandler;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ParentHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->logger->warning($this->formatter->format($throwable));
        return $response->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

}