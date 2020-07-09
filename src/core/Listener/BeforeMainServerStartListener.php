<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Listener;

use Hyperf\Framework\Event\BeforeMainServerStart;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use SmallSung\Hyperf\Logger\LoggerFactory;

class BeforeMainServerStartListener implements ListenerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            BeforeMainServerStart::class,
        ];
    }

    public function process(object $event)
    {
        $logger = $this->container->get(LoggerFactory::class)->get();
        $taskEnableCoroutine = $event->serverConfig['settings']['task_enable_coroutine'] ?? null;
        if (is_null($taskEnableCoroutine)){
            $logger->error('必须显示配置 task_enable_coroutine = false');die;
        }
        if ($taskEnableCoroutine){
            $logger->error('必须配置 task_enable_coroutine = false');die;
        }
    }
}
