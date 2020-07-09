<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task;

use SmallSung\Hyperf\Task\AbstractInterface\Closure;
use SmallSung\Hyperf\Task\AbstractInterface\FinishHandlerAbstract;

class FinishHandlerClosuer extends FinishHandlerAbstract
{
    use Closure;
}