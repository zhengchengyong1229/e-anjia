<?php

namespace Addons\CBD;
use Common\Controller\Addon;

/**
 * 热门商圈插件
 * @author smartymoon
 */

    class CBDAddon extends Addon{

        public $info = array(
            'name'=>'CBD',
            'title'=>'热门商圈',
            'description'=>'热门商圈更靠谱',
            'status'=>1,
            'author'=>'smartymoon',
            'version'=>'1.0'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的J_CBD钩子方法
        public function J_CBD($param){

        }

    }