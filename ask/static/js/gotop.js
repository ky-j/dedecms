$(function(){
	gotop();
	$(".gotop").click(function(){
		$(window).scrollTop(0);			 
		})
	})
function gotop(){
	var w=$(window).width();
	var w2=(w-960)/2+960;
	var h=$(window).height();
	var h2=h-60;
	$(".gotop").css({"top":h2,"left":w2});
	$(window).scroll(function(){
		var scrtop=$(window).scrollTop();
		if(scrtop>0){
			$(".gotop").show(200);
			}else{
			$(".gotop").hide(200);
			};
		$(".gotop").css({"top":h2+scrtop,"left":w2})
		})	
	}