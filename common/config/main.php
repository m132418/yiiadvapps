<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'name'=>'权都有WANG',
    'language'=>'zh-cmn-Hans',
    'timeZone' => 'Asia/Shanghai',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
//        'redis10' => [
//            'class' => 'yii\redis\Connection',
//            'hostname' => 'localhost',
//            'port' => 6379,
//            'database' => 10,
//        ],
        'session' => [
            'class' => 'yii\web\DbSession',
             'sessionTable' => 'my_session',
        ],
        'setting' => [
            'class' => 'funson86\setting\Setting',
        ],
    ],
];
