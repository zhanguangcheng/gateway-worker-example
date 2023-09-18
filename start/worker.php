<?php

/**
 * 业务进程
 */

use app\components\Events;
use GatewayWorker\BusinessWorker;
use Workerman\Worker;

require_once __DIR__ . '/../init.php';

global $worker;
$worker = new BusinessWorker();
$worker->name = config('worker-name', 'BusinessWorker');
$worker->count = config('worker-count', 4);
$worker->registerAddress = config('register-address', '127.0.0.1:1236');
$worker->secretKey = config('secret-key', '');
$worker->eventHandler = Events::class;

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
