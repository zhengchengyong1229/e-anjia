//  楼盘上传 js 控制
jQuery(function(){
  var $ = jQuery,
//      $list = $('#fileList'),
        uploader;
  //初始化Web Uploader
  uploader = WebUploader.create({
      //自动上传
      auto: true,

      //swf文件路径
      swf: './Uploader.swf',

      //文件接受服务器
      server: U('Core/File/uploadPicture'),

      //选择文件按钮
      pick: '#coverpicker',

      //控制文件类型
      accept: {
            title: 'Images',
            extensions: 'jpg,jpeg,bmp,png',
            mimeType: 'image/*'
      }

  }) 

  //有文件加入触发事件
  uploader.on('fileQueued',function( file ){
       alert('此处要生成缩略图');
       console.log(file);
  })

  uploader.on('uploadSuccess',function(file,response){
        console.log('上传成功')
        console.log(response);
  })

  uploader.on('uploadError',function(file,response){
        console.log('上传失败')
        console.log(response);
  })

  uploader.on('uploadComplete',function( file ){
       alert('上传完成');
       console.log(file);
  })
})
