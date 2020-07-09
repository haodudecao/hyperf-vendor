<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Framework\Event;

use Swoole\Http\Request;
use Swoole\Http\Response;

class OnRequest
{
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}