<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Controller\ApiServer;

use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\Context;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Lcobucci\JWT\Token;
use SmallSung\Hyperf\Controller\ControllerAbstract as ParentControllerAbstract;
use SmallSung\Hyperf\Response\Exception\NormalError;
use SmallSung\Hyperf\Response\Exception\RequestParameterError;
use SmallSung\Hyperf\Response\Exception\RequestParameterFormatError;
use SmallSung\Hyperf\Response\Exception\Unauthorized;
use SmallSung\Hyperf\Response\Exception\Unknown;
use SmallSung\Hyperf\Utils\JWToken;
use function json_decode;
use function json_last_error;

abstract class ControllerAbstract extends ParentControllerAbstract
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected RequestInterface $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected ValidatorFactoryInterface $validationFactory;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $data
     * @return Token
     */
    protected function generateJWToken(array $data) : Token
    {
        return $this->container->get(JWToken::class)->generate($data);
    }

    /**
     * @return Token
     * @throws Unauthorized
     */
    protected function getJWtoken() : Token
    {
        try {
            $jwToken = $this->request->getHeaderLine('JWToken');
            $jwToken = $this->container->get(JWToken::class)->verify($jwToken);
            if ($jwToken instanceof Token){
                return $jwToken;
            }
        }catch (Exception $exception){
            throw new Unauthorized();
        }
        throw new Unauthorized();
    }

    /**
     * @return int
     * @throws Unauthorized
     */
    protected function getUserId() : int
    {
        $userId = Context::get('jwToken.userId');
        if (is_int($userId) && $userId > 0 ){
            return $userId;
        }
        $jwToken = $this->getJWtoken();
        $userId = $jwToken->getClaim('userId');
        if (!is_int($userId) || $userId <= 0){
            throw new Unauthorized();
        }
        return Context::set('jwToken.userId', $userId);
    }

    /**
     * @return int
     * @throws Unauthorized
     */
    protected function getLoginType() : string
    {
        $loginType = Context::get('jwToken.loginType');
        if (is_int($loginType) && $loginType > 0 ){
            return $loginType;
        }
        $jwToken = $this->getJWtoken();
        $loginType = $jwToken->getClaim('loginType');
        if (!is_string($loginType) || !in_array($loginType, ['login', 'master'])){
            throw new Unauthorized();
        }
        return Context::set('jwToken.loginType', $loginType);
    }

    /**
     * @param int $userId
     * @param bool $returnJWToken
     * @return Token|null
     */
    protected function setLoginType(string $loginType, int $userId, $returnJWToken = true) : ?Token
    {
        Context::set('jwToken.loginType', $loginType);
        if ($returnJWToken){
            return $this->generateJWToken([
                'loginType'=>$loginType,
                'userId'=>$userId,
            ]);
        }
        return null;
    }
    
    /**
     * @param int $userId
     * @param bool $returnJWToken
     * @return Token|null
     */
    protected function setUserId(int $userId, $returnJWToken = true) : ?Token
    {
        Context::set('jwToken.userId', $userId);
        if ($returnJWToken){
            return $this->generateJWToken([
               'userId'=>$userId,
            ]);
        }
        return null;
    }

    /**
     * 生成雪花算法ID
     * @return int
     */
    protected function generateSnowflake() : int
    {
        return $this->container->get(IdGeneratorInterface::class)->generate();
    }

    /**
     * @param array $validRules
     * @return array
     * @throws RequestParameterError
     * @throws RequestParameterFormatError
     */
    protected function getParams(array $validRules=[]) : array
    {
        $body = $this->request->getBody()->getContents();
        $params = json_decode($body, true, 512, 0);
        if (JSON_ERROR_NONE !== json_last_error()){
            throw new RequestParameterFormatError();
        }
        if (!empty($validRules)){
            $params = $this->validParams($validRules, $params);
        }
        return $params;
    }

    /**
     * @param array $rules
     * @param array|null $params
     * @return array
     * @throws RequestParameterError
     * @throws RequestParameterFormatError
     */
    protected function validParams(array $rules, ?array $params=null) : array
    {
        $params ??= $this->getParams();
        $validator = $this->validationFactory->make($params, $rules);
        if ($validator->fails()){
            $throw = new RequestParameterError();
            $throw->setErrorData($validator->errors()->getMessages());
            throw $throw;
        }
        return $params;
    }

    /**
     * @param int $param1
     * @param string $param2
     * @param array $params
     * @throws NormalError
     */
    protected function throwNormalError(int $param1 = -1, string $param2 = '', array $params = []) : void
    {
        $exception = new NormalError($param1, $param2);
        $exception->setErrorData($params);
        throw $exception;
    }

    /**
     * @throws Unknown
     */
    protected function throwUnknown() : void
    {
        throw new Unknown();
    }
}