<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Logger;

use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Hyperf\Logger\LoggerFactory as HyperfLoggerFactory;

class LoggerFactory
{
    const DEFAULT_NAME = 'hyperf-ss';

    public function __invoke(ContainerInterface $container, string $name = 'hyperf', string $group = 'default') : LoggerInterface
    {
        return $this->get($name, $group);
    }

    public function get(string $name = self::DEFAULT_NAME, string $group = 'default') : LoggerInterface
    {
        return static::logger($name, $group);
    }

    public static function logger(string $name = self::DEFAULT_NAME, string $group = 'default') : LoggerInterface
    {
        $container = ApplicationContext::getContainer();
        $loggerFactory = $container->get(HyperfLoggerFactory::class);
        return $loggerFactory->get($name, $group);
    }
}