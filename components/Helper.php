<?php

namespace app\components;

use Workerman\Http\Client;
use Workerman\Timer;

class Helper
{
    /**
     * 事件回调
     * @param string $type
     * @param array $data
     * @param int $retry
     * @return void
     */
    public static function eventsCallback(string $type, array $data, int $retry = 0)
    {
        $callbacks = config('callbacks');
        if (isset($callbacks[$type])) {
            $http = new Client();
            $data['type'] = $type;
            $retryFunc = function() use($type, $data, $retry) {
                if ($retry < config('callback-retry', 3)) {
                    Timer::add(10 * pow(3, $retry), function($type, $data, $retry) {
                        self::eventsCallback($type, $data, $retry + 1);
                    }, [$type, $data, $retry], false);
                }
            };
            $http->post($callbacks[$type], $data, function($response) use($retryFunc) {
                echo $response->getBody();
                if ($response->getStatusCode() !== 200) {
                    $retryFunc();
                }
            }, function($exception) use($retryFunc) {
                echo $exception;
                $retryFunc();
            });
        }
    }
}