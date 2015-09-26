<?php

/**
 * 热门商圈 
 * @author smartymoon
 */

namespace Addons\CBD\Controller;
use Home\Controller\AddonsController;

class CBDController extends AddonsController{
	
	public function getProvince(){
		if (IS_AJAX){
			$pid = I('pid');  //默认的省份id

			if( !empty($pid) ){
				//$map['id'] = $pid;
			}
			$map['level'] = 1;
			$map['upid'] = 0;
			$list = D('Addons://ChinaCity/District')->_list($map);

			$data = "<option value =''>-省份-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $pid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//获取城市信息
	public function getCity(){
		if (IS_AJAX){
			$cid = I('cid');  //默认的城市id
			$pid = I('pid');  //传过来的省份id

			if( !empty($cid) ){
				//$map['id'] = $cid;
			}
			$map['level'] = 2;
			$map['upid'] = $pid;

			$list = D('Addons://ChinaCity/District')->_list($map);

			$data = "<option value =''>-城市-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $cid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//获取区县市信息
	public function getDistrict(){
		if (IS_AJAX){
			$did = I('did');  //默认的城市id
			$cid = I('cid');  //传过来的城市id

			if( !empty($did) ){
				//$map['id'] = $did;
			}

			$map['pid'] = $cid;

            $d_cbd = D('Home/cbd');
            
            $list = $d_cbd->getlist($map);


			$data = "<option value =''>-州县-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $did == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//商圈信息
	public function getCbd(){
		if (IS_AJAX){
			$did = I('did');  //默认的城市id
            $bid = I('bid');
			$map['pid'] = $did;
            


            $d_cbd = D('Home/cbd');
            $list = $d_cbd->getlist($map);

			$data = "<option value =''>-商圈-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $bid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}

	//楼盘名称
	public function getProperty(){
		if (IS_AJAX){
			$bid = I('bid');  //默认的城市id
            $fid = I('fid');
			$map['pid'] = $bid;
            


            $d_property = D('Home/property');
            $list = $d_property->getlist($map,0,'objectcount desc',100);

			$data = "<option value =''>-楼盘-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $fid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['title'] . "</option>";
			}
			$this->ajaxReturn($data);
		}
	}
}
