<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Model;
use Think\Model;
/**
 * 楼盘模型 
 */
class PropertyModel extends Model{
    //获取楼盘列表
    /*
     *   主页展示楼盘列表
     *   规则  条件城市,房源数量,更新时间  
     *   
     *   头像 ，手工上传
     *
     */
    public function getlist($map,$page = 0 ,$order = 'objectcount desc', $r = 10 ){
        $field = 'distinct a.cuxiao,a.id,a.name as title,a.objectcount,a.uptime,a.cover,(select min(`totalprice`/`area`*10000) from fang_object where fid = a.id) as uprice';
        $list  =     $this->where($map)->field($field)->alias('a')
                     ->join('__OBJECT__ d on d.fid = a.id','left')   //房源搜索条件
                     //->order('a.objectcount desc')
                     ->order('a.uptime desc')
                     ->page($page,$r)
                     ->select();

        foreach($list as $key=>$val){
            $list[$key]['avatar'] = query_user('avatar128',$val['uid']);
        }
        return $list;
    }
    public function getDetail($id){
        return $this->find($id);
    }
}
