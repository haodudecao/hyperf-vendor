<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task;

use SmallSung\Hyperf\Task\AbstractInterface\Closure;
use SmallSung\Hyperf\Task\AbstractInterface\TaskHandlerAbstract;

class TaskHandlerClosure extends TaskHandlerAbstract
{
    use Closure;
}