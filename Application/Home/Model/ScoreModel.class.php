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
class ScoreModel extends Model{

    protected $tableName = 'object_score';


    /*
     * 增加一条评分
     * @变量说明  $score 指房源总分平均值
     *            $user_score 指参与某房源打分者所有打分平均值
     */
    public function addScore($data){
        //insert 一条数据
        $res =  $this->add($data);

        //获得该房源变更值
        $score = $this->where(array('oid'=>$data['oid']))->avg('score');
        $score = round(floatval($score),2);
        //获得参与某房源打分者所有打分平均值
        $user_score = $this->query('select avg(`score`) as avgscore from fang_object_score where uid in (select uid from fang_object_score where oid = '.$data['oid'].')');
        $user_score = round($user_score[0]['avgscore'],2);

        //更新数据
        $id = $data['oid'];
        D('Object')->setUserAverage($id,$score);
        //经测试不准确
        //D('Object')->setObjectAverage($id,$user_score);
        D('Object')->setRatio($id);

        return $res;
    }



}
