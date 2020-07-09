<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer\Traits;


use Hyperf\DbConnection\Db;
use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\Response\Exception\AlreadyExist;
use SmallSung\Hyperf\Response\Exception\VerificationCodeError;
use SmallSung\Hyperf\Utils\VerificationCode\DigitalVerificationCode;

/**
 * Trait SignUpByMobile
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 * @method string hashPassword(string $str)
 * @method string generateSnowflake()
 */
trait SignUpByMobile
{
    /**
     * @api
     * @return bool
     * @throws AlreadyExist
     * @throws VerificationCodeError
     * 通过手机号注册
     */
    public function signUpByMobile()
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

        if (!is_null(Db::table('user')->select('id')->where([
            'mobile_region'=>$mobileArray[0],
            'mobile'=>$mobileArray[1],
        ])->limit(1)->first())){
            throw new AlreadyExist();
        }

        $currTimestamp = time();
        $password = $this->hashPassword($params['password']);
        $snowflake = $this->generateSnowflake();
        $ret = Db::table('user')->insert([
            'userid'=>$snowflake,
            'mobile_region'=>$mobileArray[0],
            'mobile'=>$mobileArray[1],
            'password'=>$password,
            'create_timestamp'=>$currTimestamp,
        ]);

        return boolval($ret);
    }
}