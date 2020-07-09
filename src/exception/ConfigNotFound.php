<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Exception;

/**
 * Class ConfigNotFound
 * @package SmallSung\Hyperf\Exception
 */
class ConfigNotFound extends RuntimeException
{
    private string $configName;

    public function __construct($configName)
    {
        $this->configName = $configName;
        $message = sprintf('Config:%s must be defined', $configName);
        parent::__construct($message);
    }

    /**
     * @return string
     */
    public function getConfigName(): string
    {
        return $this->configName;
    }

    /**
     * @param string $configName
     */
    public function setConfigName(string $configName): void
    {
        $this->configName = $configName;
    }
}