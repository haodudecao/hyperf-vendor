<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Task;

use SmallSung\Hyperf\Task\AbstractInterface\TaskHandlerAbstract;

class SendMail extends TaskHandlerAbstract
{
    private $to;
    private $subject;
    private $body;

    public function __construct(string $to, string $subject, string $body)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        $mailer = $this->container->get(\SmallSung\Hyperf\Utils\SendMail::class);
        $mailer->send($this->to, $this->subject, $this->body);
    }
}