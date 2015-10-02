<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class BaseController extends Controller
{

    protected $city_name;
    protected $city_id;

    /* 空操作，用于输出404页面 */
    public function _empty()
    {
        $this->redirect('Index/index');
    }


    protected function _initialize()
    {


        //微信分享
        $this->weixin_js();

        //自动登陆
        D('Common/Member')->need_login();

        $city = session('user_city.city_id');
        if(!$city['city_id']){
           // $ip = $_SERVER['REMOTE_ADDR'];
              $ip = '221.2.170.181';

            //开始取得城市信息
            $city = get_city_by_lee_ip($ip);
            //如果有区域参数，则获取区域id
            //$city_id = M('district')->where(array('name'=>$city))->getField('id');
            $city_id = 371000;

            //开始取得城市信息  end 
            //把城市信息存到session中
            session('user_city',array('city_name'=>$city,'city_id'=>$city_id));

            $this->city_name = $city;
            $this->city_id   = $city_id;
        }else{
            $this->city_name = $city['city_name'];
            $this->city_id   = $city['city_id'];
        }




        if(is_login()){
                if(!$user_info = session('user_info')){
                    $user_info = $this->user_info();
                }
                $this->assign('user_info',$user_info);
        }

        //统计房源数量
        ($object_count = S('TOTAL_OBJECT_COUNT')) || ($object_count =  S('TOTAL_OBJECT_COUNT',$this->createTotalObjectCount(),2*60*60));
        $this->assign('object_count',$object_count);

        //header("Content-type:text/html;charset=uft-8");
        /*读取站点配置*/
        $config = api('Config/lists');
        C($config); //添加配置
        if (!C('WEB_SITE_CLOSE')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
    }

    /* 用户登录检测 */
    protected function login()
    {
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
    }

    protected function ensureApiSuccess($result)
    {
        if (!$result['success']) {
            $this->error($result['message'], $result['url']);
        }
    }

    //获取客户信息
    protected function user_info(){

      $user_info = query_user(array('avatar128','nickname','uid','mobile'));
      $user_info['identity'] = D('broker')->where(array('uid'=>is_login()))->getField('identity');
      session('user_info',$user_info);
      return $user_info;
    }


    protected function weixin_js(){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $arr = array(
            'jsapi_ticket'=>  S('jsapi_ticket')?S('jsapi_ticket'):D('Common/weixin')->get_jsapi_ticket(),
            'noncestr'    =>  create_rand(),
            'timestamp'   => NOW_TIME,
            'url'         => $url,
        );

        $str = '';

        foreach($arr as $key=>$val){
            if($key == 'url'){
               $str .= $key.'='.$val;
            }else{
               $str .= $key.'='.$val.'&';
            }
        }

        $str = sha1($str);
        $arr['signature'] = $str;

        $weixin_json = json_encode($arr);
        $this->assign('weixin_json',$weixin_json);

    }

    protected function createTotalObjectCount(){
       return  D('object')->where(array('status'=>1))->count();

    }
}
