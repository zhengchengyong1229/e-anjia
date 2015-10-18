<?php
namespace Common\Model;
use Think\Model;

class ClientModel extends Model
{

    //自动完成添加
    protected $_auto = array(
        array('status','0',self::MODEL_INSERT),  //0表示推荐状态
        array('createtime',NOW_TIME,self::MODEL_INSERT),
        array('uptime',NOW_TIME,self::MODEL_BOTH),
    );

    protected $_client_status = array(
           -1=> '删除', //删除
           0 => '推荐',//推荐
           1 => '跟进', 
           2 => '成交',
    );

    
    //添加一个客户
    public function addClient($uid,$client_name,$client_tel,$adviser_id){
         $data =  $this->create();

         $data['tid']         =$uid;
         $data['client_name'] =$client_name;
         $data['client_tel']  =$client_tel;
         $data['adviser_id']  =$adviser_id;

         $res =  $this->add($data);
         return $res;
    }

    //查询出客户列表
    public function getlist($map){
       $list =     $this->where($map)->order('id desc')->select();
       return $list;
    }


    //查询详情
    public function getDetail($id){

       $field = 'a.id,a.client_name,a.client_des,a.client_tel,a.uptime,b.nickname as tuijian_name,a.status,d.mobile as tuijian_tel,a.adviser_id,c.nickname as adviser_name,e.mobile as adviser_tel';
       $data = $this->where(array('a.id'=>$id))->alias('a')->field($field)
                        ->join('__MEMBER__ b on a.tid        = b.uid','left')       //推荐人 b
                        ->join('__UCENTER_MEMBER__ d on b.uid = d.id','left')
                        ->join('__MEMBER__ c on a.adviser_id = c.uid','left')  //顾问ID
                        ->join('__UCENTER_MEMBER__ e on c.uid = e.id','left')
                        ->find();

       $client_status = $this->_client_status;
       $data['client_status'] = $client_status[$data['status']];

       return $data;

    } 

    //给后台展示的列表
    public function getAdminList($map){

         $field = 'a.id,a.client_name,a.client_tel,a.uptime,b.nickname as tuijian_name,a.status,d.mobile as tuijian_tel,c.nickname as adviser_name,e.mobile as adviser_tel';
         $list = D('client')->alias('a')->where($map)->field($field)
                            ->join('__MEMBER__ b on a.tid        = b.uid','left')       //推荐人 b
                            ->join('__UCENTER_MEMBER__ d on b.uid = d.id','left')
                            ->join('__MEMBER__ c on a.adviser_id = c.uid','left')  //顾问ID
                            ->join('__UCENTER_MEMBER__ e on c.uid = e.id','left')
                            ->select();

         $client_status = $this->_client_status;
         foreach($list as $key=>$val){
             $list[$key]['client_status'] = $client_status[$val['status']];
         }

         return $list;

    }

    

    //放弃一个客户
    public function giveUp($map){
        $res = $this->where($map)->setField('status',-1);
        return $res;
    } 

    //修改一个客户的状态
    public function setStatus($map){
         //$res = $this->where($map)->setInc('status');
        
        $data =  array(
            'uptime'=>NOW_TIME,
            'status'=>array('exp','status+1'), 
        );

        $res = $this->where($map)->save($data);

        return $res;

    }

    //手动分配顾问 admin 使用
    /*
     * $id 主表ID
     * $new_adviser_id 新分配id
     *
     */
    

    public function assignAdviser($id,$new_adviser_id){

        $res = $this->where(array('id'=>$id))->setField('adviser_id',$new_adviser_id);
         return $res;

    }
}
