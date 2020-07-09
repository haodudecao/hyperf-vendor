<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\HttpServer;

use Closure;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Router\Dispatched;
use Psr\Http\Message\ServerRequestInterface;
use Hyperf\HttpServer\CoreMiddleware as ParentCoreMiddleware;

class CoreMiddleware extends ParentCoreMiddleware
{
    protected function handleNotFound(ServerRequestInterface $request)
    {
        $html = <<<HTML
<!doctype html><html lang="zh"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><title>404 Not Found</title><style>html {font-size: 10px;font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans", "wenquanyi micro hei", "Hiragino Sans GB", "Hiragino Sans GB W3", FontAwesome, sans-serif;}hr {display: block;padding: 0;border: 0;height: 0;border-top: 1px solid #eee;-webkit-box-sizing: content-box;box-sizing: content-box;}.page-404 {background: #fff;border: none;width: 200px;margin: 0 auto;display: block;padding: 1rem;font-size: 1.3rem;line-height: 1.6;word-break: break-all;word-wrap: break-word;color: #555;border-radius: 0;font-family: Monaco, Menlo, Consolas, "Courier New", FontAwesome, monospace;}</style></head><body><h2 style="text-align: center;font-size: 300%;">哇靠!页面丢了?</h2><p style="text-align: center;font-size: 150%;">看来又有程序猿要被开除了</p>
<pre class="page-404">          .----.
       _.'__    `.
   .--($)($$)---/#\
 .' @          /###\
 :         ,   #####
  `-..__.-' _.-\###/
        `;_:    `"'
      .'"""""`.
     /,  卧槽  \\\\
    //   无情！ \\\\
    `-._______.-'
    ___`. | .'___
   (______|______)
</pre></body></html>
HTML;
        return $this->response()->withStatus(404)->withBody(new  SwooleStream($html));
    }

    /**
     * 405=>404，不暴露类及类成员方法
     * @param array $methods
     * @param ServerRequestInterface $request
     * @return array|\Hyperf\Utils\Contracts\Arrayable|mixed|\Psr\Http\Message\ResponseInterface|string
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request)
    {
        return $this->handleNotFound($request);
    }

    /**
     * 500=>404，不暴露类及类成员方法
     * @param Dispatched $dispatched
     * @param ServerRequestInterface $request
     * @return array|\Hyperf\Utils\Contracts\Arrayable|mixed|\Psr\Http\Message\ResponseInterface|string|null
     */
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
            $parameters = $this->parseParameters($controller, $action, $dispatched->params);
            $response = $controllerInstance->{$action}(...$parameters);
        }
        return $response;
    }
}