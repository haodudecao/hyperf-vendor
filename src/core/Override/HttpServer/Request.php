<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\HttpServer;

use Hyperf\HttpServer\Request as ParentRequest;

class Request extends ParentRequest
{
    public function getSwooleRequest(): \Swoole\Http\Request
    {
        return $this->call(__FUNCTION__, func_get_args());
    }
}