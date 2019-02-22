<?php
/**
 * Created by PhpStorm.
 * User: corerman
 * Date: 19-2-21
 * Time: 上午11:28
 */

require_once (__DIR__."/CurlClient.php");

//内部循环调用十次
for ($i=0;$i<10;$i++){
    try {
        //获取Client静态变量,复用curl单体
        $ch = CurlClient::getCurlClient();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1/test.php');

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        $response = curl_exec($ch);
        // var_dump($response);
    } catch (\Exception $e) {
        $error = $e->getMessage();
        var_dump($error);
    }
}




