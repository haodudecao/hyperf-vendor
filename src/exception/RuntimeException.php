<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Exception;

/**
 * Class RunTimeException
 * @package SmallSung\Hyperf\Exception
 */
class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    public function __construct($message = "")
    {
        parent::__construct($message);
    }
}