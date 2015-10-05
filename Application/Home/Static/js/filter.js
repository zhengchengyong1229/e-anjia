$(function(){
  //定义容器
   var $filter = $('#filter');//总容器
   var $fc     = $('#filter-container');//筛选内容

   var $f_cbd    = $('#filter-cbd');  //商圈
   var $f_price  = $('#filter-price'); //价格
   var $f_shi    = $('#filter-shi');//户型
   var $f_area   = $('#filter-area');//区域
   var $f_type   = $('#filter-type');//区域

   //触发器定义
   var $cbd_triger   = $('#cbd-triger');
   var $price_triger = $('#price-triger');
   var $shi_triger   = $('#shi-triger');
   var $area_triger  = $('#area-triger');
   var $type_triger  = $('#type-triger');

   var $cbd_second_triger = $('.cbd-second-triger');
   var $cbd_second   = $('.cbd_second');


   
   //拼装公共URL
   var url = '';

   url = ThinkPHP.APP + $('#filter-cbd').data('url') + '/' + common_url +'cbd/';
   
   //传输的数据
   var trans_data    = {
       cbd:0,
       price:0,
       shi:0,
       area:0
   };

   //隐藏其他三个
   function hidden_fc(param){
     switch(param){
       case 'cbd':
          $f_price.hide();
          $f_shi.hide();
          $f_area.hide();
          $f_type.hide();
          break;
       case 'price':
          $f_cbd.hide();
          $f_shi.hide();
          $f_area.hide();
          $f_type.hide();
          break;
       case 'shi':
          $f_cbd.hide();
          $f_price.hide();
          $f_area.hide();
          $f_type.hide();
          break;
       case 'area':
          $f_cbd.hide();
          $f_price.hide();
          $f_shi.hide();
          $f_type.hide();
          break;
       case 'type':
          $f_cbd.hide();
          $f_price.hide();
          $f_shi.hide();
          $f_area.hide();
          break;
     }
   }

   function cbd_second(data,id,name){
       var html = '<li><a href="'+ url + '-'+id +'">&nbsp;&nbsp;&nbsp;全'+ name +'</a></li>';

       if(data == undefined){
         return '';
       }

       data.map(function(it){
         html+='<li><a href="'+ url + it.id+'">&nbsp;&nbsp;&nbsp;'+it.name+'</a></li>';
       })

       return html;
   }

   $cbd_triger.click(function(){
      $f_cbd.toggle();
      hidden_fc('cbd');
   });

   $price_triger.click(function(){
      $f_price.toggle();
      hidden_fc('price');
   });

   $shi_triger.click(function(){
      $f_shi.toggle();
      hidden_fc('shi');
   });

   $area_triger.click(function(){
      $f_area.toggle();
      hidden_fc('area');
   });

   $type_triger.click(function(){
      $f_type.toggle();
      hidden_fc('type');
   });

   //cbd子菜单展示功能

   $cbd_second_triger.click(function(){
        $('#filter-cbd>li').css('background','#FFF');
        $(this).css('background','#F7F7F7');
        var cbd_id =  $(this).data('id');

        var data =  cbd_table_json[cbd_id]['_'];
        var name =  cbd_table_json[cbd_id]['name'];
        var id   =  cbd_table_json[cbd_id]['id'];

        var html = cbd_second(data,id,name)
        $cbd_second.html(html).show();
   })




   //处理筛选菜单动画效果，参考amazeui动画
   
   
   $('#filter').on('click',function(){
     $(this).addClass('filter-modal').css('margin-top',0);
     $('.am-dimmer').show();
     $('#filter-container').show();
     $('.am-dimmer').on('click',function(){
       $('.am-dimmer').hide();
       $('#filter-container').hide();
       $('#filter').removeClass('filter-modal').css('margin-top','-1.6rem');
     })
     $(this).addClass('filter-modal').css('margin-top',0);
   })
   
   
   
})
