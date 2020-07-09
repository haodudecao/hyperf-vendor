<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Server\WebSocketServer;

use Closure;
use SmallSung\Hyperf\Push\Exception\AbstractInterface\ExceptionInterface;
use SmallSung\Hyperf\Push\Exception\ApiNotFound;
use SmallSung\Hyperf\Push\Exception\DataNotAllowed;

class Request
{
    /**
     * 不再向客户端发送
     * @var bool
     */
    private bool $finish = false;
    /**
     * send该数据的客户端
     */
    private int $fd = -1;
    private int $frameOpcode = -1;
    private ?string $frameData = null;
    /**
     * push该数据的客户端
     */
    private int $pushFd = -1;
    private ?JsonRpc $jsonRpc = null;
    private $handler = null;

    private ?ExceptionInterface $exception = null;

    /**
     * @return static
     */
    static public function create() : self
    {
        return new static();
    }

    public function __construct()
    {
        $this->jsonRpc = JsonRpc::create();
    }

    /**
     * @return $this
     */
    public function response() : self
    {
        return clone $this;
    }

    /**
     * @return JsonRpc
     */
    public function getJsonRpc(): JsonRpc
    {
        return $this->jsonRpc;
    }

    /**
     * @param JsonRpc $jsonRpc
     * @return $this
     */
    public function setJsonRpc(JsonRpc $jsonRpc): self
    {
        $this->jsonRpc = $jsonRpc;
        return $this;
    }


    /**
     * @param string $json
     * @return $this
     * @throws DataNotAllowed
     */
    public function setJsonRpcFromJson(string $json): self
    {
        try {
            $this->jsonRpc->loadJson($json);
        } catch (DataNotAllowed $e) {
            throw $e->setFd($this->fd);
        }
        return $this;
    }

    /**
     * @param int $fd
     * @return $this
     */
    public function setFd(int $fd): self
    {
        $this->fd = $fd;
        return $this;
    }

    /**
     * @return int
     */
    public function getFd(): int
    {
        return $this->fd;
    }

    /**
     * @return int
     */
    public function getPushFd(): int
    {
        return $this->pushFd;
    }

    /**
     * @param int $pushFd
     * @return $this
     */
    public function setPushFd(int $pushFd): self
    {
        $this->pushFd = $pushFd;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFinish(): bool
    {
        return $this->finish;
    }

    /**
     * @param bool $finish
     * @return $this
     */
    public function setFinish(bool $finish): self
    {
        $this->finish = $finish;
        return $this;
    }

    /**
     * @return int
     */
    public function getFrameOpcode() : int
    {
        return $this->frameOpcode;
    }

    /**
     * @param int $frameOpcode
     * @return $this
     */
    public function setFrameOpcode(int $frameOpcode): self
    {
        $this->frameOpcode = $frameOpcode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrameData()
    {
        return $this->frameData;
    }

    /**
     * @param $frameData
     * @return $this
     */
    public function setFrameData($frameData): self
    {
        $this->frameData = $frameData;
        return $this;
    }

    /**
     * @return ExceptionInterface
     */
    public function getException(): ExceptionInterface
    {
        return $this->exception;
    }

    /**
     * @param ExceptionInterface $exception
     * @return $this
     */
    public function setException(ExceptionInterface $exception): self
    {
        $this->exception = $exception;
        $this->jsonRpc->setError($exception->toError());
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param $handler
     * @return $this
     * @throws ApiNotFound
     */
    public function setHandler($handler): self
    {
        if ($handler instanceof Closure){
            $this->handler = $handler;
        }elseif (is_string($handler)){
            if (strpos($handler, '@') !== false) {
                $handler =  explode('@', $handler);
            }else{
                $handler = explode('::', $handler);
            }
            if (is_array($handler)  && isset($handler[0], $handler[1]) && count($handler) === 2) {
                $this->handler = $handler;
            }
        }else{
            throw new ApiNotFound($this->frameData, $this->fd);
        }
        return $this;
    }
}