<?php

declare(strict_types=1);

return [
    'projectName'=>env('PROJECT_NAME'),
    'JWToken'=>[
        'secret'=>env('JWTOKEN_SECRET'),
        'issuer'=>env('JWTOKEN_ISSUER'),        //iss 【issuer】发布者的url地址
        'audience'=>env('JWTOKEN_AUDIENCE'),    //aud 【audience】接受者的url地址
        'subject'=>env('JWTOKEN_SUBJECT'),      //sub 【subject】该JWT所面向的用户，用于处理特定应用，不是常用的字段
    ],
    'Mailer'=>[
        'host'=>env('MAIL_HOST'),
        'port'=>intval(env('MAIL_PORT', 25)),
        'username'=>env('MAIL_USERNAME'),
        'password'=>env('MAIL_PASSWORD'),
    ],
    'SnowFlake'=>[
        'dataCenterId'=>env('SNOWFLAKE_DATACENTERID'),
    ]
];