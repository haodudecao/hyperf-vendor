<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer\Traits;

use Psr\Container\ContainerInterface;
use Redis;
use SmallSung\Hyperf\Response\Exception\RateLimit;
use SmallSung\Hyperf\Utils\VerificationCode\DigitalVerificationCode;

/**
 * Trait SendIdentCodeByMobile4SignUp
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 */
trait SendIdentCodeByMobile4SignUp
{

    /**
     * @api
     * @return string
     * @throws RateLimit 发送注册验证码短信
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterError
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterFormatError
     */
    public function sendIdentCodeByMobile4SignUp()
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
        $this->sendIdentCodeByMobile4SignUp_Send($mobile, $vCode);
        return $vCode->getToken();
    }


    /**
     * 异步发送验证码
     * @param string $mobile
     * @param DigitalVerificationCode $vCode
     */
    abstract protected function sendIdentCodeByMobile4SignUp_Send(string $mobile, DigitalVerificationCode $vCode) : void ;

}