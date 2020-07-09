<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer\Traits;

use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;
use Redis;
use SmallSung\Hyperf\Response\Exception\RateLimit;
use SmallSung\Hyperf\Response\Exception\VerificationCodeError;
use SmallSung\Hyperf\Utils\VerificationCode\DigitalVerificationCode;

/**
 * Trait ForgetPasswordByMobile
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 * @method string hashPassword(string $str)
 */
trait ForgetPasswordByMobile
{

    /**
     * @return string
     * @throws RateLimit
     * 发送找回密码验证码短信
     */
    public function sendIdentCodeByMobile4ForgetPassword()
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
        $this->sendIdentCodeByMobile4ForgetPassword_Send($mobile, $vCode);
        return $vCode->getToken();
    }

    public function forgetPasswordByMobileAndSms()
    {
        $params = $this->getParams([
            'mobile' => 'required|mobile',
            'password'=>'required|alpha_num|between:6,20',
            'vCode'=>'required|numeric',
            'vToken'=>'required|alpha_num',
        ]);
        $mobile = $params['mobile'];
        $mobileArray = explode('+', $mobile);
        DigitalVerificationCode::validate($mobile, $params['vToken'], $params['vCode']);

        $password = $this->hashPassword($params['password']);
        $ret = Db::table('user')->where([
            'mobile_region'=>$mobileArray[0],
            'mobile'=>$mobileArray[1],
        ])->update([
            'password'=>$password,
        ]);

        return $ret > 0;
    }

    /**
     * 异步发送验证码
     * @param string $mobile
     * @param DigitalVerificationCode $vCode
     */
    abstract protected function sendIdentCodeByMobile4ForgetPassword_Send(string $mobile, DigitalVerificationCode $vCode) : void ;

}