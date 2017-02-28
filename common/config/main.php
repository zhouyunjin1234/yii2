<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'auth_item',
            'assignmentTable' => 'auth_assignment',
            'itemChildTable' => 'auth_item_child',
            'ruleTable'=>'auth_rule',
            ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'keyPrefix'=> 'blodedemo'
        ],
    ],
];
