<?php

return [
    'host' => 'https://www.example.com',
    // 签名密钥
    'signature-key' => '',
    // 是否启用访问令牌，连接是验证访问令牌并绑定用户和client_id
    'enable-access-token' => true,
    // 访问令牌标识
    'access-token-key' => 'access_token',
    // 通讯密钥
    'secret-key' => '',
    // 允许连接的源，不合法就关掉连接
    'allow-origins' => ENV_PROD ? [
        'https://www.example.com',
    ] : [
        'http://localhost:*',
    ],
    // 回调失败重试次数
    'callback-retry' => 3,
    // 回调设置
    'callbacks' => [
        /*
        'online' => 'https://example.com',
        'offline' => 'https://example.com',
        'message' => 'https://example.com',
        */
    ],

    // 服务注册中心监听地址
    'register-listen' => 'text://0.0.0.0:1236',
    // 服务注册中心连接地址
    'register-address' => '127.0.0.1:1236',

    // 网关中心(Gateway)监听地址
    'gateway-listen' => 'websocket://0.0.0.0:8282',
    // gateway进程名称
    'gateway-name' => 'Gateway',
    // gateway进程数为cpu核数
    'gateway-count' => 2,
    // gateway本机ip，分布式部署时使用内网ip
    'gateway-lan-ip' => '127.0.0.1',
    // gateway内部通讯起始端口，假如gatewayCount=2，则起始端口为2900，会使用2900 2901 2个端口作为内部通讯端口
    'gateway-start-port' => 2900,
    // 发送心跳的端，client：客户端主动心跳，server：服务端主动心跳
    'gateway-ping-side' => 'client',
    // 心跳间隔
    'gateway-ping-interval' => 55,
    // 心跳数据
    'gateway-ping-data' => 'ping',

    // 业务进程名称
    'worker-name' => 'BusinessWorker',
    // 业务进程数，推荐为cpu2-3倍
    'worker-count' => 4,
];
