<?php
namespace Home\Model;
use Think\Model;
/**
 * 楼盘模型 
 */
class PropertyModel extends Model{
    //获取楼盘列表
    /*
     *   主页展示楼盘列表
     *   规则  条件城市,房源数量,更新时间  
     *   
     *   头像 ，手工上传
     *
     */
    public function getlist($map,$page = 0 ,$order = 'objectcount desc', $r = 10 ){
        $field = 'distinct a.id,a.name as title,a.type,a.objectcount,a.uptime,a.cover,CONCAT(f.name,"-",e.name) as area,g.max_area,g.min_area,g.uprice';
        //$objectSql  = ->table('object')->field('max(area) as max_area,min(area) as min_area,min(`totalprice`/`area`*10000) as uprice')->where(array('status'=>1))->buildSql();
        $objectSql = 'select fid,max(area) as max_area,min(area) as min_area,min(`totalprice`/`area`*10000) as uprice from fang_object where status = 1 group by fid';

        $list =  $this->where($map)->field($field)->alias('a')
                 ->join('__OBJECT__ d on d.fid = a.id','left')   //房源搜索条件
                 ->join('('.$objectSql.') g on g.fid = a.id','left')   //房源搜索条件
                 ->join('__CBD__  e on a.pid = e.id','left')   //房源搜索条件
                 ->join('__CBD__  f on a.district = f.id','left')   //房源搜索条件
                 ->order('a.uptime desc')
                 ->page($page,$r)
                 ->select();

        foreach($list as $key=>$val){
            $list[$key]['type']    =  implode(' ',array_map(array(__CLASS__,'_type2spans'),explode(',',$val['type'])));
            $list[$key]['max_area'] =  intval($val['max_area']);
            $list[$key]['min_area'] =  intval($val['min_area']);
            $list[$key]['uprice']   =  intval($val['uprice']);
        }
        return $list;
    }

    public function getDetail($id){
        return $this->find($id);
    }
    
    private function  _type2spans($num){
         return C('PROPERTY_SPAN.'.$num);
    }

}
