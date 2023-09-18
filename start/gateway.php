<?php

/**
 * 网关中心
 * - 户端的请求都是由Gateway接收然后分发给BusinessWorker处理
 * - BusinessWorker也会将要发给客户端的响应通过Gateway转发出去
 * - Gateway的onMessage行为固定为将客户端数据转发给BusinessWorker
 */

use app\components\Helper;
use app\components\JWT;
use GatewayWorker\Lib\Context;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;
use GatewayWorker\Gateway;

require_once __DIR__ . '/../init.php';

global $gateway;
$gateway = new Gateway(config('gateway-listen', 'websocket://0.0.0.0:8282'));
$gateway->name = config('gateway-name', 'Gateway');
$gateway->count = config('gateway-count', 2);
$gateway->lanIp = config('gateway-lan-ip', '127.0.0.1');
$gateway->startPort = config('gateway-start-port', 2900);
$gateway->registerAddress = config('register-address', '127.0.0.1:1236');
$gateway->pingInterval = config('gateway-ping-interval', 55);
$gateway->secretKey = config('secret-key', '');

if (config('gateway-ping-side', 'client') === 'client') {
    $gateway->pingNotResponseLimit = 1;
    $gateway->pingData = '';
} else {
    $gateway->pingNotResponseLimit = 0;
    $gateway->pingData = 'ping';
}

$gateway->onConnect = function ($connection) {
    $connection->onWebSocketConnect = function ($connection) {
        // 隐藏服务端标识
        $connection->headers[] = 'Server: *';

        // 验证连接源
        $origin = $_SERVER['HTTP_ORIGIN'] ?? $_SERVER['HTTP_REFERER'] ?? null;
        if (!checkOrigin($origin)) {
            $connection->close();
        }

        // 验证访问令牌
        if (config('enable-access-token', true)) {
            try {
                getUserByAccessToken(getAccessToken($_GET));
            } catch (Exception $e) {
                $connection->close();
            }
        }
    };
};

if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

