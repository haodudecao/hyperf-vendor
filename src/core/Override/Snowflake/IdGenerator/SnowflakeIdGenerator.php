<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Snowflake\IdGenerator;

use Hyperf\Snowflake\IdGenerator\SnowflakeIdGenerator as ParentSnowflakeIdGenerator;
use Hyperf\Snowflake\Meta;

class SnowflakeIdGenerator extends ParentSnowflakeIdGenerator
{
    public function generate(?Meta $meta = null): int
    {
        $meta = $this->meta($meta);

        $interval = $meta->getTimestamp() << $this->config->getTimestampLeftShift();
        $dataCenterId = $meta->getDataCenterId() << $this->config->getDataCenterIdShift();
        $workerId = $meta->getWorkerId() << $this->config->getWorkerIdShift();

        return $interval | $dataCenterId | $workerId | $meta->getSequence();
    }
}