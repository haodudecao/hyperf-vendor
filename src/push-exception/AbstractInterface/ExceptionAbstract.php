<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Push\Exception\AbstractInterface;

use Exception;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Utils\ApplicationContext;

abstract class ExceptionAbstract extends Exception implements ExceptionInterface
{
    protected array $errorData = [];

    public function __construct()
    {
        parent::__construct($this->getError(), $this->getErrno());
    }

    /**
     * 国际化
     * @return string
     */
    final protected function getTransError(): string
    {
        $key = 'PushExceptionMessage.'.static::class;
        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);
        if ($translator->has($key)){
            return $translator->trans($key);
        }
        return $transError = $this->getError();
    }

    public function getHttpStatusCode(): int
    {
        return 200;
    }

    /**
     * @return array
     */
    final public function getErrorData(): array
    {
        return $this->errorData;
    }


    /**
     * @param array $errorData
     * @return $this
     */
    final public function setErrorData(array $errorData): self
    {
        $this->errorData = $errorData;
        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    final public function addErrorData(string $key, $value) :self
    {
        $this->errorData[$key] = $value;
        return $this;
    }

    /**
     * @param array $errorData
     * @return $this
     */
    final public function addErrDataByArray(array $errorData) :self
    {
        $this->errorData = array_merge($this->errorData, $errorData);
        return $this;
    }

    final public function toError(): array
    {
        $error = [];
        $error['code'] = $this->getErrno();
        $error['message'] = $this->getTransError();
        if (!empty($this->getErrorData())){
            $error['data'] = $this->getErrorData();
        }
        return $error;
    }
}