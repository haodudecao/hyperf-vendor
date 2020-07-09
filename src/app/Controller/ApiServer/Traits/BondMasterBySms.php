<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer\Traits;

use Hyperf\DbConnection\Db;
use Lcobucci\JWT\Token;
use Psr\Container\ContainerInterface;
use Redis;
use SmallSung\Hyperf\App\Event\LoginAccountNotExist;
use SmallSung\Hyperf\App\Event\LoginSuccessEvent;
use SmallSung\Hyperf\Response\Exception\AccountPasswordMismatch;
use SmallSung\Hyperf\Response\Exception\RateLimit;
use SmallSung\Hyperf\Utils\VerificationCode\DigitalVerificationCode;

/**
 * Trait BondMasterBySms
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 * @method string hashPassword(string $str)
 * @method string setUserId(string $str)
 * @method mixed emit(object)
 */
trait BondMasterBySms
{
    /**
     * @api
     * @return string
     * @throws RateLimit 发送托管验证短信
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterError
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterFormatError
     */
    public function sendIdentCodeByMobile4BondMaster()
    {
        $interval = 60;

        $params = $this->getParams([
            'mobile' => 'required|mobile',
        ]);
        $mobile = $params['mobile'];
        $redisKey = 'CD:'.__FUNCTION__.':'.$mobile;
        $redis = $this->container->get(Redis::class);
        if ($redis->exists($redisKey)){
            throw new RateLimit();
        }
        $redis->set($redisKey, '', $interval);

        $vCode = DigitalVerificationCode::generate($mobile, 6);
        $this->sendIdentCodeByMobile4BondMaster_Send($mobile, $vCode);
        return $vCode->getToken();
    }

    /**
     * @api
     * @return string
     * @throws RateLimit 发送绑定验证码
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterError
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterFormatError
     */
    public function sendIdentCodeByMobile4SignIn()
    {
        $interval = 60;

        $params = $this->getParams([
            'mobile' => 'required|mobile',
        ]);
        $mobile = $params['mobile'];
        $redisKey = 'CD:'.__FUNCTION__.':'.$mobile;
        /** @var Redis $redis */
        $redis = $this->container->get(Redis::class);
        if ($redis->exists($redisKey)){
            throw new RateLimit();
        }
        $redis->set($redisKey, '', $interval);

        $vCode = DigitalVerificationCode::generate($mobile, 6);
        $this->sendIdentCodeByMobile4BondMaster_Send($mobile, $vCode);
        return $vCode->getToken();
    }


    /**
     * 异步发送验证码
     * @param string $mobile
     * @param DigitalVerificationCode $vCode
     */
    abstract protected function sendIdentCodeByMobile4BondMaster_Send(string $mobile, DigitalVerificationCode $vCode) : void ;


}