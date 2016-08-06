/***********************************************************
 * 使用iframe模拟ajax的窗体，本JS需要引用jquery框架
 * 修改自 thickbox 源码
 * by:tianya<tianya@dedecms.com>
************************************************************/

var tb_pathToImage="static/images/loading.gif";
var ftop1=0,ftop2=0,ftop=0,fleft=0,fftop=0;
var ref_parent = false;

/**
 * 对于指定了class为'thickbox'的超链接自动监听其超链接，其中可以指定 rel=(0|1) 属性决定点击关闭后是否刷新上级窗口
 * 如果不需要侦听超链接事件，可以禁用些初始化方法
 */
$(document).ready(function(){
	tb_init('a.thickbox, area.thickbox, input.thickbox');
    imgLoader=new Image();
    imgLoader.src=tb_pathToImage;
    
});

/**
 * 弹窗警告窗口让用户确认操作
 * refParent 参数(0|1)决定点击关闭后是否刷新上级窗口
 */
function tb_action(msg, gourl)
{
    msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;|&nbsp; <a href='"+gourl+"'>确定要操作&gt;&gt;</a>";
    tb_showmsg(msg);
}

/* 初始化函数 */ 
function tb_init(domChunk)
{
	$(domChunk).click(function(){
    	var t=this.title||this.name||null;
        var a=this.href||this.alt;
        var g=this.rel||false;tb_show(t,a,g);
        this.blur();
        return false;
    });
};

/**
 * 弹窗主函数
 * refParent 参数(0|1)决定点击关闭后是否刷新上级窗口
 */
function tb_show(caption,url,refParent,imageGroup){
	ref_parent = refParent;
	try{
    	ftop=0,fleft=0,fftop=0,ftop1=0,ftop2=0;
        if(typeof document.body.style.maxHeight==="undefined")
        {
        	$("body","html").css({height:"100%",width:"100%"});
            $("html").css("overflow","hidden");
            if(document.getElementById("TB_HideSelect")===null)
            {
            	$("body").append("<iframe id='TB_HideSelect'></iframe><div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(tb_remove);
            }
        } else {
            if(document.getElementById("TB_overlay")===null)
            {
            	$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
                $("#TB_overlay").click(tb_remove);}};
                if(tb_detectMacXFF()){
                	$("#TB_overlay").addClass("TB_overlayMacFFBGHack");
                } else {
                	$("#TB_overlay").addClass("TB_overlayBG");
                };
                if(caption===null){
                	caption="";
                };
                $("body").append("<div id='TB_load'><img src='"+imgLoader.src+"' /></div>");
                $('#TB_load').show();
                var baseURL;
                if(url.indexOf("?")!==-1){
                	baseURL=url.substr(0,url.indexOf("?"));
                } else {
                	baseURL=url;
                };
                var urlString=/\.jpg$|\.jpeg$|\.png$|\.gif$|\.bmp$/;
                var urlType=baseURL.toLowerCase().match(urlString);
                if(urlType=='.jpg'||urlType=='.jpeg'||urlType=='.png'||urlType=='.gif'||urlType=='.bmp')
                {
                	TB_PrevCaption="";
                    TB_PrevURL="";
                    TB_PrevHTML="";
                    TB_PrevHTML1="";
                    TB_NextCaption="";
                    TB_NextURL="";
                    TB_NextHTML="";
                    TB_NextHTML1="";
                    TB_imageCount="";
                    TB_FoundURL=false;
                    if(imageGroup){
                    	TB_TempArray=$("a[rel="+imageGroup+"]").get();
                        for(TB_Counter=0;((TB_Counter<TB_TempArray.length)&&(TB_NextHTML===""));TB_Counter++)
                        {
                        	var urlTypeTemp=TB_TempArray[TB_Counter].href.toLowerCase().match(urlString);
                            if(!(TB_TempArray[TB_Counter].href==url)){
                            	if(TB_FoundURL){
                                	TB_NextCaption=TB_TempArray[TB_Counter].title;
                                    TB_NextURL=TB_TempArray[TB_Counter].href;
                                    TB_NextHTML="<span id='TB_next'>&nbsp;&nbsp;<a href='#'>\u4E0B\u4E00\u5F20 &gt;</a></span>";TB_NextHTML1='<a href="#" id="nextLink" title="\u4E0B\u4E00\u5F20"></a>';
                                } else {
                                	TB_PrevCaption=TB_TempArray[TB_Counter].title;
                                    TB_PrevURL=TB_TempArray[TB_Counter].href;
                                    TB_PrevHTML="<span id='TB_prev'>&nbsp;&nbsp;<a href='#'>&lt; \u4E0A\u4E00\u5F20</a></span>";TB_PrevHTML1='<a href="#" title="\u4E0A\u4E00\u5F20" id="prevLink"></a>';
                                }
                            } else {
                            	TB_FoundURL=true;
                                TB_imageCount="\u56FE\u7247 "+(TB_Counter+1)+" / "+(TB_TempArray.length);
                            }
                        }
                    };
                    imgPreloader=new Image();
                    imgPreloader.onload=function(){
                    	imgPreloader.onload=null;
                        var pagesize=tb_getPageSize();
                        var x=pagesize[0]-150;
                        var y=pagesize[1]-150;
                        var imageWidth=imgPreloader.width;
                        var imageHeight=imgPreloader.height;
                        if(imageWidth>x){
                        	imageHeight=imageHeight*(x/imageWidth);
                            imageWidth=x;
                            if(imageHeight>y){
                            	imageWidth=imageWidth*(y/imageHeight);
                                imageHeight=y;
                            }
                        } else if(
                        	imageHeight>y
                        )
                        {
                        	imageWidth=imageWidth*(y/imageHeight);
                            imageHeight=y;
                            if(imageWidth>x){
                            	imageHeight=imageHeight*(x/imageWidth);
                                imageWidth=x;
                            }
                        };
                        TB_WIDTH=imageWidth+30;
                        TB_HEIGHT=imageHeight+60;
                        $("#TB_window").append("<img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"'/><div id='hoverNav'>"+TB_PrevHTML1+TB_NextHTML1+"</div><div id='TB_caption'>"+caption+"<div id='TB_secondLine'>"+TB_imageCount+TB_PrevHTML+TB_NextHTML+"</div></div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton' title='\u5173\u95ED\u6216\u6309\u952E\u76D8\u9000\u51FA\u952E'>\u5173\u95ED</a></div>");
                        $("#TB_closeWindowButton").click(tb_remove);
                        if(!(TB_PrevHTML==="")){
                        	function goPrev(){
                            	if($(document).unbind("click",goPrev)){
                                	$(document).unbind("click",goPrev);
                                };
                                $("#TB_window").remove();
                                $("body").append("<div id='TB_window'></div>");
                                tb_show(TB_PrevCaption,TB_PrevURL,ref_parent,imageGroup);
                                return false;
                            };
                            $('#prevLink').height(imageHeight);
                            $("#TB_prev").click(goPrev);
                            $("#prevLink").click(goPrev);
                        };
                        if(!(TB_NextHTML==="")){
                        	function goNext(){
                            	$("#TB_window").remove();
                                $("body").append("<div id='TB_window'></div>");
                                tb_show(TB_NextCaption,TB_NextURL,ref_parent,imageGroup);
                                return false;
                            };
                            $("#TB_next").click(goNext);
                            $('#nextLink').height(imageHeight);
                            $("#nextLink").click(goNext);
                        };
                        document.onkeydown=function(e){
                        	if(e==null){
                            	keycode=event.keyCode;
                            }else{
                            	keycode=e.which;
                            };
                            if(keycode==27){
                            	tb_remove();
                            } else if (keycode==39){
                            	if(!(TB_NextHTML==="")){
                                	document.onkeydown="";
                                    goNext();
                                }
                            } else if (keycode==37){
                            	if(!(TB_PrevHTML==="")){
                                	document.onkeydown="";
                                    goPrev();
                                }
                            }
                        };
                        tb_position();
                        $("#TB_load").remove();
                        $("#TB_ImageOff").click(tb_remove);
                        $("#TB_window").css({display:"block"});
                    };
                    imgPreloader.src=url;
                    $("#TB_window").fdrag(true);
                }else{
                	var queryString=url.replace(/^[^\?]+\??/,'');
                    var params=tb_parseQuery(queryString);
                    var fwidth=params['width'];
                    var fheight=params['height'];
                    if(fwidth<=1){
                    	fwidth=$("body").width()*fwidth;
                    };
                    if(fheight<=1){
                    	fheight=document.documentElement.clientHeight*fheight;
                    };
                    TB_WIDTH=(fwidth*1)+30||630;
                    TB_HEIGHT=(fheight*1)+40||440;
                    ajaxContentW=TB_WIDTH-30;
                    ajaxContentH=TB_HEIGHT-45;
                    if(url.indexOf('TB_iframe')!=-1)
                    {
                    	urlNoQuery=url.split('TB_');
                        $("#TB_iframeContent").remove();
                        if(params['modal']!="true"){
                        	$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='\u5173\u95ED\u6216\u6309\u952E\u76D8\u9000\u51FA\u952E'>\u5173\u95ED</a></div></div><iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;' > </iframe>");
                        }else{
                        	$("#TB_overlay").unbind();
                            $("#TB_window").append("<iframe frameborder='0' hspace='0' src='"+urlNoQuery[0]+"' id='TB_iframeContent' name='TB_iframeContent"+Math.round(Math.random()*1000)+"' onload='tb_showIframe()' style='width:"+(ajaxContentW+29)+"px;height:"+(ajaxContentH+17)+"px;'> </iframe>");
                        }
                    } else {
                    	if($("#TB_window").css("display")!="block"){
                        	if(params['modal']!="true"){
                            	$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='\u5173\u95ED\u6216\u6309\u952E\u76D8\u9000\u51FA\u952E'>\u5173\u95ED</a></div></div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px'></div>");
                            }else{
                            	$("#TB_overlay").unbind();
                                $("#TB_window").append("<div id='TB_ajaxContent' class='TB_modal' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>");
                            }
                        } else {
                        	$("#TB_ajaxContent")[0].style.width=ajaxContentW+"px";
                            $("#TB_ajaxContent")[0].style.height=ajaxContentH+"px";
                            $("#TB_ajaxContent")[0].scrollTop=0;
                            $("#TB_ajaxWindowTitle").html(caption);
                        }
                    };
                    $("#TB_closeWindowButton").click(tb_remove);
                    if(url.indexOf('TB_inline')!=-1){
                    	$("#TB_ajaxContent").append($('#'+params['inlineId']).children());
                        $("#TB_window").unload(function(){$('#'+params['inlineId']).append($("#TB_ajaxContent").children());});
                        tb_position();
                        $("#TB_load").remove();
                        $("#TB_window").css({display:"block"});
                    }else if(url.indexOf('TB_iframe')!=-1){
                    	tb_position();
                        if($.browser.safari){
                        	$("#TB_load").remove();
                            $("#TB_window").css({display:"block"});
                        }
                    }else{
                    	$("#TB_ajaxContent").load(url+="&random="+(new Date().getTime()),function(){tb_position();$("#TB_load").remove();tb_init("#TB_ajaxContent a.thickbox");$("#TB_window").css({display:"block"});});
                    };
                    $("#TB_window").fdrag(true);
                };
                if(!params['modal']){
                	$("#TB_window").setHandler('TB_title');
                    document.onkeyup=function(e){
                    	if(e==null){
                        	keycode=event.keyCode;
                        }else{
                        	keycode=e.which;
                        };
                        if(keycode==27){
                        	tb_remove();
                        }
                    };
                }
    }catch(e){}
};

/**
 * 弹窗信息框
 */
function tb_showmsg(msg, caption, talign, ww, wh)
{
		//默认参数
		if(!caption || caption=="") caption="消息窗口";
		if(!talign) talign = "center";
		if(!ww) ww = "350";
		if(!wh) wh = "180";
		
		if(ww<=1){
			ww=$("body").width()*ww;
		};
		if(wh<=1){
			wh=document.documentElement.clientHeight*wh;
		};
		TB_WIDTH=(ww*1)+100||630;
		TB_HEIGHT=(wh*1)+40||440;
		
		if (typeof document.body.style.maxHeight === "undefined") {
			$("body","html").css({height: "100%", width: "100%"});
			//$("html").css("overflow","hidden");
		}
		if(document.getElementById("TB_overlay") === null){
				$("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
				$("#TB_overlay").click(tb_remove);
		}
		
		if(tb_detectMacXFF()){
			$("#TB_overlay").addClass("TB_overlayMacFFBGHack");
		}else{
			$("#TB_overlay").addClass("TB_overlayBG");
		}
		
		
		$("#TB_window").append("<div id='TB_title'><div id='TB_ajaxWindowTitle'>"+caption+"</div><div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton' title='关闭'>关闭</a></div></div><div id='TB_ajaxContent'><table width='100%'><tr><td valign='middle' style='height:100%;font-size:14px;line-height:28px;' align='"+talign+"'>"+ msg +"</td></tr></table></div>");
		
		$("#TB_closeWindowButton").click(tb_remove);
		tb_position()
		//$("#TB_window")[0].style.width = ww;
		//$("#TB_window")[0].style.height = wh; 
		
		$("#TB_window").css({display:"block"}).css({top:"120px"});
        $("#TB_window").fdrag(true);
		
		//alert(wh);
        if(!params['modal']){
            $("#TB_window").setHandler('TB_title');
            document.onkeyup=function(e){
                if(e==null){
                    keycode=event.keyCode;
                }else{
                    keycode=e.which;
                };
                if(keycode==27){
                    tb_remove();
                }
            };
        }
                
	  document.onkeyup = function(e){ kc = (e == null ? event.keyCode : e.which); if(kc == 27){ tb_remove(); } };
}

function tb_showIframe(){
	$("#TB_load").remove();
    $("#TB_window").css({display:"block"});
};

function tb_remove(){
	$("#TB_imageOff").unbind("click");
    $("#TB_closeWindowButton").unbind("click");
    $("#TB_window").fadeOut("fast",function(){
    	$('#TB_window,#TB_overlay,#TB_HideSelect').trigger("unload").unbind().remove();
        }
    );
    $("#TB_load").remove();
    if(typeof document.body.style.maxHeight=="undefined")
    {
    	$("body","html").css({height:"auto",width:"auto"});
        $("html").css("overflow","");
    };
    document.onkeydown="";
    document.onkeyup="";
    if( ref_parent ) location.reload();
    return;
};

function tb_position(){
	$("#TB_window").css({width:TB_WIDTH+'px'});
    $("#TB_window").fPosition({vpos:"middle",hpos:"center",fw:TB_WIDTH,fh:TB_HEIGHT});
};

function tb_parseQuery(query){
	var Params={};
    if(!query){
    	return Params;
    };
    var Pairs=query.split(/[;&]/);
    for(var i=0;i<Pairs.length;i++){
    	var KeyVal=Pairs[i].split('=');
        if(!KeyVal||KeyVal.length!=2){
        	continue;
        };
        var key=unescape(KeyVal[0]);
        var val=unescape(KeyVal[1]);
        val=val.replace(/\+/g,' ');
        Params[key]=val;
    };
	return Params;
};

function tb_getPageSize(){
	var de=document.documentElement;
    var w=window.innerWidth||self.innerWidth||(de&&de.clientWidth)||document.body.clientWidth;
    var h=window.innerHeight||self.innerHeight||(de&&de.clientHeight)||document.body.clientHeight;
    arrayPageSize=[w,h];
    return arrayPageSize;
};

function tb_detectMacXFF(){
	var userAgent=navigator.userAgent.toLowerCase();
    if(userAgent.indexOf('mac')!=-1&&userAgent.indexOf('firefox')!=-1)
    {
    	return true;
    }
};

/* 让其支持拖动 */
(function($){
	$.fn.fPosition=function(options)
    {
    	var defaults={vpos:null,hpos:null};
        var top;
        var left;
        var options=$.extend(defaults,options);
        return this.each(
        	function(index){
            var $this=$(this);
            $this.css("position","absolute");
            if(jQuery.browser.opera){
            	ftop=((parseInt(window.innerHeight)/2)-(options.fh/2));
                $this.css("top",($(document).scrollTop()+(parseInt(window.innerHeight)/2)-(options.fh/2))+"px");
            } else {
            	ftop=((parseInt($(window).height())/2)-(options.fh/2));
                $this.css("top",($(document).scrollTop()+(parseInt($(window).height())/2)-(options.fh/2))+"px");
            };
            $this.css("left",((parseInt($(window).width())/2)-(options.fw/2))+"px");
            fleft=((parseInt($(window).width())/2)-(options.fw/2));
            }
        );
    };

    var isMouseDown=false;
    var currentElement=null;
    var dropCallbacks={};
    var dragCallbacks={};
    var bubblings={};
    var lastMouseX;
    var lastMouseY;
    var lastElemTop;
    var lastElemLeft;
    var dragStatus={};
    var holdingHandler=false;

    $.getMousePosition=function(e){
        var posx=0;
        var posy=0;
        if(!e)var e=window.event;
        if(e.pageX||e.pageY){
            posx=e.pageX;posy=e.pageY;
        } else if (e.clientX||e.clientY){
            posx=e.clientX+document.body.scrollLeft+document.documentElement.scrollLeft;
            posy=e.clientY+document.body.scrollTop+document.documentElement.scrollTop;
        };
        return{'x':posx,'y':posy};
    };

    $.updatePosition=function(e){
        var pos=$.getMousePosition(e);
        var spanX=(pos.x-lastMouseX);
        var spanY=(pos.y-lastMouseY);
        $(currentElement).css("top",(lastElemTop+spanY));
        $(currentElement).css("left",(lastElemLeft+spanX));
        fleft=lastElemLeft+spanX;
        fftop=spanY;
    };

    $(document).mousemove(
        function(e){
            if(isMouseDown&&dragStatus[currentElement.id]!='false')
            {
                $.updatePosition(e);
                if(dragCallbacks[currentElement.id]!=undefined)
                {
                    dragCallbacks[currentElement.id](e,currentElement);
                };
                return false;
            }
        }
    );

    $(document).mouseup(
        function(e){
            if(isMouseDown&&dragStatus[currentElement.id]!='false')
            {
                isMouseDown=false;
                if(dropCallbacks[currentElement.id]!=undefined){
                    dropCallbacks[currentElement.id](e,currentElement);
                };
                return false;
            }
        }
    );

    $.fn.ondrag=function(callback){
        return this.each(
            function(){
                dragCallbacks[this.id]=callback;
            }
        );
    };

    $.fn.ondrop=function(callback){
        return this.each(
            function(){
                dropCallbacks[this.id]=callback;
            }
        );
    };

    $.fn.dragOff=function(){
        return this.each(
            function(){
                dragStatus[this.id]='off';
            }
        );
    };

    $.fn.dragOn=function(){
        return this.each(
            function(){
                dragStatus[this.id]='on';
            }
        );
    };

    $.fn.setHandler=function(handlerId){
        return this.each(
            function(){
                var draggable=this;
                bubblings[this.id]=true;
                $(draggable).css("cursor","");
                dragStatus[draggable.id]="handler";
                $("#"+handlerId).css("cursor","move");
                $("#"+handlerId).mousedown(function(e){
                    holdingHandler=true;
                    $(draggable).trigger('mousedown',e);
                }
                );
                $("#"+handlerId).mouseup(
                    function(e){
                        holdingHandler=false;
                    });
            }
        );
    };


    $.fn.fdrag=function(allowBubbling){
        return this.each(function(){
                if(undefined==this.id||!this.id.length) this.id="easydrag"+(new Date().getTime());
                bubblings[this.id]=allowBubbling?true:false;
                dragStatus[this.id]="on";
                $(this).css("cursor","move");
                $(this).mousedown(
                    function(e){
                        if((dragStatus[this.id]=="off")||(dragStatus[this.id]=="handler"&&!holdingHandler)) return bubblings[this.id];
                        $(this).css("position","absolute");
                        $(this).css("z-index",parseInt(new Date().getTime()/1000));
                        isMouseDown=true;
                        currentElement=this;
                        var pos=$.getMousePosition(e);
                        lastMouseX = pos.x;
                        lastMouseY = pos.y;
                        lastElemTop=this.offsetTop;
                        lastElemLeft=this.offsetLeft;
                        $.updatePosition(e);
                        return bubblings[this.id];
                    }
                );
            }
        );
    };
})(jQuery);

/* 滚动条自适应 */
$(window).scroll(
	function(){
    	if(ftop2!=fftop){
        	ftop1=ftop1+fftop;ftop2=fftop;
        };
        $("#TB_window").css("top",(ftop+ftop1+$(document).scrollTop())+"px").css("left",(fleft+$(document).scrollLeft())+"px");
    }
);