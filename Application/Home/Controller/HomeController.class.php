<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */
namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


/*
 *   后台主页所有后台管理的方法都放在这里，思考其后期可维护性
 *
 */
class HomeController extends AdminController
{
    public function index(){
       //    display 会显示模版不存在,动态生成所有视图,你妹的，压力山大
      //    $this->display();
    }

    /*
     *  认证管理   
     *  auth
     *  memberAuth  //会员认证
     *  orgAuth     //会员认证
     */

     //memberAuth  展示未认证界面,取出所有未认证的人,不分角色
     public function memberAuth(){
         $d_apply = D('apply');

         $map['a.status'] = 2 ; //2表示待审核
         $map['to_auth']   = 2 ; //2表示普通会员
         $list = $d_apply->alias('a')->field('a.*,b.nickname,c.mobile,a.createtime')->where($map)->join('__MEMBER__  as b on a.uid = b.uid','left')
         ->join('__UCENTER_MEMBER__ as c on a.uid = c.id','left')
         ->select();

         $builder = new AdminListBuilder();
         $builder->title('未认证管理');
         $builder->setStatusUrl(U('setAuthStatus'))
                 ->keyId()->keyLink('nickname','昵称','authPicture?id=###')->keyText('mobile','电话')->keyCreateTime('createtime')
                 ->keyDoAction('doMemberAuth?id=###','审核');
                         //操作按钮，一个通过，一个拒绝//TODO
                         // ->keyDoAction('', '通过审核')
                         // ->keyDoAction('', '拒绝');
         $builder->data($list);
         $builder->display();
     }

     //配置申请用户的状态
     /*
      *  通过审核，拒绝审核
      *  配置身份：经纪人,中介,楼盘
      *  如果是楼盘：将楼盘表配置到该用户身上,将该用户绑定到配置表上，双向绑定,后期做优化取舍 
      *
      *  $id intval apply id 
      *
      */
     public function doMemberAuth($id = 0){
         if(IS_POST){
             //实际配置
             /*
              *
              *   第一步判断是否通过审核,如果通过,修改apply status ＝ 1,不然修改apply status ＝ 0(通知没有通过审核)
              *
              *   如果通过审核,判断身份,
              *
              *   如果是楼盘，并且传回了property ID 则在
              *
              *   将身份信息存入broker,
              *
              */

              $id     = I("post.id",0,'intval');
              $status = I("post.status",0,'intval');
              $identity = I("post.identity",0,'intval');
              $fid    = I("post.fid",0,'intval');
              $uid    = I("post.uid",0,'intval');



              if($status == 1){
                  //在申请表中标记通过时间,状态
                  $res = D('apply')->where(array('id'=>$id))->save(array('status'=>1,'uptime'=>NOW_TIME));
                  if($res){
                      $res = D('Broker')->addIdentity($uid,$identity);

                      if($res){
                          $this->success('认证成功');
                      }else{
                          $this->error('认证失败');
                      }

                  }else{
                      $this->error('审核失败');
                  }
              }else if($status == 0){
                  $res = D('apply')->where(array('id'=>$id))->setField('status',0);
                  if($res){
                      $this->success('已拒绝');
                  }else{
                      $this->error('拒绝失败');
                  }
              }
         }else{
            $id = intval($id);
            $data = D('apply')->alias('a')->where(array('id'=>$id))->field('a.*,b.nickname')
                    ->join('__MEMBER__ b on a.uid=b.uid','LEFT')
                    ->find();

            $this->assign('data',$data);
            $this->display();
         }
     }




/*
 *
 *
 *     认证后的列表
 *
 *
 *
 *
 */
     //broker列表
     public function brokerList(){
         $identity = I('get.identity',0,'intval');
         switch($identity){
             case 1:
                $title = "员工列表";
                break;
             case 3:
                $title = "兼职经纪人";
                break;
         }

         $map = array(
             'a.identity'=>$identity,
         );

         $list = D('Broker')->alias('a')->where($map)
                            ->field('a.id,a.uid,b.nickname,c.mobile,a.createtime,a.status')
                            ->join('__MEMBER__ b on a.uid = b.uid','left')
                            ->join('__UCENTER_MEMBER__ c on a.uid = c.id','left')
                             ->select();

         $builder = new AdminListBuilder();
         $builder->title($title);
         $builder->keyId()->keyText('nickname','昵称')->keyText('mobile','电话')->keyCreatetime('createtime')
                 ->keyDoActionEdit('Home/editBroker?id=###')
                 ->keyStatus();
         $builder->data($list);
         $builder->display();

     }


     public function propertyTrash(){
         $buidler = new AdminListBuilder();
         $buidler->display();
     }

    /*
     *  楼盘管理
     *  property
     *  propertyList    //楼盘列表
     *  propertyTrash   //已禁用
     */

     //管理用户
     public function editProperty($id = 0){
         if(IS_POST){
             $fid = I('post.fid',0,'intval');
             $uid = I('post.uid',0,'intval');

             if($fid&&$uid){
                $res =   D('property')->where(array('id'=>$fid))->setField('uid',$uid);
                if($res){
                    $this->success('配置成功');
                }else{
                    $this->error('配置失败');
                }
             }

         }else{

                 $uid = D('apply')->where(array('id'=>$id))->getField('uid');
                 $data = D('member')->alias('a')->where(array('b.id'=>$id))->field('a.nickname,a.uid,i.id as property,e.id as cbd,f.id as district,g.id as city,h.id as province,j.mobile')
                            ->join('__APPLY__ b on b.uid = a.uid','left')
                            ->join('__PROPERTY__ i on i.uid = b.uid')
                            ->join('__CBD__ e on e.id = i.pid','LEFT')
                            ->join('__CBD__ f on f.id = e.pid','LEFT')
                            ->join('__DISTRICT__ g on g.id = f.pid','LEFT')
                            ->join('__DISTRICT__ h on h.id = g.upid','LEFT')
                            ->join('__UCENTER_MEMBER__ j on j.id = b.uid','LEFT')
                            ->find();
                            //dump(D('member')->getdberror());
                            //jdump($data);
                            //exit;

                 //楼盘
                 //   用户名称，通过审核的用户
                 //   隐藏的用户ID
                 //   选择cbd
                 //   展示详细cbd, 取出一大堆数据
                 
                    $admin_config = new AdminConfigBuilder();
                    $admin_config->title('管理楼盘用户')->keyReadOnly('nickname', '名称')
                                 ->keyHidden('uid')
                                 ->keyReadOnly('mobile','电话')
                                 ->keyCBD()
                                 ->data($data)
                                 ->buttonSubmit(U('editProperty'))->buttonBack()->display();

         }
     }

    /*
     *  个人房源
     *  object
     *  objectList     //房源列表
     *  objectTrash    //已禁用
     */
    //objectList 指的是供给房源
     public function objectList(){
         $buidler = new AdminListBuilder();
         $buidler->display();
     }
     
    //objectAskList 指的是需求房源
     public function objectAskList(){
         $buidler = new AdminListBuilder();
         $buidler->display();
     }

     public function objectTrash(){
         $buidler = new AdminListBuilder();
         $buidler->display();
     }



    //展示执照照片
    /*
     *@param $id 用户id
     *
     */
    public function authPicture($id){
        $license = D('apply')->where(array('id'=>$id))->getField('license');

        header('Location:'.getOrignalImageById($license));
    }
   
    /*
     * 商圈管理
     *
     *
     */

    //展示所有商圈
    /*
     * pid intval 父ID
     *
     */
    public function cbd($pid = 0,$upid = 0,$city = 0,$level = 0){

         $pid = intval($pid);
         $builder = new AdminListBuilder();
         if($level == 2){
            $builder->title('楼盘列表');
         }else{
            $builder->title('商圈列表');
         }

             //取出省
         if($pid == 0&&$upid == 0){
             $map = array(
                 'level'=>1    
             );

             $href = 'cbd?upid=###';

             $list = D('district')->where($map)->field('id,name')->select();
         }elseif($pid == 0){
             // 取出市
             $map = array(
                 'upid'=>$upid  //upid 市
             );

             $href = 'cbd?pid=###&city=###';

             $list = D('district')->where($map)->field('id,name')->select();

         }elseif($level < 2){
             //取出市以下,展示区和cbd
             $map = array(
                 'pid'=>$pid
             );

             //添加新增按钮
             $level = D('cbd')->where(array('id'=>$pid))->getField('level');
             $level = $level+1; //level指当前数据的level


             $builder ->buttonNew(U('CBDEdit',array('pid'=>$pid,'city'=>$city,'level'=>$level)));
             $href = 'cbd?pid=###&level='.$level.'&city='.$city;
             $list = D('cbd')->where($map)->field('id,name')->select();

         }elseif($level == 2){

             //此处要添加具体楼盘
             $map = array(
                 'pid'=>$pid
             );

             //添加楼盘按钮
             $builder ->buttonNew(U('PropertyEdit',array('pid'=>$pid,'city'=>$city)));
             $href = 'propertyEdit?id=###';
             $list = D('property')->where($map)->field('id,name')->select();

         }


         $builder->setStatusUrl(U('setCBDStatus'))
                 ->keyId()
                 ->keyLink('name','区域',$href);

         $builder->data($list);
         $builder->display();
    }

    //新增修改商圈信息 
    public function CBDEdit($id = 0,$pid = 0,$city = 0){
        $pid = intval($pid);
        $id = intval($id);


        if(IS_POST){
            $d_CBD = D('cbd');

            $data = $d_CBD->create();
            if($id){
                if($d_CBD->save($data)){
                    $this->success('编辑成功');
                }else{
                    $this->error('编辑失败');
                }
            }else{
                if($d_CBD->add($data)){
                    $this->success('新增成功');
                }else{
                    $this->error('新增失败');
                }
            }
        }else{
            //区域，如环翠
            if($pid!=0&&$city!=0&&$pid == $city){
                $data['level'] = 1;
            }else{
            //商圈，如火车站
                $data['level'] = 2;
            }

            if($id){
                $data = D('cbd')->find($id);
            }
            $data['pid']  = $pid;
            $data['city'] = $city;
            $admin_config = new AdminConfigBuilder();
            $admin_config->title('新增商圈')->keyId()->keyText('name', '名称')
                         ->keyHidden('pid')
                         ->KeyHidden('city')
                         ->KeyHidden('level')
            //             ->keyReadOnly('pname','父区域')
                         ->data($data)
                         ->buttonSubmit(U('CBDEdit'))->buttonBack()->display();
        }
    }

    //添加楼盘
    public function propertyEdit($id = 0,$pid = 0,$city = 0){

        $pid = intval($pid);
        $id = intval($id);
        $city = intval($city);


        if(IS_POST){

            $d_property = D('property');
            $data = $d_property->create();
            if($id){
                if($d_property->save($data)){
                    $this->success('编辑成功');
                }else{
                    $this->error('编辑失败');
                }
            }else{
                if($d_property->add($data)){
                    $this->success('新增成功');
                }else{
                    $this->error('新增失败');
                }
            }
        }else{

            $data['pid']  = $pid;
            $data['city'] = $city;

            if($id){
                $data = D('property')->find($id);
            }

            $admin_config = new AdminConfigBuilder();
            $admin_config->title('新增楼盘')->keyId()->keyText('name', '名称')
                         ->keyText('nei_name','内部名字')
                         ->keyText('nei_tel','内部电话')
                         ->keyText('gong_name','外部名字')
                         ->keyText('gong_tel','外部电话')
                         ->keyUid('uid','用户ID')
                         ->keyHidden('pid')
                         ->keyHidden('city')
                         ->keySingleImage('cover','封面','首页各个楼盘的logo')
                         ->keyCheckBox('type','类型','',array(0=>'住宅',1=>'公寓',2=>'商铺',3=>'写字楼'))
                         ->data($data)
                         ->buttonSubmit(U('propertyEdit'))->buttonBack()->display();
        }

    }

    //后台分配顾问
    /*
     *  
     *
     */

    //客户列表
    //客户名，客户电话，客户描述，推荐人，推荐人电话，推荐时间，客户状态,重新分配顾问,（自页面编辑）
    public function clientList(){

        $time_filter = I('get.time_filter',0,'intval');    //时间类型，今天，昨天，范围 1,2,3
        $start_time  = strtotime(I('get.start_time',0,'strval'));     //开始时间
        $end_time    = strtotime(I('get.end_time',0,'strval'));       //结束时间
        $broker_filter = I('get.broker_filter',0,'intval');//员工
        $status_filter = I('get.status_filter',0,'intval');//状态

        //字段uptime
            if($time_filter == 3){
                //表范围
                $map['a.uptime'] = array('between',array($start_time,$end_time));

            }else if($time_filter == 1){
                //表今天
                $lingcheng =  strtotime('today');
                $map['a.uptime'] = array('between',array($lingcheng,NOW_TIME));
            }else if($time_filter == 2){

                //表昨天
                $lingcheng =  strtotime('today');
                $zuolingcheng =  $lingcheng-24*60*60;
                $map['a.uptime'] = array('between',array($zuolingcheng,$lingcheng));
            }
        //字段adviser_id
        if($broker_filter){
            $map['a.adviser_id'] = $broker_filter;
        }

        //字段status
        if($status_filter !=3){
            $map['a.status'] = $status_filter;
        }

        $list = D('client')->getAdminList($map);
        $builder = new AdminListBuilder();
        $builder->title('客户管理列表');
        $builder->keyId()
                ->keyText('client_name','客户名')  //后期改成带描述的类型
                ->keyText('client_tel','客户电话')
                ->keyText('tuijian_name','推荐人')
                ->keyText('tuijian_tel','推荐人电话')
                ->keyText('adviser_name','顾问')
                ->keyText('adviser_tel','顾问电话')
                ->keyText('client_status','客户状态')
                ->keyBrokerFilter(true)
                ->keyCreatetime('uptime')
                ->keyDoActionEdit('Home/assignAdviser?id=###');

        $builder->data($list);
        $builder->display();
        
    }


    //手动分配顾问
    /*
     *   展示基本信息, 
     *
     *
     *
     */
    public function assignAdviser($id){
        if(IS_POST){
            $id         =  I('post.id',0,'intval');         //客户表主ID
            $adviser_id =  I('post.adviser_id',0,'intval'); //顾问ID

            $res = D('client')->assignAdviser($id,$adviser_id);
            if($res){
                $this->success('修改成功',U('clientList'));
            }else{
                $this->success('修改失败');
            }
        }else{

            $data = D('client')->getDetail($id);
            $adviser_list = S('adviser_list')?S('adviser_list'):D('Broker')->getAdviserList();

            $admin_config = new AdminConfigBuilder();
            $admin_config->title('管理客户')
                         ->keyReadOnly('client_name', '客户名')
                         ->keyReadOnly('client_tel', '客户电话')
                         ->keyReadOnly('tuijian_name', '推荐人')
                         ->keyReadOnly('tuijian_tel', '推荐人电话')
                         ->keyReadOnly('client_status', '客户状态')
                         ->keySelect('adviser_id','顾问','',$adviser_list)
                         ->keyHidden('id')
                         ->data($data)
                         ->buttonSubmit(U('assignAdviser'))->buttonBack()->display();
        }

    }

}
