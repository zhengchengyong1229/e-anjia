/*
 *   可以用 
 */
$(function () {
    var   uploader,
          $container = $('#license_image'),
          $license_id 

    uploader = WebUploader.create({
      auto:true,
      server: U('Core/File/uploadPicture'),
      pick: {
        id:'#uploaderpicker',
        multiple:false
      },
      accept:{
          title:'Images',
          extensions:'gif,jpg,bmp,png',
          mimeTypes:'image/*'
      },
      thumb:{
           width:600,
           height:200,
           quality:70,
           crop:false,
      }

    })

    uploader.on('fileQueued',function(file){
        uploader.makeThumb(file,function(error,ret){
            if(error){
                $container.text('预览错误,使用高版本浏览器');
            } else{
               $container.html('<img class="am-center am-img-responsive am-img-thumbnail am-radius" src="'+ ret +'"/>')
            }
        })
          
    })

    uploader.on('uploadComplete',function(file){

    })

    uploader.on('uploadSuccess',function(file,response){
          //response 服务器返回的数据
          $license_id = response.data.file.id;
          var url    = U('Ucenter/config/apply',['license',$license_id]);
          var button = '<button class="ajax-post am-btn am-btn-block am-btn-primary" style="margin:10px 0;background-color:#c40d23;border-color:#c40d23;" href="'+ url +'">提交认证</button>';
          $container.append(button);
    })
});
