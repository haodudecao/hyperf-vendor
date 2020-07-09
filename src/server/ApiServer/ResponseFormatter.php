<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\ApiServer;

use function json_encode;

class ResponseFormatter
{
    private $result = null;
    private $error = null;

    /**
     * @return static
     */
    static public function create() : self
    {
        return new static();
    }

    /**
     * @param $result
     * @return $this
     */
    public function setResult($result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param $error
     * @return $this
     */
    public function setError($error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return json_encode([
            'error'=>$this->error,
            'result'=>$this->result,
        ], JSON_UNESCAPED_UNICODE);
    }
}