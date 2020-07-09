<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SmallSung\Hyperf\Logger\LoggerFactory;

abstract class ControllerAbstract
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    protected LoggerFactory $loggerFactory;
    protected LoggerInterface $logger;

    public function __construct()
    {
        $container = ApplicationContext::getContainer();
        $this->loggerFactory = $container->get(LoggerFactory::class);
        $this->logger = $this->loggerFactory->get('app');
    }
}