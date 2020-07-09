<?php

declare(strict_types=1);

namespace SmallSung\Hyperf\Core\Listener;

use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;


/**
 * Class ValidatorFactoryResolvedListener
 * @package SmallSung\Hyperf\Core\Listener
 * 扩展验证器
 * @Listener()
 */
class ValidatorFactoryResolvedListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event)
    {
        /**  @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;

        // 注册了验证器
        $validatorFactory->extend('mobile', function ($attribute, $value, $parameters, $validator){
//        if (preg_match('@^\d{6,}$@', $value)){
//            return true;
//        }
            if (preg_match('@^\d{1,4}\+\d{6,}$@', $value)){
                return true;
            }
            return false;
        }, 'eg:1234+123456');
        // 当创建一个自定义验证规则时，你可能有时候需要为错误信息定义自定义占位符这里扩展了 :foo 占位符
//        $validatorFactory->replacer('mobile', function ($message, $attribute, $rule, $parameters) {
//            return str_replace(':foo', $attribute, $message);
//        });
    }
}