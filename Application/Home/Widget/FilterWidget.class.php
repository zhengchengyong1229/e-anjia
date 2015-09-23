<?php
namespace Home\Widget;
use Think\Action;
/*
 *    数据过滤wiget
 */
class FilterWidget extends Action
{
    /*
     * $Pid 城市id
     */
      public function index(){


          $city_info = session('user_city');
          $city = $city_info['city_id'];

          $cbd    = I('cbd',0,'intval');
          $price  = I('price',0,'intval');
          $area   = I('area',0,'intval');
          $shi    = I('shi',0,'intval');

          $cbd_table_orignal   = D('cbd')->get_cbd_table($city);
          $cbd_table   = list_to_tree($cbd_table_orignal,'id','pid','_',$city);

          $price_table = C('HOUSE_PRICE_TABLE');
          $area_table  = C('HOUSE_AREA_TABLE');
          $shi_table   = C('HOUSE_HUXING_TABLE');


          $filter_menu = array(
              'cbd'  =>$cbd == 0?$city_info['city_name']:$cbd_table_orignal[abs($cbd)]['name'],
              'price'=>$price_table[$price],
              'area' =>$area_table[$area],
              'shi'  =>$shi_table[$shi],
          );

          $common_url = array(
              'cbd'  =>$cbd,
              'price'=>$price,
              'area' =>$area,
              'shi'  =>$shi,
          );


          $tree = array();
          foreach($cbd_table as $k=>$v){
              $tree[$v['id']] = $v;
          }

          $this->assign('tree',$tree);
          $this->assign('price_table',$price_table);
          $this->assign('area_table',$area_table);
          $this->assign('shi_table',$shi_table);
          $this->assign('cbd_table',$tree);
          $this->assign('cbd_table_json',json_encode($tree));
          $this->assign('filter_menu',$filter_menu);
          $this->assign('common_url',$common_url);

          $this->display('Widget/filter');
      }
} 
