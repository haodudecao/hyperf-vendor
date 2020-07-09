<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Utils;
use Exception;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\Exception\ConfigNotFound;

/**
 * Class JWToken
 * @package SmallSung\Hyperf\Utils
 */
class JWToken
{
    protected ContainerInterface $container;
    protected string $secret;
    protected string $issuer = '';
    protected string $audience = '';
    protected string $subject = '';
    protected int $expires = 7 * 86400;
    protected Signer $signer;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $config = $this->container->get(ConfigInterface::class);
        $secret = $config->get('project.JWToken.secret');
        if (empty($secret)){
            throw new ConfigNotFound('project.JWToken.secret');
        }
        $this->secret = $secret;
        $this->issuer = $config->get('project.JWToken.issuer') ?: '';
        $this->audience = $config->get('project.JWToken.audience') ?: '';
        $this->subject = $config->get('project.JWToken.subject') ?: '';

        $this->signer = new Sha256();
    }

    public function generate(array $data) : Token
    {
        $jti = $this->container->get(IdGeneratorInterface::class)->generate();
        $time = time();

        $builder = new Builder();
        $builder->issuedAt($time)                           //iat 【issued at】 该jwt的发布时间；unix 时间戳
        ->expiresAt($time + $this->expires)       //exp 【expiration】 该jwt销毁的时间；unix时间戳
        ->canOnlyBeUsedAfter($time)                  //nbf 【not before】 该jwt的使用时间不能早于该时间；unix时间戳
        ->identifiedBy($jti, true); //jti 【JWT ID】 该jwt的唯一ID编号
        if (!empty($this->issuer)){
            $builder->issuedBy($this->issuer);  //iss 【issuer】发布者的url地址
        }
        if (!empty($this->audience)){
            $builder->permittedFor($this->audience);  //aud 【audience】接受者的url地址
        }
        if (!empty($this->subject)){
            $builder->relatedTo($this->subject);  //sub 【subject】该JWT所面向的用户，用于处理特定应用，不是常用的字段
        }

        foreach ($data as $key=>$value){
            $builder->withClaim($key, $value);
        }

        return $builder->getToken($this->signer, new Key($this->secret));
    }

    public function verify(string $jwToken) : ?Token
    {
        $jwToken = (new Parser())->parse($jwToken);
        try {
            if ($jwToken->verify($this->signer, new Key($this->secret))){
                $validationData = new ValidationData();
                if ($jwToken->validate($validationData)){
                    return $jwToken;
                }
            }
            return null;
        }catch (Exception $exception){
            return null;
        }
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     */
    public function setExpires(int $expires): void
    {
        $this->expires = $expires;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     */
    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->issuer;
    }

    /**
     * @param string $issuer
     */
    public function setIssuer(string $issuer): void
    {
        $this->issuer = $issuer;
    }

    /**
     * @return string
     */
    public function getAudience(): string
    {
        return $this->audience;
    }

    /**
     * @param string $audience
     */
    public function setAudience(string $audience): void
    {
        $this->audience = $audience;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return Signer
     */
    public function getSigner(): Signer
    {
        return $this->signer;
    }

    /**
     * @param Signer $signer
     */
    public function setSigner(Signer $signer): void
    {
        $this->signer = $signer;
    }
}