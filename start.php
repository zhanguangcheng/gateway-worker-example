<?php
/**
 * run with command
 * php start.php start
 */

ini_set('display_errors', 'on');

use Workerman\Worker;

if (strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_for_win.bat\n");
}

if (!extension_loaded('pcntl') || !extension_loaded('posix')) {
    exit("Please install pcntl and posix extension. See https://www.workerman.net/doc/workerman/appendices/install-extension.html\n");
}

const GLOBAL_START = true;
require_once __DIR__ . '/init.php';
foreach (glob(__DIR__ . '/start/*.php') as $startFile) {
    require_once $startFile;
}
Worker::runAll();
