<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.thinkphp.cn>
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */
return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD'     =>    'OT\\TagLib\\Article,OT\\TagLib\\Think',
        
    /* 主题设置 */
    'DEFAULT_THEME' =>  'default',  // 默认模板主题名称

    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'onethink_', // 缓存前缀
    'DATA_CACHE_TYPE'   => 'File', // 数据缓存类型

    /* 文件上传相关配置 */
    'DOWNLOAD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Download/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //下载模型上传配置（文件上传类配置）

    /* 编辑器图片上传相关配置 */
    'EDITOR_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Editor/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ),

     /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）



    /**
     * 附件相关配置
     * 附件是规划在插件中的，所以附件的配置暂时写到这里
     * 后期会移动到数据库进行管理
     */
    'ATTACHMENT_DEFAULT' => array(
        'is_upload'     => true,
        'allow_type'    => '0,1,2', //允许的附件类型 (0-目录，1-外链，2-文件)
        'driver'        => 'Local', //上传驱动
        'driver_config' => null, //驱动配置
    ), //附件默认配置

    'ATTACHMENT_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 5*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Attachment/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //附件上传配置（文件上传类配置）
    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Application/Home'   . '/Static/images',
        '__CSS__' => __ROOT__ . '/Application/Home'   . '/Static/css',
        '__JS__' => __ROOT__ . '/Application/Home'  . '/Static/js',

        '__ZUI__' => __ROOT__ . '/Public/zui',
        '__AMAZE__'=>__ROOT__.'/Public/amaze',
	),

    /*  户型列表 */
    //总价参考列表
    'HOUSE_PRICE_TABLE' => array(
        1=>'20万以下',
        2=>'20-30万',
        3=>'30-40万',
        4=>'40-50万',
        5=>'50-60万',
        6=>'60-80万',
        7=>'80-100万',
        8=>'100-120万',
        9=>'120-150万',
        10=>'150万以上',
    ),

    'HOUSE_PRICE_MAP'  =>array(
        1=>array('lt','20'),
        2=>array('between','20,30'),
        3=>array('between','30,40'),
        4=>array('between','40,50'),
        5=>array('between','50,60'),
        6=>array('between','60,80'),
        7=>array('between','80,100'),
        8=>array('between','100,120'),
        9=>array('between','120,150'),
        10=>array('gt','150'),
    ),

    //面积参考列表
    'HOUSE_AREA_TABLE' => array(
        1=>'50㎡以下',
        2=>'50-70㎡',
        3=>'70-90㎡',
        4=>'90-110㎡',
        5=>'110-130㎡',
        6=>'130-150㎡',
        7=>'150-200㎡',
        8=>'200㎡以上',
    ),

    'HOUSE_AREA_MAP' => array(
        1=>array('lt','50'),
        2=>array('between','50,70'),
        3=>array('between','70,90'),
        4=>array('between','90,110'),
        5=>array('between','110,130'),
        6=>array('between','130,150'),
        7=>array('between','150,200'),
        8=>array('gt','200'),
    ),

    //户型参考列表
    'HOUSE_HUXING_TABLE'=>array(
        1=>'一室',
        2=>'二室',
        3=>'三室',
        4=>'四室',
        5=>'四室以上'
    ),

    'HOUSE_HUXING_MAP'=>array(
        1=>'1',
        2=>'2',
        3=>'3',
        4=>'4',
        5=>array('gt',4),
    ),

    
    "HOUSE_TYPE_TABLE"=>array(
         5 =>'住宅',
         1 =>'公寓',
         4 =>'别墅',
         2 =>'商铺',
         3 =>'写字楼'
    ),

    "HOUSE_TYPE_MAP"=>array(
         5 =>'%0%',
         1 =>'%1%',
         4 =>'%4%',
         2 =>'%2%',
         3 =>'%3%',
    ),

    //房产类型,object中使用
    'HOUSE_STYLE'=>array(
        1=>'住宅',
        2=>'公寓',
        3=>'商铺',
        4=>'写字楼',
        5=>'别墅'
    ),

    'IDENTITY_TABLE'=>array(
        0=>'未认证',    
        1=>'经纪人',
        2=>'中介',
        3=>'开发商',
    ),

    'OBJECT_LEIBIE'=>array(
        1=>'多层',
        2=>'小高层',
        3=>'高层',
    ),

    //房产类型，property中使用
    'PROPERTY_SPAN'=>array(
        0=>'<span class="am-badge am-badge-primary am-radius">住宅</span>',
        1=>'<span class="am-badge am-badge-secondary am-radius">公寓</span>',
        2=>'<span class="am-badge am-badge-success am-radius">商铺</span>',
        3=>'<span class="am-badge am-badge-warning am-radius">写字楼</span>'
    ),

);
