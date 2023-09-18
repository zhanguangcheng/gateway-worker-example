<?php

use app\components\ErrorCode;
use app\components\JWT;
use Workerman\Worker;

global $CONFIG;
$CONFIG = require_once APP_PATH . '/config/main.php';

function vd($var, $return = false)
{
    $export = var_export($var, true);
    $patterns = [
        "/array \(/" => '[',
        "/^([ ]*)\)(,?)$/m" => '$1]$2',
        "/=>[ ]?\n[ ]+\[/" => '=> [',
        "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
    ];
    $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
    if ($return) {
        return $export;
    }
    echo $export;
}

function config(string $key = null, $defaultValue = null)
{
    global $CONFIG;
    if ($key) {
        return $CONFIG[$key] ?? $defaultValue;
    }
    return $CONFIG;
}

function getAccessToken($get)
{
    $accessTokenKey = config('access-token-key', 'access_token');
    return $get[$accessTokenKey] ?? null;
}

/**
 * @param $accessToken
 * @return array
 * @throws Exception
 */
function getUserByAccessToken($accessToken): array
{
    $accessTokenKey = config('access-token-key', 'access_token');
    paramsRequired([$accessTokenKey => $accessToken]);
    $payload = JWT::decode($accessToken, config('signature-key'));
    paramsRequired(['uid' => $payload['uid'] ?? null]);
    return $payload;
}

/**
 * 检测连接来源
 * @param ?string $origin 来源url
 * @return bool
 * @link https://github.com/walkor/phpsocket.io/blob/master/src/Engine/Engine.php#L132
 */
function checkOrigin(?string $origin): bool
{
    $allowOrigins = config('allow-origins', []);
    if (empty($allowOrigins)) {
        return true;
    }

    // file:// URLs produce a null Origin which can't be authorized via echo-back
    if ('null' === $origin || null === $origin) {
        return true;
    }
    if (empty($origin)) {
        return false;
    }

    $parts = parse_url($origin);
    $defaultPort = 'https:' === $parts['scheme'] ? 443 : 80;
    $parts['port'] = $parts['port'] ?? $defaultPort;
    foreach ($allowOrigins as $allowOrigin) {
        $ok =
            $allowOrigin === $parts['scheme'] . '://' . $parts['host'] . ':' . $parts['port'] ||
            $allowOrigin === $parts['scheme'] . '://' . $parts['host'] ||
            $allowOrigin === $parts['scheme'] . '://' . $parts['host'] . ':*' ||
            $allowOrigin === '*:' . $parts['port'];
        if ($ok) {
            // 只需要有一个白名单通过，则都通过
            return true;
        }
    }
    return false;
}

function safeEcho($message, $category = 'INFO')
{
    $time = date('Y-m-d H:i:s');
    $log = "$time [$category] $message\n";
    Worker::safeEcho($log);
}

function writeLog($message, $category = 'INFO', $file = 'app')
{
    $logFile = APP_PATH . "/runtime/$file.log";
    $time = date('Y-m-d H:i:s');
    $log = "$time [$category] $message\n";
    file_put_contents($logFile, $log, FILE_APPEND | LOCK_EX);
}

function parseUrlQuery($url): array
{
    $urls = parse_url($url);
    if (empty($urls['query'])) {
        return [];
    }
    parse_str($urls['query'], $params);
    return $params;
}

/**
 * @throws InvalidArgumentException
 */
function paramsRequired(array $args): void
{
    foreach ($args as $k => $v) {
        if ($v === null || $v === '') {
            throw new InvalidArgumentException("参数不能为空：$k");
        }
    }
}

/**
 * @param string|array $message
 * @param array $result
 * @return string
 */
function successResponse($message = 'OK', array $result = []): string
{
    if (!is_string($message)) {
        $result = $message;
        $message = 'OK';
    }
    return response(ErrorCode::OK, $message, $result);
}

function errorResponse($code, $message, $result = []): string
{
    return response($code, $message, $result);
}

function clientErrorResponse($message, $result = []): string
{
    return response(ErrorCode::CLLIENT_ERROR, $message, $result);
}

function serverErrorResponse($message = 'Server internal error', $result = []): string
{
    return response(ErrorCode::SERVER_ERROR, $message, $result);
}

function response($code, $message, $result): string
{
    return jsonEncode([
        'code' => $code,
        'message' => $message,
        'result' => $result,
    ]);
}

function jsonEncode($data)
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function jsonDecode(string $json)
{
    return json_decode($json, true);
}
