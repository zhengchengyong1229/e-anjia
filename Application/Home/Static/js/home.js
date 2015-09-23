$(function(){

   var isLoading = false;


  function ifLoadMore(){

     var $windowH = $(window).height();//窗口高度
     var $scrollH = $(window.top).scrollTop();//滑动距离
     var $loadMoreOffset = $('.getmore').offset().top;//元素距顶部距离

    return $windowH+$scrollH > $loadMoreOffset-100?true:false;
  }

  var $getmore = $('.getmore');

//  $getmore.click(function(){
    $(window).on('scroll',function(){
      if(ifLoadMore()&&!isLoading){
          var $this = $('.getmore');
          var url = $this.attr('data-url');
          var page = $this.data('page');
          isLoading = true;
          $.post(url,{page:page+1},function(msg){
            if(msg.status){
              $this.parent().prev().append(msg.html);
              $this.data('page',page+1);
              isLoading = false;
            }else{
              isLoading = true;
              $this.html('全部加载完成！');
              $this.parent().delay(3000).hide(0);
            }
          },'json')
      }

  })
})
