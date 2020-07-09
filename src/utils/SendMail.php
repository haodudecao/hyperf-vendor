<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Utils;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use SmallSung\Hyperf\Exception\ConfigNotFound;
use SmallSung\Hyperf\Logger\LoggerFactory;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class SendMail
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $mailer;

    private $username;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $this->container->get(LoggerFactory::class)->get();

        $config = $this->container->get(ConfigInterface::class);
        $host = $config->get('project.Mailer.host');
        $port = $config->get('project.Mailer.port');
        $username = $config->get('project.Mailer.username');
        $password = $config->get('project.Mailer.password');

        if (!is_string($host) || empty($host)){
            throw new ConfigNotFound('project.Mailer.host');
        }
        if (!is_int($port) || $port <= 0){
            throw new ConfigNotFound('project.Mailer.port');
        }
        if (!is_string($username) || empty($username)){
            throw new ConfigNotFound('project.Mailer.username');
        }
        if (!is_string($password) || empty($password)){
            throw new ConfigNotFound('project.Mailer.password');
        }


        $transport = new Swift_SmtpTransport();
        $transport->setHost($host)
            ->setPort($port)
            ->setUsername($username)
            ->setPassword($password);

        $this->mailer = new Swift_Mailer($transport);
        $this->username = $username;
    }

    public function send(string $to, string $subject, string $body) : bool
    {
        $message = (new Swift_Message($subject))
            ->setFrom($this->username)
            ->setTo($to)
            ->setBody($body);
        $ret = $this->mailer->send($message);
        return $ret > 0;
    }

    public function sendMulti(string $subject, array $to, string $body)
    {
        $message = (new Swift_Message($subject))
            ->setFrom([$this->username])
            ->setTo($to)
            ->setTo($body);
        $this->mailer->send($message);
    }
}