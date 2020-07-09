<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\App\Controller\ApiServer\Traits;


use Hyperf\DbConnection\Db;
use Lcobucci\JWT\Token;
use Psr\Container\ContainerInterface;
use SmallSung\Hyperf\App\Event\LoginAccountNotExist;
use SmallSung\Hyperf\App\Event\LoginPasswordErrorEvent;
use SmallSung\Hyperf\App\Event\LoginSuccessEvent;
use SmallSung\Hyperf\Response\Exception\AccountPasswordMismatch;

/**
 * Trait SignInByMobileAndPassword
 * @package SmallSung\Hyperf\App\Controller\ApiServer\Traits
 * @property ContainerInterface $container
 * @method array getParams(array $validRules=[])
 * @method string hashPassword(string $str)
 * @method string setUserId(string $str)
 * @method mixed emit(object)
 */
trait SignInByMobileAndPassword
{
    /**
     * @api
     * @return string
     * @throws AccountPasswordMismatch
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterError
     * @throws \SmallSung\Hyperf\Response\Exception\RequestParameterFormatError
     */
    public function signInByMobileAndPassword()
    {
        $params = $this->getParams([
            'mobile' => 'required|mobile',
            'password'=>'required|alpha_num|between:6,20',
        ]);
        $mobile = $params['mobile'];
        $mobileArray = explode('+', $mobile);
        $user = Db::table('user')->where([
            'mobile_region'=>$mobileArray[0],
            'mobile'=>$mobileArray[1],
        ])->limit(1)->first();
        if (is_null($user)){
            $event = new LoginAccountNotExist();
            $this->emit($event);
            throw new AccountPasswordMismatch();
        }
        if ($this->hashPassword($params['password']) !== $user->password){
            $event = new LoginPasswordErrorEvent();
            $this->emit($event);
            throw new AccountPasswordMismatch();
        }
        $event = new LoginSuccessEvent();
        $this->emit($event);
        /** @var Token $jwToken */
        $jwToken = $this->setUserId($user->userid);
        return $jwToken->__toString();
    }
}