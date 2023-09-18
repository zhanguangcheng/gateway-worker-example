# gateway-worker-example

gateway-worker-example是使用[GatewayWorker](https://www.workerman.net/doc/gateway-worker)搭建的基础使用示例。

[GatewayWorker](https://www.workerman.net/doc/gateway-worker/) 是一个基于[Workerman](https://www.workerman.net)开发的，用于快速开发TCP长连接应用的项目框架。
- 适合开发TCP长链接的项目
- 天然支持分布式多服务器部署
- 不支持UDP

## 功能
* 项目结构优化
* 参数配置化
* 限制连接的源
* 连接用户认证、绑定
* 设置被动心跳
* 支持回调配置

## 快速启动
```bash
git clone https://github.com/zhanguangcheng/gateway-worker-example.git
cd gateway-worker-example
composer install
# 设置config/main.php的signature-key为随机字符串
php start.php start
```

不支持reload的文件
- init.php
- start.php
- start/*
- config/main.php
- components/functions.php
