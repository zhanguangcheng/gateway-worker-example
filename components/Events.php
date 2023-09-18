<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */

// declare(ticks=1);
namespace app\components;

use Exception;
use GatewayWorker\Lib\Gateway;
use Workerman\Http\Client;


/**
 * 主逻辑
 */
class Events
{
    public static function onWorkerStart()
    {
        safeEcho("workerStart");
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     */
    public static function onConnect($client_id)
    {
        safeEcho("connect:$client_id");
    }

    public static function onWebSocketConnect($client_id, $data)
    {
        safeEcho("webSocketConnect:$client_id");
        if (config('enable-access-token', true)) {
            try {
                // 绑定用户
                $user = getUserByAccessToken(getAccessToken($data['get']));
                Gateway::bindUid($client_id, $user['uid']);
                unset($user['iat'], $user['exp']);
                $_SESSION = $user;

                if (isset($_SESSION['uid'])) {
                    Helper::eventsCallback('online', ['uid' => $_SESSION['uid']]);
                }
            } catch (Exception $e) {
            }
        }

    }

    /**
     * 当客户端发来消息时触发
     * @param string $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message)
    {
        safeEcho("message:$client_id:$message");
        if (isset($_SESSION['uid']) && config('gateway-ping-data', 'ping') !== $message) {
            Helper::eventsCallback('message', ['uid' => $_SESSION['uid'], 'message' => $message]);
        }
    }

    /**
     * 当用户断开连接时触发
     * @param string $client_id 连接id
     */
    public static function onClose($client_id)
    {
        safeEcho("close:$client_id");
        if (isset($_SESSION['uid'])) {
            Helper::eventsCallback('offline', ['uid' => $_SESSION['uid']]);
        }
    }
}
