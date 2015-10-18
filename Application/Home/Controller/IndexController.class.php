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
 * 展示控制器
 * 展示主页,楼盘详情，房源详情
 * 
 */

class IndexController extends BaseController
{
    protected $d_object;
    protected $d_ask;
    protected $mid;


    //初始化方法
    //控制权限，只有认证的机构用户可以访问该控制器
    public function _initialize(){
        parent::_initialize();
        //实例化模型
        $this->d_ask = D('ask');
        $this->d_object  = D('Object');
        $this->mid       = is_login();
    }

    //展示房源内容
    public function objectShow($id=0){

       $id = intval($id);
       $data =  $this->d_object->getDetail($id);

       $this->assign('data',$data);
       $this->display();

    }

    //展示房源列表
    /*
    public function objectList($page = 0){

        $list = $this->d_object->getList($map = '',$page);
        $totalcount = $this->d_object->getTotalcount();
        $this->assign('totalcount',$totalcount);
        $this->assign('list',$list);
        $this->display();
    }

    /*
     * 增加说说方法
     *  没有找到访问wiget方法，所以将widget中的add展示迁移到index中
     */
    public function addShuo($content=''){
            $d_shuo = D('Shuo');
            $data =  $d_shuo->create();
            $res =  $d_shuo->add($data);

            if($res){
                $this->ajaxReturnHandle('1','发布说说成功');
            }else{
                $this->ajaxReturnHandle('0','发布说说失败');
            }
    }

    //展示需求列表
    public function askList($page = 0){
        $list = D('ask')->getlist($page);

        $this->assign('list',$list);
        $this->display();

    }
    //展示需求内容
    public function askShow($id=0){
        $id = intval($id);
        $data = D('ask')->getdetail($id);
        
        $price_table =  C('HOUSE_PRICE_TABLE');
        $data['totalprice'] = $price_table[$data['totalprice']];

        $huxing_table =  C('HOUSE_HUXING_TABLE');
        $data['shi'] = $huxing_table[$data['shi']];

        $area_table =  C('HOUSE_AREA_TABLE');
        $data['area'] = $area_table[$data['area']];

        $this->assign('data',$data);
        $this->display();

    }

    //变更城市
    /*
     * 默认城市设置为威海
     *
     */

    public function changeCity($city_id = 371000  ){
        $city_id  = intval($city_id);
        $city = M('district')->where(array('id'=>$city_id))->getField('name');
        if(!$city){
            $this->error('该城市不存在');
        }
        session('user_city',array('city_name'=>$city,'city_id'=>$city_id));
        $this->redirect();
    }

    /*
     *
     * 搜索方法 TODO 这个要好好研究研究
     *
     *
     */

     public function search($keywords = '',$page = 0){

         $type = I('post.type',0,'intval');

         if($type == 1){
             $this->propertySearch($keywords);
             exit;
         }

         $d_object   = $this->d_object;
         $d_ask      = D('ask');

         $keywords    = html(text($keywords));
         $keywords = array_filter(explode(' ',$keywords));
         /* 拼接sql 开始  */
         foreach($keywords as $k=>$v){
               $tmp = array('like','%'.$v.'%');
               
               $o_map['title'][]       = 
               $o_map['description'][] = 
               $a_map['description'][] =
               $a_map['title'][] = $tmp;
         }


               $o_map['title'][]       = 
               $o_map['description'][] = 
               $a_map['description'][] = 
               $a_map['title'][] = 'OR';
               $a_map['_logic'] = $o_map['_logic'] = 'OR';

               $a_where['_complex'] = $a_map;
               $o_where['_complex'] = $o_map;

               $a_where['a.status'] = $o_where['a.status'] = 1;

               //房源列表
               //$o_list = $d_object->where($o_map)->select();
               //咨询列表
               /*
               $a_list = $d_ask->where($a_map)->select();
               $o_list = $d_object 
                 ->having($o_map)
                 ->alias('a')
                 ->field("a.id,concat(lname,' ',floor,'楼',' ',area,'平',' ',shi,'室','',totalprice,'万',' ') as title ,1 as type,a.uptime,description,b.show_role")
                 ->join('__MEMBER__ b on a.uid = b.uid','left')
                 ->select();

               foreach($a_list as &$val){
                   $val['description'] = msubstr($val['description'],0,50);
               }
               */

               $list =   $d_object->getSearchList($o_where,$a_where,$page);

               if(IS_AJAX){

                    $html = $this->objectlist_to_html($list);
                    if($html){
                        $this->ajaxreturn(array('status'=>1,'html'=>$html));
                    }else{
                        $this->ajaxreturn(array('status'=>0));
                    }

               }

               $this->assign('list',$list);
               $this->display();
     }

     public  function propertySearch($keywords){

          
             $keywords    = html(text($keywords));
             $keywords = array_filter(explode(' ',$keywords));

             /* 拼接sql 开始  */

             foreach($keywords as $k=>$v){

                 //c   property 
                 //d   object 
                   $tmp = array('like','%'.$v.'%');
                   
                   $map['c.name'][]  = 
                   $map['b.nickname']=
                   $map['d.title'][] = $tmp;

             }

                   $map['c.name'][]  = 
                   $map['b.nickname'][]  = 
                   $map['d.title'][] =  'OR';
                   $map['_logic'] = 'OR';

                   $where['_complex'] = $map;

                   $where['d.status'] = 1;



             $list = D('shuo')->getList($where);
             $this->filter_menu($cbd,$price,$area,$shi,$type);

             $this->assign('shuo_list',$list);
             $this->display('index');
     }

     /*
      * 给一个房源打分
      *@parem $id  intval 项目id
      *@param $number intval 分数 (1-5)
      *
      */
     public function addScore($id = 5,$number = 3){
         $d_score = D('score');
         $id = intval($id);
         $number = intval($number);
         if($number>5||$number<1){
             $this->error('分数超出范围');
         }

         //控制不能重复打分
         $if_already = $d_score->field('id')->where(array('uid'=>$this->mid,'oid'=>$id))->find();

         if($if_already){
             $this->ajaxReturnHandle(0,'您已经评价过了');
             exit;
         }

         $data['oid'] = $id;
         $data['score'] = $number;
         $data['uid'] = $this->mid;
         $data['createtime'] = time();
         
         $res = $d_score->addScore($data);
         if($res){
             $this->ajaxReturnHandle(1,'评论成功');
         }else{
             $this->ajaxReturnHandle(2,'评论失败');
         }
     }

    /*
     * 下载文件
     *@param $oid intval 项目id
     *
     * 判断权限,认证提示正在准备下载,无权限提示去认证
     * 
     */
    public function  downloadObject($id = 0){
        $id = intval($id);
//        if(in_array($this->user_info['auth_role'],array(3,4))&&!$oid){
//      只要登陆了就可以下载
        if(is_login()){
            $url = U('Core/file/downloadObject',array('id'=>$id));
            $this->ajaxReturnHandle(1,'正在准备下载图片压缩包',$url);
        }else{
            $this->ajaxReturnHandle(0,'清先登陆');
        }
    }

    /*
     * 筛选 
     * $type 1  区域筛选
     *       2  总价筛选
     *       3  厅室
     *       4  面积
     *
     *
     */
    public function filter($page =0,$cbd=0,$shi=0,$area=0,$price=0){


          $d_object = $this->d_object;
          $city = $this->city_id;


          
          $list = $this->d_object->getlist($page);


          $this->filter_menu($cbd,$price,$area,$shi);
          $this->assign('list',$list);
          $this->display();
    }

    //   主页
    /*
     *
     *
     */
    public function index($page = 0, $cbd = 0, $area = 0,$shi = 0,$price = 0,$type = 0){

        $d_broker = D('broker');

        //$res = D('broker')->addBroker(0,0,18660352919);
        //exit;
        /*
        $src = getThumbImageById(25,50,50);
        dump($src);

        $img ="<img src='".$src."' />";
        $this->show($img);
        exit;
        */

       if((!I('get.tel','')) && is_login() && $this->user_info['identity']!=0){
           $this->redirect('Home/index/index',array('tel'=>($this->user_info['mobile'])));
       }

       $city = $this->city_id?$this->city_id:371000;

          /*
          $d_object = $this->d_object;
          $cbd    = intval($cbd);
          $price  = intval($price) ;
          $area   = intval($area);
          $shi    = intval($shi);
          $type    = intval($type);

          $list = $this->d_object->getList($map,$a_map,$page);
          $totalcount = $this->d_object->getTotalcount($map,$a_map);

          $this->assign('list',$list);
          $this->assign('totalcount',$totalcount);
          */

          ////////////////////////////////////////////////////////////////////////////
          
          $d_shuo = D('shuo');
          //筛选区域
          
          if($cbd == 0){
              $map['a.city'] = $city;
          }elseif($cbd>0){
              //$map['c.pid'] = $cbd;
              $map['a.pid'] = $cbd;
          }elseif($cbd<0){
              $map['a.district'] = -$cbd;
          }

          $price_map = C('HOUSE_PRICE_MAP');
          $area_map = C('HOUSE_AREA_MAP');
          $shi_map = C('HOUSE_HUXING_MAP');
          $type_map = C('HOUSE_TYPE_MAP');

          //筛选价格
        
          if($shi >0){
              $map['d.shi'] = $shi_map[$shi];
          }

          if($area>0){
              $map['d.area'] = $area_map[$area];
          }

          if($price>0){
              $map['d.totalprice'] = $price_map[$price];
          }

          if($type>0){
              $map['a.type'] = array('like',$type_map[$type]);
          }

          //$map['a.status'] = 1;
          //$map['a.title']=  array('neq','');

          //$list = $d_shuo->getlist($map,$page);  //原从说说中更新主页信心
          //现从楼盘列表中更新首页  propertyModel
          $list = D('property')->getlist($map);
          $this->filter_menu($cbd,$price,$area,$shi);

          $this->assign('shuo_list',$list);
          $this->display('index');
    }


    //此方法只处理筛选菜单展示，不处理数据
    protected function filter_menu($cbd = 0,$price = 0,$area = 0,$shi = 0,$type = 0){

          $city = $this->city_id;

          /*
          $cbd    = intval($cbd);
          $price  = intval($price);
          $area   = intval($area);
          $shi    = intval($shi);
          */

          $price_table = C('HOUSE_PRICE_TABLE');
          $area_table  = C('HOUSE_AREA_TABLE');
          $shi_table   = C('HOUSE_HUXING_TABLE');
          $type_table   = C('HOUSE_TYPE_TABLE');


          if(!($cbd_table_orignal = S('cbd_table_orignal'.$city))){
                $cbd_table_orignal   = D('cbd')->get_cbd_table($city);
                S('cbd_table_orignal'.'city',$cbd_table_orignal,7200);
          }
          if(!($cbd_table = S('cbd_table_'.$city))){
                $cbd_table   = list_to_tree($cbd_table_orignal,'id','pid','_',$city);
                S('cbd_table_'.'chty',$cbd_table);
          }

          $filter_menu = array(
              'cbd'  =>$cbd == 0?$this->city_name:$cbd_table_orignal[abs($cbd)]['name'],
              'price'=>$price_table[$price],
              'area' =>$area_table[$area],
              'shi'  =>$shi_table[$shi],
              'type'  =>$type_table[$type],
          );

          $common_url = array(
              'cbd'  =>$cbd,
              'price'=>$price,
              'area' =>$area,
              'shi'  =>$shi,
              'type'  =>$type,
              'tel'  =>I('get.tel'),
          );

          $tree = array();
          foreach($cbd_table as $k=>$v){
              $tree[$v['id']] = $v;
          }

          $this->assign('cbd_table',$tree);
          $this->assign('cbd_table_json',json_encode($tree));
          $this->assign('filter_menu',$filter_menu);
          $this->assign('common_url',$common_url);
    }


    //加载更多说说
    /*
     * $page intval 页面
     *
     */
    public function addMoreProperty($page = 0,$shi = 0,$area = 0,$price = 0,$area = 0,$cbd = 0){

          $price_map = C('HOUSE_PRICE_MAP');
          $area_map = C('HOUSE_AREA_MAP');
          $shi_map = C('HOUSE_HUXING_MAP');

        //筛选区域
          $city = $this->city_id;
        
          if($cbd == 0){
              $map['a.city'] = $city;
          }elseif($cbd>0){
              $map['a.pid'] = $cbd;
          }elseif($cbd<0){
              $map['a.district'] = -$cbd;
          }

        //筛选价格
          if($shi >0){
              $map['d.shi'] = $shi_map[$shi];
          }

          if($area>0){
              $map['d.area'] = $area_map[$area];
          }

          if($price>0){
              $map['d.totalprice'] = $price_map[$price];
          }

       //   $map['a.status'] = 1;
          //$map['a.title']=  array('neq','');

        $page = intval($page);
        $list = D('property')->getlist($map,$page);//获取数据

        $html = $this->shuolist_to_html($list);//拼接html

        if($html){
            $this->ajaxreturn(array('status'=>1,'html'=>$html));
        }else{
            $this->ajaxreturn(array('status'=>0));
        }
    }


    //加载更多房源
    /*
     * $page intval 页面
     *
     */
    public function addMoreObject($page = 0){

        $page = intval($page);
        $list = D('object')->getlist($page);//获取数据
        $html = $this->objectlist_to_html($list);//拼接html

        if($html){
            $this->ajaxreturn(array('status'=>1,'html'=>$html));
        }else{
            $this->ajaxreturn(array('status'=>0));
        }

    }

    protected function shuolist_to_html($list){
        $this->assign('shuo_list',$list);
        $html = $this->fetch('Public/propertyitem');
        return $html;
    }

    protected function objectlist_to_html($list){
        $this->assign('list',$list);
        $html = $this->fetch('Public/myitem');
        return $html;
    }

    //临时操作类
    /*
    public function sqlac(){
       $res =   D('cbd')->field('id,pid')->select();
       $i = 0;
       foreach($res as $k=>$v){
         $r =    D('property')->where(array('pid'=>$v['id']))->save(array('district'=>$v['pid']));
         $i++;
         $re = $r?'成功':'失败';
         echo $i.$re.'<br/>';
       }
    }
    */
    
} 
