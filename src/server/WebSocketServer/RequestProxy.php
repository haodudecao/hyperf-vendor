<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\WebSocketServer;

use Hyperf\Utils\Context;

class RequestProxy
{
    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return Context::get(Request::class);
    }

    public function __call($name, $arguments)
    {
        $request = $this->getRequest();

        $substr = substr($name, 0, 3);
        if ($substr === 'get'){
            return $request->$name(...$arguments);
        }elseif ($substr === 'set'){
            Context::set(Request::class, $request->$name(...$arguments));
            return $this;
        }else{
            return $request->$name(...$arguments);
        }
    }
}