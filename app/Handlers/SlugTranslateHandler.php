<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{
    public function translate($text)
    {

        // 实例化 HTTP 客户端
        $http = new Client;

        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.testid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid. $text . $salt . $key);

        // 构建请求参数
        $query = http_build_query([
            "q"     =>  $text,
            "from"  => "zh",
            "to"    => "en",
            "appid" => $appid,
            "salt"  => $salt,
            "sign"  => $sign,
        ]);

        // 发送 HTTP Get 请求
        $response = $http->get($api.$query);

        $result = json_decode($response->getBody(), true);

        /*
        获取结果，如果请求成功，dd($result) 结果如下：

        array:3 [▼
            "from" => "zh"
            "to" => "en"
            "trans_result" => array:1 [▼
                0 => array:2 [▼
                    "src" => "XSS 安全漏洞"
                    "dst" => "XSS security vulnerability"
                ]
            ]
        ]

        */

        // 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['trans_result'][0]['dst']);
        } else {
            // 如果百度翻译没有结果，使用拼音作为后备计划。
            return $this->pinyin($text);
        }
    }

    public function youdao($text)
    {

        // 实例化 HTTP 客户端
        $http = new Client;

        // 初始化配置信息
        $url = config('services.youdao_translate.api');        
        $appid = config('services.youdao_translate.appid');
        $key = config('services.youdao_translate.key');

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        // if (empty($appid) || empty($key)) {
        //     return $this->pinyin($text);
        // }


        $to   = $from = 'auto';  //目标语言
        $salt = mt_rand(10,99);     //随机数
        $sign = md5($appid.$text.$salt.$key);        //签名,通过md5(appKey+q+salt+密钥)生成


        $api = $url.
            "q=".$text.'&'.
            "from=".$from.'&'.
            "to=".$to.'&'.
            "appKey=".$appid.'&'.
            "salt=".$salt.'&'.
            "sign=".$sign;

        $response =  $http->get($api);
        $result = json_decode($response->getBody(), true);
       
        // 尝试获取获取翻译结果
        if (isset($result['translation'][0])) {
            return  str_slug($result['translation'][0]);
        } else {
            // 如果有道翻译没有结果，使用拼音作为后备计划。
            return $this->pinyin($text);
        }

    }

    public function pinyin($text)
    {
        return str_slug(app(Pinyin::class)->permalink($text));
    }
}