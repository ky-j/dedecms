<!--

function $Nav()
{
	if(window.navigator.userAgent.indexOf("MSIE")>=1) return 'IE';
	else if(window.navigator.userAgent.indexOf("Firefox")>=1) return 'FF';
	else return "OT";
}

function SelectImage(fname,stype,imgsel)
{
	if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	if(!fname) fname = 'form1.picname';
	if(imgsel) imgsel = '&noeditor=yes';
	if(!stype) stype = '';
	window.open("../include/dialog/select_images.php?f="+fname+"&noeditor=yes&imgstick="+stype+imgsel, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=650,height=400,left="+posLeft+", top="+posTop);
}

function imageCut(fname)
{
	if($Nav()=='IE'){ var posLeft = window.event.clientX-100; var posTop = window.event.clientY; }
	else{ var posLeft = 100; var posTop = 100; }
	if(!fname) fname = 'pic1';
	file = document.getElementById(fname).value;
	if(file == '') {
		alert('请先选择网站内已上传的图片');
		return false;
	}
	window.open("/dede/imagecut.php?f="+fname+"&file="+file, "popUpImagesWin", "scrollbars=yes,resizable=yes,statebar=no,width=800,height=600,left="+posLeft+", top="+posTop);
}

-->