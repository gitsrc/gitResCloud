<?php
/**
 * Created by PhpStorm.
 * User: corerman
 * Date: 19-2-21
 * Time: 上午11:28
 */

require_once (__DIR__."/GuzzleClient.php");


//内部循环调用十次
for ($i=0;$i<10;$i++){
    try {
        //获取Client静态变量,复用curl单体
        $client = GuzzleClient::getGuzzleClient("http://127.0.0.1");
       $response = $client->request('GET', '/test.php');
       // $promise = $client->requestAsync('GET', '/test.php');
      //  var_dump($response->getBody()->getContents());
    } catch (\Exception $e) {
        $error = $e->getMessage();
        var_dump($error);
    }
}




