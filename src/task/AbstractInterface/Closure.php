<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Task\AbstractInterface;

use Hyperf\Utils\ApplicationContext;
use SmallSung\Hyperf\Logger\LoggerFactory;

trait Closure
{
    protected $closureSerialize;

    public function handle(): void
    {
        $args = func_get_args();
        $closure = \Opis\Closure\unserialize($this->closureSerialize);
        $ret = call_user_func($closure, ...$args);

        if (!is_null($ret)){
            $logger = ApplicationContext::getContainer()->get(LoggerFactory::class)->get();
            $logger->warning('Closure 不应该有返回值');
        }
    }

    public function __construct(\Closure $closure)
    {
        $this->closureSerialize = \Opis\Closure\serialize($closure);
    }


//    final public function __invoke()
//    {
//        $args = func_get_args();
//        return call_user_func($this->closure, ...$args);
//    }
//
//    final function call(...$args)
//    {
//        return call_user_func($this->closure, ...$args);
//    }
}