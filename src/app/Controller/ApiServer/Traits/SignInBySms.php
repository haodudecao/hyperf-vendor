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
 * Trait SignInBySms
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 * @method string hashPassword(string $str)
 * @method string setUserId(string $str)
 * @method mixed emit(object)
 */
trait SignInBySms
{
    /**
     * @api
     * @return string
     * @throws AccountPasswordMismatch
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterError
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterFormatError
     * @throws \SmallSung\Hyperf\Response\Exception\VerificationCodeError
     */
    public function signInByMobileAndSms()
    {
        $params = $this->getParams([
            'mobile' => 'required|mobile',
            'vCode'=>'required|numeric',
            'vToken'=>'required|alpha_num',
        ]);
        $mobile = $params['mobile'];
        $mobileArray = explode('+', $mobile);

        DigitalVerificationCode::validate($mobile, $params['vToken'], $params['vCode']);

        $user = Db::table('user')->where([
            'mobile_region'=>$mobileArray[0],
            'mobile'=>$mobileArray[1],
        ])->limit(1)->first();
        if (is_null($user)){
            $event = new LoginAccountNotExist();
            $this->emit($event);
            throw new AccountPasswordMismatch();
        }
        $event = new LoginSuccessEvent();
        $this->emit($event);
        /** @var Token $jwToken */
        $jwToken = $this->setUserId($user->userid);
        return $jwToken->__toString();
    }

    /**
     * @api
     * @return string
     * @throws RateLimit 发送登录验证码短信
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
        $this->sendIdentCodeByMobile4SignIn_Send($mobile, $vCode);
        return $vCode->getToken();
    }


    /**
     * 异步发送验证码
     * @param string $mobile
     * @param DigitalVerificationCode $vCode
     */
    abstract protected function sendIdentCodeByMobile4SignIn_Send(string $mobile, DigitalVerificationCode $vCode) : void ;

}