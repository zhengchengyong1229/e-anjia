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
 * 说说模型
 */
class ShuoModel extends Model{

    protected $_auto = array(
         array('status','1'),
         array('createtime',NOW_TIME,1),
         array('uptime',NOW_TIME,3),
         array('uid','is_login','1','function'),
         array('hid','gethid',3,'callback'),
         array('sTime','strtotime',3,'function'),
         array('eTime','strtotime',3,'function')
    );

    protected $_validate = array(
    //     array('title', '0,15', '长度应在15个字内', self::EXISTS_VALIDATE, 'length'), 
    );


    public function getList($map = '',$page = 0,$r = 10){
        $field = 'distinct a.uid,IF(a.title!="",a.title,concat(count,"套房源")) as title,a.content,a.uptime,a.identity,a.count,a.sTime,a.eTime,b.nickname';
        $list  =   $this->where($map)->field($field)->alias('a')
                 ->join('__MEMBER__ b on a.uid = b.uid','left')
                 ->join('__PROPERTY__ c on c.id = a.property','left')
                 ->join('__OBJECT__ d on d.uid = a.uid','left')
                 ->order('a.uptime desc')
                 ->page($page,$r)
                 ->select();

        foreach($list as $key=>$val){
            $list[$key]['avatar'] = query_user('avatar128',$val['uid']);
        }



        return $list;
    }

    protected function gethid(){
        return D('property')->where(array('uid'=>is_login()))->getfield('id');
    }

    public function getdetail($map){
      $data =   $this->alias('a')->field('a.title,a.uid,a.content,a.policy,a.identity,a.sTime,a.eTime,b.nickname')
                     ->join('__MEMBER__ b on a.uid = b.uid','left')->where($map)->find();

      if($data){
         $data['avatar'] = query_user('avatar64',$data['uid']);
      }

      return $data;
    }
}
