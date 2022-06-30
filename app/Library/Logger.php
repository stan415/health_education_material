<?php

namespace App\Library;

use Illuminate\Support\Facades\Log;

class Logger
{
    private static $loggerId = null;
    private static $loggerStep = 0;

    /**
     * 获取日志唯一标识ID
     * @return string [description]
     */
    public static function getLoggerId()
    {
        if (self::$loggerId === null) {
            self::$loggerId = date("md") . md5(uniqid(mt_rand(10000, 99999), true));
        }

        return self::$loggerId;
    }


    public static function initLoggerId($loggerId)
    {
        if (! empty($loggerId)) {
            self::$loggerId = $loggerId;
        }
    }


    /**
     * 重设日志唯一标识属性
     */
    protected static function resetLogger()
    {
        self::$loggerId = null;
        self::$loggerStep = 0;
    }


    protected static function log($message, $data = [], $level = 'info')
    {
        $data['loggerid'] = self::getLoggerId();
        $data['loggerstep'] = self::$loggerStep++;

        try {
            $data['clientIp'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $data['apiUrl'] = isset($_SERVER['REQUEST_URI']) ? urldecode($_SERVER['REQUEST_URI']) : '';
            $data['http_host'] = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            $data['http_referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        } catch (\exception $e) {
            $data['exception'] = $e->getMessage();
        }

        $data['message'] = (string) $message;
        $data['level'] = $level;

        switch ($level) {
            case 'info':
                Log::info($message, $data);
                break;
            case 'error':
                Log::error($message, $data);
                break;
            case 'debug':
                Log::debug($message, $data);
                break;
        }

    }


    public static function debug($message, $context = [])
    {
        if (env('APP_DEBUG')) {
            self::log($message, $context, 'debug');
        }
    }


    public static function info($message, $context = [])
    {
        self::log($message, $context, 'info');
    }


    public static function error($message, $context)
    {
        self::log($message, $context, 'error');
    }

    public static function logE($message, $e, $context = [])
    {
        if ($e instanceof \Throwable) {
            $context['err'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'code' => $e->getCode(),
                'err' => $e->getMessage()
            ];
        }

        self::log($message, $context, 'error');
    }
}
