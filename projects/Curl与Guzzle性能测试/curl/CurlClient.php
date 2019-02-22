<?php
/**
 * Created by PhpStorm.
 * User: corerman
 * Date: 19-2-21
 * Time: 上午11:34
 */

class CurlClient
{
    protected static $curlClientConnection = null;

    public static function getCurlClient($persistent = true)
    {
        if (!$persistent || !self::$curlClientConnection) {
            self::$curlClientConnection = curl_init();
        }

        return self::$curlClientConnection;
    }

}