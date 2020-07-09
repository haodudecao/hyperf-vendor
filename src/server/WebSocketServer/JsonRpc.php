<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\WebSocketServer;

use SmallSung\Hyperf\Push\Exception\DataNotAllowed;
use function json_decode;
use function json_encode;

class JsonRpc
{

    private $id = '';
    private $jsonrpc = '2.0';
    private $method = '';
    private $params = [];

    private $result = null;
    private $error = null;

    public function __construct()
    {

    }

    /**
     * @return static
     */
    static public function create() : self
    {
        return new static();
    }

    /**
     * @param string $json
     * @return $this
     * @throws DataNotAllowed
     */
    public function loadJson(string $json) : self
    {
        $jsonArray = $this->parseJson($json);
        $this->id = $jsonArray['id'];
        $this->method = $jsonArray['method'];
        $this->params = $jsonArray['params'];

        return $this;
    }

    /**
     * @param string $json
     * @return array
     * @throws DataNotAllowed
     */
    private function parseJson(string $json) : array
    {
        $jsonArray = json_decode($json, true);
        if (!is_array($jsonArray)  || count($jsonArray) !== 4){
            throw new DataNotAllowed($json);
        }
        if (!isset($jsonArray['id']) || !is_string($jsonArray['id']) || !preg_match('@^[a-z0-9]{13,}$@i', $jsonArray['id'])){
            throw new DataNotAllowed($json);
        }
        if (!isset($jsonArray['jsonrpc']) || !is_string($jsonArray['jsonrpc']) || '2.0' !== $jsonArray['jsonrpc']){
            throw new DataNotAllowed($json);
        }
        if (!isset($jsonArray['method']) || !is_string($jsonArray['method']) || empty($jsonArray['method'])){
            throw new DataNotAllowed($json);
        }
        if (!isset($jsonArray['params']) || !is_array($jsonArray['params'])){
            throw new DataNotAllowed($json);
        }
        return $jsonArray;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getJsonrpc(): string
    {
        return $this->jsonrpc;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
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
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function formatResponse() : string
    {
        return json_encode([
            'id'=>$this->getId(),
            'jsonrpc'=>$this->getJsonrpc(),
            'error'=>$this->getError(),
            'result'=>$this->getResult(),
        ], JSON_UNESCAPED_UNICODE);
    }
}