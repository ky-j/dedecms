<!--
//window.onload=function(){FCKeditor_OnComplete()}
//function FCKeditor_OnComplete()
//{
//	var editor = FCKeditorAPI.GetInstance('content') ;
//	editor.Events.AttachEvent('OnSelectionChange', editor_keydown);
//}  
//String.prototype.realLength = function(){return this.replace(/[^\x00-\xff]/g, "**").length;} 
//String.prototype.Trim = function(){return this.replace(/(^\s*)|(\s*$)/g, "");}
//function editor_keydown(editor)
//{
//	var maxl = 9999;
//	var s = editor.GetData();
//	var ss = s.realLength();
//	len = maxl - ss;
//	if(ss > maxl) editor.SetHTML(s.substr(0,maxl-1));
//	else $("#count").html(len);
//}

function quickSubmit(event) {
	if (event.ctrlKey && event.keyCode == 13) {
		answer.submit()
	}
}

function checkform(f,des) {
	if(f.content.value=="") {
		alert("请输入您的"+des);
		f.content.focus();
		return false;
	}
}

function rate(divid,id,type){
	var taget_obj = document.getElementById(divid);
	var myajax = new DedeAjax(taget_obj,false,false,"","","");
	myajax.SendGet2("?ct=question&ac=rate&askaid="+id+"&type="+type);
	DedeXHTTP = null;
}

//修改答复
function edit_reply(id){
	if(id >= 0)
	{
    	tb_show('修改答复', '?ct=question&ac=reply&id='+id+'&height=600&amp;width=700', true);
	}else{
		var msg = "没有选择正确信息";
		msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;";
		tb_showmsg(msg);
	}
}

function del(id,type)
{
    if(id <= 0)
    {
        var msg = "没有选择正确信息";
		msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;";
		tb_showmsg(msg);  
    }else if(type == 1){
        var msg = "你确定要删除该提问?";
        msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;|&nbsp; <a href='?ct=question&ac=del&askaid="+id+"'>确定要删除&gt;&gt;</a>";
    }else if(type == 2){
        var msg = "你确定要删除该评论?";
        msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;|&nbsp; <a href='?ct=question&ac=comment_del&id="+id+"'>确定要删除&gt;&gt;</a>";
    }else if(type == 3){
        var msg = "你确定要删除该回复?";
        msg += "<br/><a href='javascript:tb_remove();'>&lt;&lt;点错了</a> &nbsp;|&nbsp; <a href='?ct=question&ac=reply_del&id="+id+"'>确定要删除&gt;&gt;</a>";
    }
    
    tb_showmsg(msg);
}
-->