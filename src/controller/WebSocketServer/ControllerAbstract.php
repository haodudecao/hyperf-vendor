<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Controller\WebSocketServer;

use Hyperf\Di\Annotation\Inject;
use SmallSung\Hyperf\Controller\ControllerAbstract as ParentControllerAbstract;
use SmallSung\Hyperf\Server\WebSocketServer\Request;

class ControllerAbstract extends ParentControllerAbstract
{
    /**
     * @Inject()
     * @var Request
     */
    protected $request;

    public function __construct()
    {
        parent::__construct();
    }
}