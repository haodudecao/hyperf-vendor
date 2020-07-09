<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Server;

class SwooleEvent extends \Hyperf\Server\SwooleEvent
{
    const ON_WORKER_ERROR = 'workerError';

    public static function isSwoolePortEvent(string $event) : bool
    {
        if (in_array($event, [
            static::ON_BEFORE_START,
            static::ON_START,
            static::ON_SHUTDOWN,
            static::ON_WORKER_START,
            static::ON_WORKER_STOP,
            static::ON_WORKER_EXIT,
            static::ON_WORKER_ERROR,
            static::ON_TASK,
            static::ON_FINISH,
            static::ON_PIPE_MESSAGE,
            static::ON_MANAGER_START,
            static::ON_MANAGER_STOP,
        ])) {
            return false;
        }
        return true;
    }
}