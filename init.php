<?php

use Workerman\Worker;

const DEBUG = true;
const ENV = 'dev';

const ENV_DEV = ENV === 'dev';
const ENV_PROD = ENV === 'prod';
const APP_PATH = __DIR__;

require_once __DIR__ . '/vendor/autoload.php';
Worker::$logFile = APP_PATH . '/runtime/workerman.log';

if (empty(config('signature-key'))) {
    exit("Please configure sign-key in file:config/main.php\n");
}
