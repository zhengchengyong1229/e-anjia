<?php

namespace Common\Model;

use Think\Model;

class WeixinModel extends Model
{

    //获取签名并缓存
    protected function get_access_token(){
        $url =  "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxc05f97763b40b83f&secret=31f16ae1872436711edfdf4d3d6ca94c";
        $res_json = file_get_contents($url);
        $access_token = json_decode($res_json,true);
        $access_token = $access_token['access_token'];

        return $access_token;
    }

    //获取jsapi_ticket
    public function get_jsapi_ticket(){

                $access_token = $this->get_access_token();
                $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
                $jsapi_ticket = file_get_contents($url);
                $jsapi_ticket = json_decode($jsapi_ticket,true);
                $jsapi_ticket  = $jsapi_ticket['ticket'];
                S('jsapi_ticket',$jsapi_ticket,7200);

                return $jsapi_ticket;
    }
}















