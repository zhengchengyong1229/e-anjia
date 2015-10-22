<?php
namespace Home\Controller;
/**
 *
 * 推荐一个客户
 *
 */
class ClientController extends BaseController
{

    //推荐一个客户
    /*
     *  推荐 ，分配？自动分配
     *
     */
    public function tuiJian(){
        if(IS_POST){

            $uid = is_login();
            $client_name = I('post.client_name','','text');
            $client_tel = I('post.client_tel','','text');
            $client_des = I('post.client_des','','text');

            if(!($client_name&&$client_tel)){
                $this->error('信息不完整');
            }

            /*    此处处理顾问   */

            $adviser_id = $this->authAssign($uid);

            /*    处理结束       */

            $res = D('Client')->addClient($uid,$client_name,$client_tel,$adviser_id);
            if($res){
                $this->success('推荐成功',U('myTuiJian'));
            }else{
                $this->error('推荐失败');
            }
        }else{
            $this->display();
        }
    }

    //我的推荐列表
    public function myTuiJian(){
        $map = array(
            'tid'=>is_login(),
        );
        $client_state_table = C('CLIENT_STATE_TABLE');
        $list = D('Client')->getlist($map);

        foreach($list as $key=>$val){
            $bb = intval($val['status']);
            $list[$key]['client_status'] = $client_state_table[$bb];
        }

        $this->assign('list',$list);
        $this->display();
    }

    //获取客户详情
    public function clientDetail($id){
        $data = D('Client')->getDetail($id);

        $this->assign('data',$data);
        $this->display();
    }

    //获取客户详情FOR 员工
    public function clientDetailForManager($id){
        $data = D('Client')->getDetail($id);
        $this->assign('data',$data);
        $this->display();
    }

    //自动分配顾问
    /*
     *
     *  原则,是什么呢 
     *
     *  注册在谁下面，就分配给谁,找到员工,无员工经纪人呢？
     *  推荐-> 1 客户名字,客户电话，需求描述
     *         2 根据推荐人分配顾问，getAdviserByUid()
     *              if false 则转入后台手动分配
     *         3 顾问终端显示客户,进行删改操作
     *
     *   原则：有员工上级？将客户分配该员工 
     *         无员工上级？将客户分配给后台
     *         随机分配  ？随机分配方法
     *  
     *  $adviser_id 是ucenter_member id
     *
     */


    public function authAssign($uid){
        // uid 表推荐者id
        //取这个人的pid，直到取到员工为止
        //方法1 用网上查询到的递归函数，亲测有效
        //方法2 在每个经纪人后面添加 adviser_id  标记
        //方法3 递归取上级直到取到标记为员工的为止.
        //TODO
        // 暂定方法：1向上查找至员工 2若无分配0
        $adviser_id = $this->getParentUntilStaff($uid);
        // $adviser_id 可能有两种情况 0 或者 非0
         
        return $adviser_id;
    }

    //管理房源主页面
    public function manageclient(){

        //权限控制 
        $user_auth = $this->user_info;
        $user_auth = $user_auth['identity'];
        // 1 表示员工 
        if($user_auth != 1){
            $this->error('权限不足');
        }

        $map = array(
            'adviser_id'=>is_login(),
            'status'=>array('gt',-1)
        );

        $list = D('Client')->getlist($map);
        $client_state_table = C('CLIENT_STATE_TABLE');

        //dump(C("CLIENT_STATE_TABLE.".strval(2)));
        
        //exit;
        //处理数据

        $this->assign('list',$list);
        $this->display();
    } 

    public function manageNext(){
        if(IS_AJAX){
            $id = I('get.id',0,'intval');

            $map = array(
                'uid'=>is_login(),
                'id' =>$id,
            );

            $res =  D('Client')->setStatus($map);
            if($res){
                $this->ajaxreturn(array('status'=>1));
            }else{
                $this->ajaxreturn(array('status'=>0));
            }
        }
    }

    //放弃一个房源
    public function managegiveup(){
        if(IS_AJAX){
            $id = I('get.id',0,'intval');

            $map = array(
                'uid'=>is_login(),
                'id' =>$id,
            );
            $res =  D('Client')->giveUp($map);

            if($res){
                $this->ajaxreturn(array('status'=>1));
            }else{
                $this->ajaxreturn(array('status'=>0));
            }
              
        }
    }

    //递归获取员工id
    //考虑把他放入模型中TODO
    //返回 员工ID 或 0
    protected  function getParentUntilStaff($uid){
        $map = array(
            'a.uid'=>$uid,
        );
        $res = D('broker')->where($map)->alias('a')->join('__BROKER__  b on a.pid = b.id','left')->field('b.uid,b.id,b.identity')->find();
        //没有找到数据，返回0 ，交给后台
        if(!$res){
            return 0;
        }else if($res['identity'] != 1){
        //没有找到员工,继续向下查找
            return $this->getParentUntilStaff($res['uid']);
        }else{
            return $res['uid'];
        }
    }
}
