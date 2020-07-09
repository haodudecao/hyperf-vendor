<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Response\Exception\AbstractInterface;

use Throwable;

interface ExceptionInterface extends Throwable
{
    /**
     * @return string
     */
    public function getError() : string ;

    /**
     * @return int
     */
    public function getErrno() : int ;

    /**
     * @return int
     */
    public function getHttpStatusCode(): int ;

    /**
     * @return string|null
     */
    public function getLogLevel() : ?string ;

    /**
     * @return string
     */
    public function getFormatLog() : string ;

    /**
     * @return array
     */
    public function toError() : array ;
}