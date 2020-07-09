<?php

declare(strict_types=1);

namespace SmallSung\Hyperf;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'publish'=>$this->publish(),
            'annotations'=>$this->annotations(),
            'dependencies'=>$this->dependencies(),
            'listeners'=>$this->listeners(),
            'logger'=>$this->logger(),
        ];
    }

    private function publish() : array
    {
        return  [
            [
                'id' => 'config',
                'description' => 'The config for smallsung/hyperf.',
                'source' => __DIR__ . '/../publish/project.php',
                'destination' => BASE_PATH . '/config/autoload/project.php',
            ],
        ];
    }

    private function dependencies() : array
    {
        return [
            \Hyperf\Contract\StdoutLoggerInterface::class=>\SmallSung\Hyperf\Logger\LoggerFactory::class,
            \Hyperf\Server\ServerFactory::class=>\SmallSung\Hyperf\Core\Override\Server\ServerFactory::class,
            \Hyperf\HttpServer\Contract\RequestInterface::class=>\SmallSung\Hyperf\Core\Override\HttpServer\Request::class,
            \SmallSung\Hyperf\Server\WebSocketServer\Request::class=>\SmallSung\Hyperf\Servers\WebSocketServer\RequestProxy::class,
            \Hyperf\Snowflake\IdGeneratorInterface::class => \SmallSung\Hyperf\Core\Override\Snowflake\IdGenerator\SnowflakeIdGenerator::class,
            \Hyperf\Snowflake\MetaGeneratorInterface::class => \SmallSung\Hyperf\Core\Override\Snowflake\MetaGeneratorFactory::class,

        ];
    }

    private function listeners() : array
    {
        return [
            \SmallSung\Hyperf\Core\Listener\BeforeMainServerStartListener::class,
            \SmallSung\Hyperf\Core\Listener\OnFinishListener::class,
            \SmallSung\Hyperf\Core\Listener\OnTaskListener::class,
            \SmallSung\Hyperf\Core\Listener\ValidatorFactoryResolvedListener::class,
        ];
    }

    private function logger() : array
    {
        return [
            'default' => [
                //先进后出
                'handlers'=>[
                    0=>[
                        'class' => \SmallSung\Hyperf\Logger\Handler\ConsoleHandler::class,
                        'constructor' => [
                            'level' => \Monolog\Logger::DEBUG,
                            'bubble'=>true,
                        ],
                        'formatter' => [
                            'class' => \Bramus\Monolog\Formatter\ColoredLineFormatter::class,
                            'constructor' => [
                                'colorScheme'=>'',
                                'format' => "[%datetime%] %channel%.%level_name%: %message% %extra%\n",
                                'allowInlineLineBreaks' => true,
                                'ignoreEmptyContextAndExtra'=>true,
                            ],
                        ],
                    ],
                    1=>[
                        'class' => \Monolog\Handler\RotatingFileHandler::class,
                        'constructor' => [
                            'filename' => BASE_PATH . '/runtime/logs/hyperf.log',
                            'level' => \Monolog\Logger::DEBUG,
                            'maxFiles'=>180,
                        ],
                        'formatter' => [
                            'class' => \Monolog\Formatter\LineFormatter::class,
                            'constructor' => [
                                'format' => null,
                                'dateFormat' => null,
                                'allowInlineLineBreaks' => true,
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }

    private function annotations() : array
    {
        return [
            'scan'=>[
                'paths'=>[
                    __DIR__
                ]
            ]
        ];
    }
}