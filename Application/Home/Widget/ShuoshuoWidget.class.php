<?php

namespace Home\Widget;

use Think\Action;

/**说说发布wiget
 * 此功能只有增加，删除， 置顶,无编辑
 *
 */
class ShuoshuoWidget extends Action
{

    /*
    * 网站主页展示三条说说
    * 
    *
    */

    public function  index()
    {
        $d_shuo = D('Shuo');
        $map = array(
            
        );
        $limit = 4;
        $field = 'id,uid,content,createtime';
        $list =  $d_shuo->getList($field,$map,'id desc',$limit);

        foreach($list as &$val){
            $val['nickname'] = query_user('nickname',$val['uid']);
        }


        $this->assign('list',$list);
        $this->display('Widget/shuoshuo');
    }

    /*
     *项目主页展示的三条说说
     *
     */

    public function myShuoList(){
        $d_shuo = D('Shuo');
        $uid = I('get.uid',0,'intval')?I('get.uid',0,'intval'):is_login();
        $map = array(
            'uid'=>$uid
        );
        $limit = 2;
        $field = 'id,content,createtime';
        $data =  $d_shuo->where($map)->field($field)->order('id desc')->find();
        $this->assign('shuo',$data);
        $this->display('Widget/myshuolist');
    }

    /*
     * 增加一条说说
     *
     */

    public function add(){
        if(IS_POST){

        }else{
            $this->display('Widget/add');
        }
    }

    /*
     *
     * 删除一条说说
     *
     */

    public function delete(){
        $id = I('param.id',0,'intval');
        $map = array(
            'uid'=>is_login(),
            'id' =>$id
        );
        $res = D('Shuo')->where($map)->setField('status',0);

        if($res){
           $this->ajaxReturnHandle(1,'删除成功');
        }else{
            $this->ajaxReturnHandle(0,'删除失败');
        }
    }

    /*
     *置顶一条说说 TODO
     *
     */

    public function top(){

    }

} 
