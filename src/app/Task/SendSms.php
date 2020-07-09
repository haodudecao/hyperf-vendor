<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Task;

use SmallSung\Hyperf\Exception\RuntimeException;
use SmallSung\Hyperf\Task\AbstractInterface\TaskHandlerAbstract;

class SendSms  extends TaskHandlerAbstract
{
    protected string $mobileNumber;
    /**
     * @var string
     * $action = SendSms::$action
     */
    protected string $action;
    protected array $params;

    public function __construct(string $action, string $mobileNumber, ...$params)
    {
        $this->mobileNumber = $mobileNumber;
        $this->params = $params;
        if (preg_match('@^[a-z][a-z0-9]+::[a-z][a-z0-9]+$@i', $action)){
            throw new RuntimeException('不应该是\Closure或$this->method，必须Class::method');
        $this->action = $action;
    }

    public function handle(): void
    {
        $sender = $this->container->get(\App\Utility\SendSms::class);
        $sender->{$this->action}($this->mobileNumber, ...$this->params);
    }
}