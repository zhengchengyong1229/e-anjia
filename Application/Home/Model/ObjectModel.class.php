<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Model;
use Think\Model;

/**
 * 房源模型 
 */
class ObjectModel extends Model{
    //获取主页右边栏数据
    /*
	public function getHomeRightList($map,$limit = 10){
        //$map
        // 展示规则
        $map = array(
        );
        return $this->getlist(0,'id,address,lname,floor,shi,ting,wei,totalprice,charge,createtime',$map,$order,$limit);
	}
    public function getlist($page,$field,$map,$order = 'id desc',$limit = 10){
        return $this->field($field)->where($map)->order($order)->page($page,$limit)->select();
    }
    */

    /*
     * 获取列表通用方法 ,  search 调用，展示列表调用 
     * @param 一眼就能明白，此处忽略不写
     * @return 返回标准化数据，直接传给模版
     *
     * array(
     *       0 =>{
     *               'title'  =>'华夏 90平 2室 29万 ' || '靠海100平80万左右的什么'
     *               'type'   =>1 表示求购            ||  2  表示需求
     *               'label'  => 标签们，用逗号分割
     *               'uptime' => 更新时间
     *               'description'   => 描述
     *       }
     *
     * )
     *
     */
    public function getlist($page=0,$order="",$field="*",$r = 10){

                 $map = $this->createMap();
                 $a_map = $map['a_map'];
                 $map   = $map['map'];

                 $o_list = $this->alias('a')->where($map)

                   // ->page($page,$r)
                      ->field('distinct a.floor,a.huxing,a.totalprice,a.area,a.shi,a.id,title,1 as type,a.fid,a.uptime,b.nickname,c.name as loupan,pic_num,ifspecial,a.type as leibie')
                      ->join('__MEMBER__ b on a.uid = b.uid','left')
                      ->join('__PROPERTY__ c on a.fid = c.id','left')
                      ->order('leibie,shi,floor')
                      ->select();


                /*
                 $a_list = D('ask')->alias('a')->where($a_map)
                         ->page($page,$r)
                          ->field('a.id,title,2 as type,a.uptime,description,b.nickname')
                          ->join('__MEMBER__ b on a.uid = b.uid','left')
                          ->order('a.uptime desc')
                          ->select();
                $list = array_merge((array)$a_list,(array)$o_list);
                array_multisort(array_column($list,'uptime'),SORT_DESC,$list);
                $list =  array_slice($list,0,10);

                */

                return $o_list;
    }




    //将房源拼装成三维数组
    /*
     *  高层／一居
     *
     *    img      ~~~~
     *             ~~~~
     *             ~~~~
     *
     */
    public function getMultyList($page=0,$order="",$field="*",$r=10){
          $list =   $this->getlist($page=0,$order="",$field="*",$r=10);
          $new_list = array();
          foreach($list as $key=>$val){
                $new_list[strval($val['leibie']).','.$val['shi']][]=$val;
          }

//      ksort($new_list);
        return $new_list;
    }

    public function getSearchList($o_map,$a_map,$page = 0,$r = '15',$order="createtime desc"){

        /*
        $this    ->alias('a')
                 ->field("a.id,concat(lname,' ',floor,'楼',' ',area,'平',' ',shi,'室','',totalprice,'万',' ') as title ,1 as type,a.uptime,description,b.show_role")
                 ->join('__MEMBER__ b on a.uid = b.uid','left')
                 ->buildSql();
        $sql =     '('.$this->_sql().')'; 

        $o_list = $this->table($sql.'c')->where($o_map)->select();
        */

        $o_list = $this->alias('a')->where($o_map)
                  ->field('a.id,title,1 as type,a.uptime,description,b.show_role')
                  ->join('__MEMBER__ b on a.uid = b.uid','left')
                  ->page($page,$r)
                  ->select();


        /*
        $a_list = D('ask')->alias('a')->where($a_map)
                  ->field('a.id,title,2 as type,a.uptime,description,b.show_role')
                  ->join('__MEMBER__ b on a.uid = b.uid','left')
                  ->page($page,$r)
                  ->select();

          
        $list = array_merge((array)$o_list,(array)$a_list);

        array_multisort(array_column($list,'uptime'),SORT_DESC,$list);
        $list =  array_slice($list,0,10);
        */


        return $o_list;
    }

    /*
     * 取得ask 和 object 表 条件下的数据量
     *
     */

    public function getTotalCount($map,$a_map){
        $object_count = $this->alias('a')->where($map)->count();
        $ask_count    = D("ask")->alias('a')->where($a_map)->count();

        return $object_count+$ask_count;
    }

    public function getDetail($id){
      $data =  $this->alias('a')->field('a.*,concat(h.name,g.name,f.name,e.name) as cbd,group_concat(b.pid) as pics,c.mobile,d.nickname,d.show_role,a.uptime,i.policy')
                    ->join('__OBJECT_PIC__  b on a.id = b.oid ','LEFT')
                    ->join('__UCENTER_MEMBER__  c on a.uid = c.id ','LEFT')
                    ->join('__MEMBER__ d on a.uid = d.uid','LEFT')
                    ->join('__CBD__ e on e.id = a.bid','LEFT')
                    ->join('__CBD__ f on f.id = e.pid','LEFT')
                    ->join('__DISTRICT__ g on g.id = f.pid','LEFT')
                    ->join('__DISTRICT__ h on h.id = g.upid','LEFT')
                    ->join('__SHUO__ i on a.uid = i.uid','LEFT')
                    ->where(array('a.id'=>$id))
                    ->find();

      return $data;
    }

    /*
     * 更新某一个房源的平均打分值
     *@param id intval  房源ID
     *
     */
    public function setObjectAverage($id,$score){
        $this->where(array('id'=>$id))->setField('score',$score);
    }


    /*
     * 取得打分者们的平均值
     * @param  $id  intval 房源id
     */
    public function setUserAverage($id,$user_score){
        $this->where(array('id'=>$id))->setField('user_score',$user_score);
    }

    /*
     *更新那个什么什么比例的值
     *
     */
    public function setRatio($id){
        //TODO

    }

    /*
     *存入图片
     *@param intval $oid 项目图片
     *@param string $pic 新图片
     *
     * return bool 成功与否
     */
    public function savePic($oid ,$pics){
        $pics = explode(',',$pics);
        D('object_pic')->where(array('oid'=>$oid))->delete();
        foreach($pics as $k=>$v){
            if($v){
                $tmp = array(
                    'oid'=>$oid,    
                    'pid'=>$v
                );
                D('object_pic')->add($tmp);
            }
        }
    }

    /*
     *  创建object 筛选条件
     *  包含  object 和 ask
     *
     */
    protected function createMap(){

          $city = session('user_city');
          $city = $city['city_id'];

          $property_id  = I('get.id',0,'intval');// 楼盘id property  ID
          $cbd    = I('get.cbd',0,'intval');
          $price  = I('get.price',0,'intval');
          $area   = I('get.area',0,'intval');
          $shi    = I('get.shi',0,'intval');

          $uid    = I('get.uid',0,'intval');//个人发布的房源

          
          //某个楼盘房源展示使用
          //$uid = I('get.uid',is_login(),'intval');

          if(ACTION_NAME == 'filter') $uid = 0;

          $price_map = C('HOUSE_PRICE_MAP');
          $area_map = C('HOUSE_AREA_MAP');
          $shi_map = C('HOUSE_HUXING_MAP');

          if($cbd == 0){
              $map['a.city'] = $city;
              $a_map['a.city'] = $city;
          }elseif($cbd>0){
              $map['a.bid'] = $cbd;
              $a_map['a.bid'] = $cbd;
          }elseif($cbd<0){
              $map['a.district'] = -$cbd;
              $a_map['a.district'] = -$cbd;
          }

          //筛选价格
        
          if($shi >0){
              $map['a.shi'] = $shi_map[$shi];
              $a_map['a.shi'] = $shi;
          }

          if($area>0){
              $map['a.area'] = $area_map[$area];
              $a_map['a.area'] = $area;
          }

          if($price>0){
              $map['a.totalprice'] = $price_map[$price];
              $a_map['a.totalprice'] = $price;
          }

          if($uid>0){
              $map['a.uid'] = $uid;
              $a_map['a.uid'] = $uid;
          }

          if($property_id > 0){
              $map['a.fid'] = $property_id;
              $a_map['a.fid'] = $property_id;
          }

          $map['a.status'] = 1;

          return array('map'=>$map,'a_map'=>$a_map);
    }
}
