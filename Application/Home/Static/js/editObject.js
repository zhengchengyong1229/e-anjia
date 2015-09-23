//房源上传js控制方法
jQuery(function(){
  var $ = jQuery,
        $wrap = $('#file-list'),//列表容器
        $input_pic = $('input[name="pics"]'),
        pictures = $input_pic.val().split(','),
        status = 1, //状态标志变量，默认0，如果为0，则清空原有数据，并把status改成1，当图片数量为9时，把状态改为2，表示达到上传最大数量,禁止上传
        count = 0,  //表示形成缩略图上传数量
        already_count = 0, //已经上传
        uploader;

  //初始化Web Uploader
  uploader = WebUploader.create({
      //自动上传
      auto: true,

      //swf文件路径
      swf: './Public/js/ext/webuploader/js/Uploader.swf',

      //文件接受服务器
      server: U('Core/File/uploadPicture'),

      //选择文件按钮
      pick: '#pictures',

      //控制文件类型
      accept: {
            title: 'Images',
            extensions: 'jpg,jpeg,bmp,png',
            mimeType: 'image/*'
      }

  }) 



  //有文件加入触发事件
   uploader.on('fileQueued',function( file ){
     if(count < 9){
       uploader.makeThumb(file,function(error,src){
            if(error){
               alert('不能预览');
               return;
            }
             var img = $('<li class="am-padding-xs" style="position:relative"><a href="" baidu-id="'+file.id+'" class="am-close am-close-spin">X</a><img class="am-img-responsive" src="'+src+'"/></li>');
             img.find('a').click(function(e){
                   var rid = $(this).data('rid');
                   pictures = $input_pic.val().split(',');
                   if(rid){
                      var key = pictures.indexOf(rid);
                      if(key>-1){
                        pictures.splice(key,1);
                      }
                      $input_pic.val(pictures);
                      $(this).parent().remove();
                   }
                   e.preventDefault();
             })
             $wrap.append(img);
             },'100','100');
     }else{
        status = 2;
     }

     count++;
  })

  uploader.on('uploadStart',function(){
     if(already_count >= 9){
          uploader.stop();
     }
  })


  uploader.on('uploadSuccess',function(file,response){
         $('.am-close-spin').filter('[baidu-id="'+file.id+'"]').data('rid',response.data.file.id);
         pictures = $input_pic.val().split(',');
         pictures.push(response.data.file.id);
         already_count++;
  })

  uploader.on('beforeFileQueued',function(){
     if( status == 0){
       $wrap.empty();
       $input_pic.val('');
       status = 1;
     }
     if(count >= 9){
         return false;
     }
  })

  uploader.on('uploadError',function(file,response){
        console.log('上传失败')
        console.log(response);
  })

  uploader.on('uploadComplete',function( file ){
       $input_pic.val(pictures);
  })
});


jQuery(function(){
  var $ = jQuery,

  //初始化Web Uploader
  uploader = WebUploader.create({
      //自动上传
      auto: true,

      //swf文件路径
      swf: './Uploader.swf',

      //文件接受服务器
      server: U('Core/File/uploadPicture'),

      //选择文件按钮
      pick: {
        id:'#file',
        multiple:false
      },

      //控制文件类型
      accept: {
            title: 'Images',
            extensions: 'jpg,jpeg,bmp,png',
            mimeType: 'image/*'
      }

  }) 


  uploader.on('uploadComplete',function( file ){
       $input_pic.val(pictures);
  })


  uploader.on('uploadSuccess',function(file,response){
         input_file.val(response.data.file.id);
  })


  uploader.on('uploadError',function(file,response){
        console.log('上传失败')
        console.log(response);
  })
});
