<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Override\Snowflake\MetaGenerator;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Snowflake\MetaGenerator\RandomMilliSecondMetaGenerator as ParentMetaGenerator;
use Hyperf\Utils\ApplicationContext;
use SmallSung\Hyperf\Exception\ConfigNotFound;
use Swoole\Server as SwooleServer;

class RandomMilliSecondMetaGenerator extends ParentMetaGenerator
{
    protected ?int $workerId = null;

    protected ?int $dataCenterId = null;

    public function getDataCenterId(): int
    {
        if (!is_int($this->dataCenterId)){
            $this->dataCenterId = ApplicationContext::getContainer()->get(ConfigInterface::class)->get('project.SnowFlake.dataCenterId');
            if (!is_int($this->dataCenterId)){
                throw new ConfigNotFound('project.SnowFlake.dataCenterId');
            }
        }
        return $this->dataCenterId;
    }

    public function getWorkerId(): int
    {
        if (!is_int($this->workerId)){
            $this->workerId = ApplicationContext::getContainer()->get(SwooleServer::class)->worker_id % 32;
        }
        return $this->workerId;
    }
}