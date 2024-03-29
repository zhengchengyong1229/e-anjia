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
 * 商圈管理模型
 */
class CbdModel extends Model{

    public function getlist($map,$page,$order = 'id desc',$field = 'id,name',$r = 0){
        $order ='';
        return $this->field($field)->where($map)->order($order)->select();
    }

	public function _list($map){
		$order = 'id ASC';
		$data = $this->where($map)->order($order)->select();
		return $data;
	}

    //获取子节点,供删选使用，查出某一个城市下的cbd
    public function get_cbd_table($pid){
         $objectSql = 'select CONCAT(" 均价",FLOOR(avg(totalprice/area*10000)),"元") as avgprice ,bid from fang_object where status = 1 group by bid';

         $list = $this->where(array('city'=>$pid))
                      ->alias('a')
                      ->join('('.$objectSql.') b on b.bid = a.id','left')
                      ->getField('id,name,b.`avgprice`,pid');
         return $list;
    }

}
