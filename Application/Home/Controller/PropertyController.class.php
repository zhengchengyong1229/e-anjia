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
 * 认证机构管理控制器
 * 
 */
class PropertyController extends BaseController
{
    protected $d_property;
    protected $d_object;
    protected $mid;


    //初始化方法
    public function _initialize(){
        
        parent::_initialize();
        //实例化模型
        $this->d_property = D('Property');
        $this->d_object  = D('Object');
        $this->mid = is_login();

        //控制权限
        if(!check_auth('Property')){
           // $this->error('对不起，只有认证的机构用户可以访问该页面');
        } 
    }

    //某个楼盘下的内容，包括该楼盘下的房源和该楼盘的促销活动
    /*
     * $id intval 楼盘ID
     */
    public function index($id = 0)
    {
        $id = I('get.id',0,'intval');//修正我的房源

        //取出促销
        $d_shuo = D('shuo');
        $shuo_data = $d_shuo->getdetail(array('a.property'=>$id));



        $this->assign('shuo_data',$shuo_data);

        $d_object = D('object');
        $list = $this->d_object->getList();

        $this->assign('list',$list);
        $this->display();
    }

    public function my(){

    }

    //可以改善
    /*
     *  只有楼盘可以修改
     *
     */
    public function editShuo(){
        if(IS_POST){

                if(!is_login()){
                    $this->success('请先登陆');
                }

                $d_shuo = D('shuo');
                $data = $d_shuo->create();
                if(utf8_strlen($data['title'])>15){
                    $this->error('标题不能超过15个字');
                }

                if($d_shuo->where(array('uid'=>is_login()))->save($data)){
                    $this->ajaxReturnHandle(1,'修改成功',U('Home/property/index',array('id'=>$this->mid)));
                }else{
                    $this->ajaxReturnHandle(0,'修改失败');
                }

        }else{
                $data = D('shuo')->where(array('uid'=>is_login()))->find();
                $data['sTime'] = $data['sTime']?date('Y-m-d',$data['sTime']):'';
                $data['eTime'] = $data['dTime']?date('Y-m-d',$data['eTime']):'';

                $this->assign('data',$data);
                $this->display();
        }
    }

    //添加一个楼盘
    public function editProperty($id=11,$name='富城',$province=100,$city=99,$district=98,$community=97,$cover=10,$mobile=18765908797,$description = '我是一段'){
        $id = intval($id);
        if(IS_POST){

                $data['name']=text($name);
                $data['province']=intval($province);
                $data['city']= intval($city);
                $data['district'] = intval($district);
                $data['community'] = intval($community);
                $data['cover'] = intval($cover);
                $data['mobile'] = intval($mobile);
                $data['description'] = text($description);
                $data['uid']  = $this->mid;
                $data['status'] = 1;
                
                if($id){
                    $data['id'] = $id;
                    $data['uptime'] = time();
                    //防治恶意篡改信息，管理员除外,TODO,看看别的应用是如何处理这个问题的
                    if(!($this->mid == $this->d_property->where(array('id'=>$id))->getField('uid'))&&!is_administrator()){
                        $this->error('对不起，您无权修改');
                    }


                    if($this->d_property->save($data)){
                        $this->ajaxReturnHandle(1,'编辑楼盘成功',U('Property/index'));
                    }else{
                        $this->ajaxReturnHandle(0,'编辑楼盘失败');
                    }
                }else{
                    $data['createtime'] = time();

                    if($this->d_property->add($data)){
                        $this->ajaxReturnHandle(1,'新增楼盘成功',U('Property/index'));
                    }else{
                        $this->ajaxReturnHandle(0,'新增楼盘失败');
                    }
                }
        }else{
            if($id){
                $this->display();
            }else{
                echo '展示新增楼盘页面';
            }
        }
    } 

    /*
     * 展示某一楼盘下房源列表
     *
     */
    public function show($id = 7){
        $id = intval($id);

        $map = array(
            'uid'=>$this->mid,
            'lid'=>$id,
            'type'=>1,//只取出通过楼盘找到的房源
            'status'=>1,
        );

        echo "<a href=".U('editObject',array('id'=>3)).">编辑一个房源</a><br>";
        echo "<a href=".U('editObject').">新增一个房源</a>";

        $list = $this->d_object->getlist('id,uid,lid,floor,type,layout,totalprice,charge,createtime,uptime',$map,$order);
        dump($list);
    }
    //添加房源
    /*
     * @param $type      tinyint  1或0  个人：0  机构：1  此处强制为1 
     * @param $layout    tinyint  户型 TODO  暂时从配置文件中去，以后考虑加到后台配置文件中去 
     * 
     */
    public function editObject($id=0,$lid=7,$floor=2,$layout=3,$totalprice=1880,$charge=80,$description='我是房源的描述'){

        $id = intval($id);
        if(!IS_POST){

            $data['lid']    = intval($lid);
            $data['floor']  = intval($floor);
            $data['layout'] = intval($layout);
            $data['totalprice'] = intval($totalprice);
            $data['charge'] = intval($charge);
            $data['description'] = text($description);
            $data['type']  = 1; //标记为机构房源 
            $data['uid']   =  $this->mid;
            $data['status'] = 1;

            if($id){
                $data['id']     = $id;
                //编辑一个房源
                if(!($this->mid == $this->d_object->where(array('id'=>$data['id']))->getField('uid'))&&!is_administrator()){
                    $this->error('对不起，您的权限不足');
                }
               $data['uptime'] = time();
                if($this->d_object->save($data)){
                    $this->ajaxReturnHandle(1,'编辑房源成功',U('Property/index'));
                }else{
                    $this->ajaxReturnHandle(0,'编辑房源失败');
                }
            }else{
                //新增一个房源
                $data['createtime'] = time();

                if($this->d_object->add($data)){
                    $this->ajaxReturnHandle(1,'新增房源成功',U('Property/index'));
                }else{
                    $this->ajaxReturnHandle(0,'新增房源失败');
                }
            }
        }else{
            if($id){
                echo '房源编辑页面';
                $this->display();
            }else{
                echo '展示新增一个房源的页面，隐藏传入楼盘参数';
            }

        }
    }

    //删除一个楼盘
    /*
     * status: 1 为正常（默认)   0为禁用
     *
     */

    public function trashProperty($id=0){
        $id = intval($id);
        $map = array(
            'id'=>$id,
            'uid'=>$this->mid,
        );

       $res = $this->d_property->where($map)->setField('status',0);
       if($res){
           $this->ajaxReturnHandle(1,'删除成功');
       }else{
           $this->ajaxReturnHandle(0,'删除失败');
       }
    }

    //删除一个房源
    public function trashObject($id=0){
        $id = intval($id);
        $map = array(
            'id'=>$id,
            'uid'=>$this->mid,
        );

       $res = $this->d_object->where($map)->setField('status',0);
       if($res){
           $this->ajaxReturnHandle(1,'删除成功');
       }else{
           $this->ajaxReturnHandle(0,'删除失败');
       }
    } 

    //房源主页中加载更多方法
    public function addMoreObject($page = 0,$uid = 0){
        $uid = intval($uid);
        $page = intval($page);
        $list = D('object')->getlist($page);//获取数据
        $html = $this->objectlist_to_html($list);//拼接html
        if($html){
            $this->ajaxreturn(array('status'=>1,'html'=>$html));
        }else{
            $this->ajaxreturn(array('status'=>0));
        }
    }

    protected function objectlist_to_html($list){
        $this->assign('list',$list);
        $html = $this->fetch('Public/item');
        return $html;
    }
}
