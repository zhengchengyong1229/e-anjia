<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Home\Controller;
/**
 * 发布一个需求 
 *
 */
class AskController extends BaseController
{

    protected $d_ask;
    protected $mid;
    public function _initialize(){
        parent::_initialize();

        $user_info = $this->user_info;
        $identity  = $user_info['identity'];

        if((!is_login())||$identity == 0){
            $this->success('仅认证会员可操作');
        }

        $this->d_ask = D('Ask');
        $this->mid = is_login();
    }
    
    public function index(){

       echo '我是询问模块主页面<br>';
       echo '<a href='.U('ask/edit').'>新增一条咨询信息</a><br>';
       echo '<a href='.U('ask/edit',array('id'=>4)).'>编辑一条咨询信息</a><br>';
       echo '<a href='.U('ask/setStatus',array('id'=>4,'status'=>0)).'>设置信息状态</a><br>';
       $list =  $this->d_ask->getlist();
       dump($list);

    }
    //编辑一条需求信息有待完善 
    /*
     *
     * @param 同editROW
     *
     *
     *
     */
    public function edit(){
        $id = intval(I('param.id'));
        $d_ask = D('Ask');

        if(IS_POST){

            $data = $d_ask->create();

            if($id){
                $data['id'] = $id;
                //编辑一个房源
                if(!($this->mid == $this->d_ask->where(array('id'=>$data['id']))->getField('uid'))&&!is_administrator()){
                    $this->error('对不起，您的权限不足');
                }
                $data['uptime'] = time();
                $data['status']     = 1;
                if($this->d_ask->save($data)){
                    $this->ajaxReturnHandle(1,'编辑需求成功',U('home/object/index',array('uid'=>$this->mid)));
                }else{
                    $this->ajaxReturnHandle(0,'编辑需求失败');
                }
            }else{
                //新增一个房源
                $data['uid']        = $this->mid;
                $data['createtime'] = $data['uptime'] = time();
                $data['status']     = 1;

                if($id = $this->d_ask->add($data)){
                    $this->ajaxReturnHandle(1,'新增需求成功',U('home/object/index',array('uid'=>$this->mid)));
                }else{
                    $this->ajaxReturnHandle(0,'新增需求失败');
                }
            }
        }else{
            if($id){
                $data = $d_ask->getDetail($id);

                $data['cbd'] = get_whole_address($data['bid']);//根据cbd，找到其父菜单
                $data['cbd']['property'] = $data['fid'];

                $this->assign('data',$data);
                $this->display();
            }else{
                $this->display();
            }
        }

    } 

    //设置需求信息的状态
    /*
     *  @param status int  :   0:删除   1:正常    2:已解决
     *
     */
    public function setStatus($id = 0,$status = 1){

        $id = intval($id);
        $d_ask = D('Ask');
        $status = intval($status);

        $res = $this->d_ask->where(array('id'=>$id,'uid'=>$this->mid))->setField('status',$status);
        if($res){
            $this->ajaxReturnHandle(1,'修改成功');
        }else{
            $this->ajaxReturnHandle(0,'修改失败');
        }
    }

    /*
     * 删除一个房源
     *
     */
    public function trash($id = 0){
       $id = intval($id);
       $map = array(
           'id'=>$id,
           'uid'=>$this->mid
       );
       $res = $this->d_ask->where($map)->setField('status',0);
       if($res){
           $this->ajaxReturnHandle(1,'删除成功');
       }else{
           $this->ajaxReturnHandle(0,'删除失败');
       }
    }
}
