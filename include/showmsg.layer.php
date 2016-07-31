<?php
/**
 *  短消息函数,可以在某个动作处理后友好的提示信息
 *
 * @param     string  $msg      消息提示信息
 * @param     string  $gourl    跳转地址
 * @param     int     $onlymsg  仅显示信息
 * @param     int     $limittime  限制时间
 * @return    void
 */
function ShowMsg($msg, $gourl, $onlymsg=0, $limittime=0)
{
    $jquerUrl = "http://libs.sun0769.com/jquery/1.11.3/jquery.min.js";
    $layerUrl = "http://libs.sun0769.com/layer/2.3/layer.js";

    if(empty($GLOBALS['cfg_plus_dir'])) $GLOBALS['cfg_plus_dir'] = '..';

    $litime = ($limittime==0 ? 1000 : $limittime);

    if($gourl=='-1')
    {
        if($limittime==0) $litime = 5000;
        $gourl = "javascript:history.go(-1);";
    }

    $msg = str_replace('"','\"',$msg);
    $msg_str =
<<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$GLOBALS['cfg_soft_lang']}" />
<script src="{$jquerUrl}"></script>
<script src="{$layerUrl}"></script>
</head>
<body>
<script type="text/javascript">
var pgo=0;
function JumpUrl(){
    if(pgo==0){
        location=' {$gourl}'; pgo=1;
    }
}
setTimeout('JumpUrl()',$litime);
function showmsg(){
    layer.msg('{$msg}', {
      icon: 1,
      time: {$litime} //2秒关闭（如果不配置，默认是3秒）
    }, function(){
      JumpUrl();
      return;
    })
}
showmsg();
</script>
</body>
</html>
EOT;

	//如果gourl为空或者设定为alert模式
	if($gourl=='' || $onlymsg==1)
	{
		$msg_str = "<script>alert(\"".str_replace("\"","“",$msg)."\");</script>";
	}

	echo $msg_str;
}
