<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Logger\Handler;

use Monolog\Handler\StreamHandler as ParentStreamHandler;
use Monolog\Logger;

class StreamHandler extends ParentStreamHandler
{
    /**
     * Minimum level for logs that are passed to handler
     *
     * @var int[]
     */
    protected array $acceptedLevels;

    public function __construct($stream, $level = [Logger::DEBUG, Logger::EMERGENCY], $bubble = true, array $processors = [])
    {
        parent::__construct($stream, $level, $bubble, null, false);
        $this->setAcceptedLevels(...$level);
        foreach ($processors as $processor){
            $this->pushProcessor($processor);
        }
    }

    /**
     * @param int|string|array $minLevelOrList A list of levels to accept or a minimum level or level name if maxLevel is provided
     * @param int|string       $maxLevel       Maximum level or level name to accept, only used if $minLevelOrList is not an array
     */
    public function setAcceptedLevels($minLevelOrList = Logger::DEBUG, $maxLevel = Logger::EMERGENCY)
    {
        if (is_array($minLevelOrList)) {
            $acceptedLevels = array_map('\Monolog\Logger::toMonologLevel', $minLevelOrList);
        } else {
            $minLevelOrList = Logger::toMonologLevel($minLevelOrList);
            $maxLevel = Logger::toMonologLevel($maxLevel);
            $acceptedLevels = array_values(array_filter(Logger::getLevels(), function ($level) use ($minLevelOrList, $maxLevel) {
                return $level >= $minLevelOrList && $level <= $maxLevel;
            }));
        }
        $this->acceptedLevels = array_flip($acceptedLevels);
    }

    public function isHandling(array $record)
    {
        return isset($this->acceptedLevels[$record['level']]);
    }
}