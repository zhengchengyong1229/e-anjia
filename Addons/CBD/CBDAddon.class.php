<?php

namespace Addons\CBD;
use Common\Controller\Addon;

/**
 * 中国热门商圈
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

            /* 先判断插件需要的钩子是否存在 */
            $this->getisHook('J_CBD', $this->info['name'], $this->info['description']);

            //读取插件sql文件
            /*
            $sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/install.sql');
            $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
            $counts = count($sqlFormat);
            
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                D()->execute($sql);
            }
            */
            return true;
        }

        public function uninstall(){
            //读取插件sql文件
            /*
            $sqldata = file_get_contents('http://'.$_SERVER['HTTP_HOST'].__ROOT__.'/Addons/'.$this->info['name'].'/uninstall.sql');

            $sqlFormat = $this->sql_split($sqldata, C('DB_PREFIX'));
            $counts = count($sqlFormat);
             
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                D()->execute($sql);
            }
            */
            return true;
        }

        //实现的J_China_City钩子方法
        public function J_CBD($param){

            //默认山东威海
            $param = empty($param)?array('province'=>'370000','city'=>'371000'):$param;

            $this->assign('param', $param);
            $this->display('cbd');
        }

        //获取插件所需的钩子是否存在
        public function getisHook($str, $addons, $msg=''){
            $hook_mod = M('Hooks');
            $where['name'] = $str;
            $gethook = $hook_mod->where($where)->find();
            if(!$gethook || empty($gethook) || !is_array($gethook)){
                $data['name'] = $str;
                $data['description'] = $msg;
                $data['type'] = 1;
                $data['update_time'] = NOW_TIME;
                $data['addons'] = $addons;
                if( false !== $hook_mod->create($data) ){
                    $hook_mod->add();
                }
            }
        }

        /**
         * 解析数据库语句函数
         * @param string $sql  sql语句   带默认前缀的
         * @param string $tablepre  自己的前缀
         * @return multitype:string 返回最终需要的sql语句
         */
        public function sql_split($sql, $tablepre) {

            if ($tablepre != "thinkox_")
                $sql = str_replace("thinkox_", $tablepre, $sql);
                $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

            if ($r_tablepre != $s_tablepre)
                $sql = str_replace($s_tablepre, $r_tablepre, $sql);
                $sql = str_replace("\r", "\n", $sql);
                $ret = array();

                $num = 0;
                $queriesarray = explode(";\n", trim($sql));
                unset($sql);

            foreach ($queriesarray as $query) {
                $ret[$num] = '';
                $queries = explode("\n", trim($query));
                $queries = array_filter($queries);
                foreach ($queries as $query) {
                    $str1 = substr($query, 0, 1);
                    if ($str1 != '#' && $str1 != '-')
                        $ret[$num] .= $query;
                }
                $num++;
            }
            return $ret;
        }
    }
