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
 *  普通房源控制器
 *  会员操作窗口,增加房源，编辑房源，删除房源,查看房源
 */
class ObjectController extends BaseController
{
    protected $d_object;
    protected $mid;

    //初始化方法
    public function _initialize(){
        parent::_initialize();
        //实例化模型
        $user_info = $this->user_info;
        $identity  = $user_info['identity'];
        if((!($mid = is_login()) || $identity == 0)){
          $this->success('仅认证会员可操作');
        }
         
        $this->mid = $mid;
        $this->d_object  = D('object');
    }

    /*
     *
     * 根据用户展示我的房源信息
     *
     */
    public function index($page = 0)
    {
        $map = array(
//            'type'=>0,
            'a.uid'=>$this->mid,
            'a.status'=>1,
        );

        $list = $this->d_object->getlist($page);
        $totalcount = $this->d_object->getTotalCount($map,$map);
        $this->assign('totalcount',$totalcount);
        $this->assign('list',$list);
		$this->display();
    }


    /*
     *
     * 编辑房源新增和编辑
     *
     * $fid 楼chengID
     */

    public function edit($id=0,$fid = 0,$floor=0,$style=0,$lname = '',$address = '',$area = 0,$shi=0,$ting=0,$wei=0,$originalprice = 0,$totalprice=0,$charge=0,$description='',$deal_description='',$pics = '',$kanfang_charge = 0,$files = '',$province = 0,$city = 0,$district = 0,$bid = 0,$tel = '',$ifspecial = 0,$type= 0,$huxing = 0){
        $id = intval($id);
        if(IS_POST){
            $data['fid']    = intval($fid);                //个人房源没有所属楼盘
            $data['style']  = intval($style);
            $data['floor']  = intval($floor);

            $data['lname']  = text($lname);
            $data['address']  = text($address);
            $data['area']   = ($area);
            $data['shi']    = intval($shi);
            $data['ting']   = intval($ting);
            $data['wei']    = intval($wei);

            $data['totalprice'] = floatval($totalprice);
            $data['originalprice'] = floatval($originalprice);

            $data['charge'] = intval($charge);
            $data['kanfang_charge'] = intval($kanfang_charge);
            $data['description'] = text($description);
            $data['deal_description'] = text($deal_description);
            $data['type']   = 0; //标记为个人房源 
            $data['uid']    =  $this->mid;
            $data['status'] = 1;
            $data['pics']    = $pics;
            $data['pic_num']  =  count(explode(',',$pics));
            $data['province']   = $province;
            $data['city']   = $city?$city:session('user_city.city_id');
            $data['district']   = $district;
            $data['files']   = $files;

            $data['type'] = intval($type);
            $data['ifspecial'] = intval($ifspecial);
            $data['huxing'] = intval($huxing);

            $data['tel']   = text($tel);
            $data['bid']   = $bid;    //CBD, 归属为哪个商圈
            //为了优化搜索，此处添加title冗余,并添加索引

            $data['title'] = $data['lname'].' '.$data['floor'].'楼 '.$data['area'].'平 '.$data['shi'].'室 '.$data['totalprice'].'万';

            if($id){
                $data['id'] = $id;
                //编辑一个房源
                if(!($this->mid == $this->d_object->where(array('id'=>$data['id']))->getField('uid'))&&!is_administrator()){
                    $this->error('对不起，您的权限不足');
                }
                $data['uptime'] = time();
                if($this->d_object->savePic($id,$data['pics'])||$this->d_object->save($data)){
                    D('shuo')->where(array('uid'=>is_login()))->save(array('uptime'=>NOW_TIME));//更新说说时间，也就是更新主页信息
                    $this->ajaxReturnHandle(1,'编辑房源成功',U('home/object/index',array('uid'=>$this->mid)));
                }else{
                    $this->ajaxReturnHandle(0,'编辑房源失败');
                }
            }else{

                //新增一个房源
                $data['createtime'] = $data['uptime'] = time();
                if($id = $this->d_object->add($data)){
                    $this->d_object->savePic($id,$data['pics']);

                    //更新说说数据
                    D('shuo')->where(array('uid'=>is_login()))->setInc('count',1);
                    D('shuo')->where(array('uid'=>is_login()))->save(array('uptime'=>NOW_TIME));//更新说说时间，也就是更新主页信息

                    //更新楼盘表数据
                    D('property')->where(array('id'=>$data['fid']))->setInc('objectcount',1);
                    D('property')->where(array('id'=>$data['fid']))->save(array('uptime'=>NOW_TIME));//更新说说时间，也就是更新主页信息

                    $this->ajaxReturnHandle(1,'新增房源成功',U('home/object/index',array('uid'=>$this->mid)));
                }else{
                    $this->ajaxReturnHandle(0,'新增房源失败');
                }
            }
        }else{
            if($id){
                $data = $this->d_object->alias('a')->where(array('id'=>$id,'uid'=>$this->mid))->field('a.*,group_concat(b.pid) as pics')->join('__OBJECT_PIC__  b on a.id = b.oid','left')->find();
                if(!$data){
                    $this->error('错误');
                }

                $data['cbd'] = get_whole_address($data['bid']);//根据cbd，找到其父菜单
                $data['cbd']['property'] = $data['fid'];

                $this->assign('data',$data);
                $this->display();
            }else{
                //认证楼盘穿一个他的楼盘id 地址集过去，默认显示他的楼盘地址
                //先判断身份,再取值
                $user_info = $this->user_info;
                $auth_role = $user_info['auth_role'];
                if($auth_role == 4){
                    //更具property表中的uid找到id，再利用get_whole_address
                    $data['cbd'] = get_whole_address_by_uid(is_login());
                }


                $this->assign('data',$data);
                $this->display();
            }
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

       $res = $this->d_object->where($map)->setField('status',0);
       $fid = $this->d_object->where($map)->getField('fid');

       if($res){
           D('shuo')->where(array('uid'=>is_login()))->setDec('count',1);//说说－1
           D('property')->where(array('id'=>$fid))->setDec('objectcount',1); //楼盘－1
           $this->ajaxReturnHandle(1,'删除成功',U('home/object/index',array('uid'=>$this->mid)));
       }else{
           $this->ajaxReturnHandle(0,'删除失败');
       }
    }

}
