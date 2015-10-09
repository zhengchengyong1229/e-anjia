<?php

namespace Common\Model;

use Think\Model;

class BrokerModel extends Model
{


    //增加兼职工,有邀请码
    /*
     * @param intval     $uid  (ucenter_member)
     * @param intval     $identity  (身份,默认兼职经纪人,当为员工时，特别指定)
     * @param strval     $yaoqingma (邀请码);
     *     
     *     1 员工
     *     2 兼职经纪人
     *     3 客户(填了邀请码的会员)->他再进行身份认证身份可以上升为兼职经纪人
     */

    public function addPid($uid,$yaoqingma){

        $p_broker_id =  $this->getBrokerIdByYaoqingma();

        if(!$p_broker_id){
            //表示获取失败，提示错误,what the fuck
            $this->error('对不起，您的邀请码不存在!');
        }

        $has = $this->where(array('uid'=>$uid))->getField('id');

        $data = array(
            'yaoqingma'=>$yaoqingma,
            'pid'   =>$p_broker_id,
            'upime'=>NOW_TIME, 
            'uid'=>$uid,
            'status'=>1,
        );

        if($has){
            $res = $this->where(array('uid'=>$uid))->save($data);
        }else{
            $data['createtime']=NOW_TIME; 
            $res = $this->where(array('uid'=>$uid))->add($data);
        }

        return $res;
    }

    //增加身份
    /*
     *    1 员工
     *    3 兼职 
     *
     */

    public function addIdentity($uid,$identity){
        $has = $this->where(array('uid'=>$uid))->getField('id');

        $data = array(
            'identity'=>$identity,
            'upime'=>NOW_TIME, 
            'uid'=>$uid,
            'status'=>1,
        );

        if($has){
            $res = $this->where(array('uid'=>$uid))->save($data);
        }else{
            $data['createtime']=NOW_TIME; 
            $res = $this->where(array('uid'=>$uid))->add($data);
        }

        return $res;
    }


    //更具邀请码在取得上级broker_id
    /*
     *  结果可能是null,邀请码不存在，可能是int('broker_id')
     *
     */
    protected function getBrokerIdByYaoqingma($yaoqingma){
            $broker_id =  D('ucenter_member')->alias('a')->where(array('mobile'=>$yaoqingma))
                          ->join('__BROKER__ b on a.id = b.uid','left')
                          ->getField('b.id');
    
            return $broker_id;
    }



}
