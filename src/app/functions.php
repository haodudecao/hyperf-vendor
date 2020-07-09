<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App;

use Hyperf\Utils\ApplicationContext;
use SmallSung\Hyperf\Task\Manager;

/**
 * 异步发送邮件
 * @param string $subject
 * @param string $body
 * @param string $to
 * @return int
 */
function sendEmail(string $subject, string $body, string $to) : int
{
    $taskManager = ApplicationContext::getContainer()->get(Manager::class);
    $task = new \SmallSung\Hyperf\App\Task\SendMail($to, $subject, $body);
    return $taskManager->async($task);
}


/**
 * 异步发送短信
 * @param string $mobileNumber
 * @param array $params
 * @param string $action
 * @return int
 */
function sendSms(string $action, string $mobileNumber, ...$params) : int
{
    $taskManager = ApplicationContext::getContainer()->get(Manager::class);
    $task = new \SmallSung\Hyperf\App\Task\SendSms($action, $mobileNumber, ...$params);
    return $taskManager->async($task);
}