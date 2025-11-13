<?php

/**
 * 数据库配置文件
 * 包含数据库连接的所有配置信息
 */

// 数据库连接配置
$config = [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'mydocs',
        'username' => 'mydocs',
        'password' => '123456',
        'port' => 3306
    ],
    'admin' => [
        'path' => 'loginAdmin'
    ]
];

/**
 * 处理系统锁定的相关操作
 */

function handleLockSystem()
{

    return;
}