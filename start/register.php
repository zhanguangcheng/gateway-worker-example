<?php

/**
 * 服务注册中心
 * - Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址
 */

use Workerman\Worker;
use GatewayWorker\Register;

require_once __DIR__ . '/../init.php';

global $register;
$register = new Register(config('register-listen', 'text://0.0.0.0:1236'));
$register->secretKey = config('secret-key', '');

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
