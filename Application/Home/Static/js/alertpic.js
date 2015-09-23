$(document).ready(function() {
  //给图片绑定事件
  var jinzhi = 1;
  $demo = $('#demo');
  $demo.on('click',function(){
      $(this).hide();
      $('body').css('overflow','visible');
      jinzhi = 1;

  })
  
  $('.download-pics').on('click',function(e){

      var pic_id = $(this).data('id');
      var $height = $(window).height();
      $demo.find('li[data-id="'+pic_id+'"]').addClass('active').siblings().removeClass('active');
      $('body').css('overflow','hidden');
      jinzhi = 0;
      $demo.css('display','flex').css('align-items','center').css('height',$height).show();
  })

  $('.back-to-left').on('click',function(e){

     var current_pic = $('#demo').find('li.active');
     change_pic(current_pic,'prev');

     e.preventDefault();
     e.stopPropagation();

  })

  $('.back-to-right').on('click',function(e){

     var current_pic = $('#demo').find('li.active');

     change_pic(current_pic,'next');

     e.preventDefault();
     e.stopPropagation();

  })


  var startX,startY,moveEndX,moveEndY;

    $demo.find('img').on('touchstart',function(e){
    //e.preventDefault();
    startX = e.originalEvent.changedTouches[0].pageX;
    startY = e.originalEvent.changedTouches[0].pageY;

  })

  $demo.find('img').on('touchend',function(e){

    var current_pic = $('#demo').find('li.active');

    //e.preventDefault();
    moveEndX = e.originalEvent.changedTouches[0].pageX;
    moveEndY = e.originalEvent.changedTouches[0].pageY;
    var X = moveEndX - startX;
    var Y = moveEndY - startY;

    if(X<-20){
       change_pic(current_pic,'next');
    }else if(X>20){
       change_pic(current_pic,'prev');
    }
  })


  function change_pic(pic,target){
    if(target == 'prev'){

        if(pic.prev().length == 0){
          pic.parent().find('li:last').addClass('active').siblings().removeClass('active');
        }else{
          pic.prev().addClass('active').siblings().removeClass('active');
        }
    }else if(target == 'next'){


        if(pic.next().length == 0){
          pic.parent().find('li:first').addClass('active').siblings().removeClass('active');
        }else{
          pic.next().addClass('active').siblings().removeClass('active');
        }
    }
  }

  $demo.on('touchmove',function(e){
      if(jinzhi == 0){
          e.preventDefault();
          e.stopPropagation();
      }
  },false)
});
