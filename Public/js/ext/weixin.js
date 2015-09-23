$(function(){

	     wx.config({
	          debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。

	          appId: 'wxc05f97763b40b83f', // 必填，公众号的唯一标识
	          timestamp: WEIXIN_JSON.timestamp, // 必填，生成签名的时间戳
	          nonceStr: WEIXIN_JSON.noncestr, // 必填，生成签名的随机串
	          signature: WEIXIN_JSON.signature,// 必填，签名，见附录1
	          jsApiList: ['onMenuShareAppMessage','onMenuShareTimeline'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2 //分享到朋友圈,分享给朋友
	     });


		wx.ready(function(){
		            wx.onMenuShareAppMessage({
					    title:WEIXIN_TITLE, // 分享标题
					    desc: WEIXIN_DESCRIP, // 分享描述
					    link: document.URL, // 分享链接
					    imgUrl: WEIXIN_LOGO, // 分享图标
					    type: '', // 分享类型,music、video或link，不填默认为link
					    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
					    success: function () { 
					        // 用户确认分享后执行的回调函数
					    },
					    cancel: function () { 
					        // 用户取消分享后执行的回调函数
					    }
					});

					wx.onMenuShareTimeline({
					    title: WEIXIN_DESCRIP, // 分享标题
					    link: document.URL, // 分享链接
					    imgUrl: WEIXIN_LOGO, // 分享图标
					    success: function () { 
					        // 用户确认分享后执行的回调函数
					    },
					    cancel: function () { 
					        // 用户取消分享后执行的回调函数
					    }
					});
    	})
     
})
