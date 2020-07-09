<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Controller\HttpServer;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use SmallSung\Hyperf\Controller\ControllerAbstract as ParentControllerAbstract;

abstract class ControllerAbstract extends ParentControllerAbstract
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    public function __construct()
    {
        parent::__construct();
    }
}