<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Utils\VerificationCode;

use SmallSung\Hyperf\Response\Exception\VerificationCodeError;

/**
 * Class DigitalVerificationCode
 * @package App\Business
 * 数字验证码
 */
class DigitalVerificationCode
{
    private string $salt = 'SALT';
    private int $expire = 180;

    private string $vcode = '';
    private int $timestamp = 0;
    private string $sign = '';
    private string $token = '';
    private string $own = '';

    public static function generate(string $own, int $len=6) : self
    {
        $self = new static();
        $self->setOwn($own);
        $self->generateVcode($len);
        return $self;
    }

    public static function validate(string $own, string $token, string $vcode, bool $throw = true) : bool
    {
        $self = new static();
        $self->setOwn($own);
        $self->setToken($token);
        $self->setVcode($vcode);
        if (time() <= $self->getTimestamp() + $self->getExpire()){
            if ($self->getSign() === $self->sign($self->getTimestamp(), $self->getVcode(), $self->getOwn())){
                return true;
            }
        }
        if ($throw){
            throw new VerificationCodeError();
        }
        return false;
    }

    public function __construct()
    {
    }

    public function generateVcode($len=6) : void
    {
        $vcode = [];
        for ($i=0; $i<$len; $i++){
            $vcode[] = rand(0, 9);
        }
        $this->vcode = implode('', $vcode);
        $this->timestamp = time();
        $this->sign = $this->sign($this->timestamp, $this->vcode, $this->own);
        $this->token = $this->token($this->timestamp, $this->sign);
    }


    public function sign(int $timestamp, string $vcode, string $own) : string
    {
        return md5(sprintf('%s-%s-%s-%s', (string)$timestamp, $own, $vcode, $this->salt));
    }
    public function token(int $timestamp, string $sign) : string {
        return (string)$timestamp.$sign;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->timestamp = (int)substr($token, 0, 10);
        if (strlen($token) > 10){
            $this->sign = substr($token, 10);
        }
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getVcode(): string
    {
        return $this->vcode;
    }

    /**
     * @param string $vcode
     */
    public function setVcode(string $vcode): void
    {
        $this->vcode = $vcode;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }


    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getOwn(): string
    {
        return $this->own;
    }

    /**
     * @param string $own
     */
    public function setOwn(string $own): void
    {
        $this->own = $own;
    }

    /**
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt(string $salt): void
    {
        $this->salt = $salt;
    }

    /**
     * @return int
     */
    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * @param int $expire
     */
    public function setExpire(int $expire): void
    {
        $this->expire = $expire;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }
}