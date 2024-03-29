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
 * 文件模型
 * 负责文件的下载和上传
 */

class FileModel extends Model{
	/**
	 * 文件模型自动完成
	 * @var array
	 */
	protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
	);

	/**
	 * 文件模型字段映射
	 * @var array
	 */
	protected $_map = array(
		'type' => 'mime',
	);

	/**
	 * 文件上传
	 * @param  array  $files   要上传的文件列表（通常是$_FILES数组）
	 * @param  array  $setting 文件上传配置
	 * @param  string $driver  上传驱动名称
	 * @param  array  $config  上传驱动配置
	 * @return array           文件上传成功后的信息
	 */
	public function upload($files, $setting, $driver = 'Local', $config = null){
		/* 上传文件 */
		$setting['callback'] = array($this, 'isFile');
		$Upload = new \Think\Upload($setting, $driver, $config);
		$info   = $Upload->upload($files);

		/* 设置文件保存位置 */
		$this->_auto[] = array('location', 'Ftp' === $driver ? 1 : 0, self::MODEL_INSERT);

		if($info){ //文件上传成功，记录文件信息
			foreach ($info as $key => &$value) {
				/* 已经存在文件记录 */
				if(isset($value['id']) && is_numeric($value['id'])){
					continue;
				}

				/* 记录文件信息 */
				if($this->create($value) && ($id = $this->add())){
					$value['id'] = $id;
				} else {
					//TODO: 文件上传成功，但是记录文件信息失败，需记录日志
					unset($info[$key]);
				}
			}
			return $info; //文件上传成功
		} else {
			$this->error = $Upload->getError();
			return false;
		}
	}

	/**
	 * 下载指定文件
	 * @param  number  $root 文件存储根目录
	 * @param  integer $id   文件ID
	 * @param  string   $args     回调函数参数
	 * @return boolean       false-下载失败，否则输出下载文件
	 */
	public function download($root, $id, $callback = null, $args = null){
		/* 获取下载文件信息 */
		$file = $this->find($id);
		if(!$file){
			$this->error = '不存在该文件！';
			return false;
		}

		/* 下载文件 */
		switch ($file['location']) {
			case 0: //下载本地文件
				$file['rootpath'] = $root;
				return $this->downLocalFile($file, $callback, $args);
			case 1: //TODO: 下载远程FTP文件
				break;
			default:
				$this->error = '不支持的文件存储类型！';
				return false;

		}

	}

    /*
     *
     *下载打包后的一个房源的9张图片
     *@param $pics 图片id们
     *@param $zipname  压缩包名称 楼盘_上传者昵称_时间.zip
     */

    public function downloadObjectPic($pics = array(),$zipname){
        $map['id'] = array('in',$pics);

        // $paths 取出列表
        $paths  =   D('Picture')->where($map)->getField('path',true);
        $zip = new \ZipArchive();
        if($zip->open('./ZIP/'.$zipname,\ZipArchive::CREATE) === true){
            foreach($paths as $path){
                //将picture下文件复制到ZIP下
                $pathinfo = pathinfo($path);
                $basename = $pathinfo['basename'];
                copy('.'.$path,'./ZIP/'.$basename);

                //添加文件到ZIP
                if( $zip->addFile('./ZIP/'.$basename)){

                }
            }
        }

        $zip->close();
        $res = $this->downLocalZIP($zipname);
    }

	/**
	 * 检测当前上传的文件是否已经存在
	 * @param  array   $file 文件上传数组
	 * @return boolean       文件信息， false - 不存在该文件
	 */
	public function isFile($file){
		if(empty($file['md5'])){
			throw new Exception('缺少参数:md5');
		}
		/* 查找文件 */
		$map = array('md5' => $file['md5']);
		return $this->field(true)->where($map)->find();
	}

	/**
	 * 下载本地文件
	 * @param  array    $file     文件信息数组
	 * @param  callable $callback 下载回调函数，一般用于增加下载次数
	 * @param  string   $args     回调函数参数
	 * @return boolean            下载失败返回false
	 */

	private function downLocalZIP($file){
		if(is_file('./ZIP/'.$file)){

			header('Content-Type:application/force-download' );
            header('Content-Transfer-Encoding:binary');
			header('Content-Type:application/zip');
  			header('Content-Length:' . filesize('./ZIP/'.$file));

			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($file) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file . '"');
			}
			readfile('./ZIP/'.$file);
			exit;
		} else {
			$this->error = '文件已被删除！';
			return false;
		}
	}

    /**
     *下载ZIP文件
     *
     *
     *
     */

	private function downLocalFile($path,$name, $callback = null, $args = null){
		if(is_file($file['rootpath'].$file['savepath'].$file['savename'])){
			/* 调用回调函数新增下载数 */
			is_callable($callback) && call_user_func($callback, $args);

			/* 执行下载 */ //TODO: 大文件断点续传
			header("Content-Description: File Transfer");
  //			header('Content-type: ' . $file['type']);
  //			header('Content-Length:' . $file['size']);
			if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
				header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"');
			} else {
				header('Content-Disposition: attachment; filename="' . $file['name'] . '"');
			}
			readfile($file['rootpath'].$file['savepath'].$file['savename']);
			exit;
		} else {
			$this->error = '文件已被删除！';
			return false;
		}
	}
}
