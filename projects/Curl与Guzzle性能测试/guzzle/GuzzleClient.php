<?php
/**
 * Created by PhpStorm.
 * User: corerman
 * Date: 19-2-21
 * Time: 上午11:34
 */
require_once(__DIR__ . "/../vendor/autoload.php");
use \GuzzleHttp\Client;
class GuzzleClient
{
    protected static $guzzleClientConnection = null;

    public static function getGuzzleClient($baseUrl, $persistent = true)
    {
        if (!$persistent || !self::$guzzleClientConnection) {
            self::$guzzleClientConnection = new Client(['base_uri' => $baseUrl]);
        }

        return self::$guzzleClientConnection;
    }

}