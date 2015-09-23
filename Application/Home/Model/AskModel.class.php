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
 * 房源需求模型 
 */
class AskModel extends Model{

    public function getList($map,$order = 'id desc',$limit = 10){
        $field = 'id,title,createtime';
        return $this->field($field)->where($map)->order($order)->limit($limit)->select();
    }

    public function getDetail($id){
            $data = $this->alias('a')
                   ->field('a.*,b.nickname,c.mobile,concat(h.name,g.name,f.name,e.name) as cbd')
                    ->join('__MEMBER__ b on a.uid=b.uid','left')
                    ->join('__UCENTER_MEMBER__ c on a.uid=c.id','left')
                    ->join('__CBD__ e on e.id = a.bid','LEFT')
                    ->join('__CBD__ f on f.id = e.pid','LEFT')
                    ->join('__DISTRICT__ g on g.id = f.pid','LEFT')
                    ->join('__DISTRICT__ h on h.id = g.upid','LEFT')
                    ->where(array('a.id'=>$id))
                    ->find();
            return $data;
    }

}
