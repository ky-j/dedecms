<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 
 * @version        2011/2/11  沙羡 $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2011, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 *
 **/
 
class answer extends Control
{
    function answer()
	{
		parent::__construct();
		$this->temp = DEDEAPPTPL.'/admin';
		$this->lurd = new Lurd('#@__askanswer', $this->temp, $this->temp.'/lurd');
        $this->lurd->appName = "答案管理";
        $this->lurd->isDebug = FALSE;  //开启调试模式后每次都会生成模板
        $this->lurd->stringSafe = 2;  //默认1(只限制不安全的HTML[script、frame等]，0--为不限，2--为不支持HTML
        //获取url
        $this->currurl = GetCurUrl();
        //载入模型
        $this->answer = $this->Model('askanswer');
        $this->question = $this->Model('mquestion');
	}
	
    function ac_index()
    {
        //指定某字段为强制定义的类型
        $this->ac_list();
    }
    
    //列出答案
    function ac_list()
    {
        $ifcheck = request('ifcheck', '2');
        $askid = request('askid', '');
        if($ifcheck == 0)
		{
		     $wherequery = "WHERE ifcheck = 0";
		     $this->lurd->SetParameter('ifcheck',0);
		}else if($ifcheck == 1){
		     $wherequery = "WHERE ifcheck = 1";
		     $this->lurd->SetParameter('ifcheck',1);
		}else{
		     $wherequery = "";
		}
		if($askid)
		{
		     $wherequery .= "WHERE askid =".$askid;
		     $this->lurd->SetParameter('askid',$askid);
		}
		$orderquery = "ORDER BY id DESC ";
        //指定每页显示数
        $this->lurd->pageSize = 20;
        //指定某字段为强制定义的类型
        $this->lurd->BindType('dateline', 'TIMESTAMP', 'Y-m-d H:i');
        //获取数据
        $this->lurd->ListData('id,askid,uid,username,dateline,content,ifcheck', $wherequery, $orderquery);
        exit();
    }
    
    //审核
	function ac_check()
    {
        $ids = request('id', '');
        if(!is_array($ids))
        {
            ShowMsg('未选择要审核的答案!','-1');
		    exit();	 
        }
		$rs = $this->answer->check($ids);
		if($rs)
		{
    		ShowMsg("审核成功！",$this->currurl);
    		exit();	
    	}else{
    	    ShowMsg("审核失败！",$this->currurl);
    		exit();	
    	} 
    }
    
    //删除
	function ac_delete()
    {
        $ids = request('id', '');
        if(!is_array($ids))
        {
            ShowMsg('未选择要删除的答案!','-1');
		    exit();	 
        }
        foreach($ids as $id)
		{
			$id = preg_replace("#[^0-9]#","",$id);
            if($id=="") continue;
			$rs = $this->answer->del($id);
			if(!$rs)
    		{
        		ShowMsg("删失败！",$this->currurl);
        		exit();	
        	}
		}
	    $this->question->update();
	    ShowMsg("删除成功！",$this->currurl);
		exit();	
    }
    
    //监听删除修改等操作
    function ac_listenall()
    {
        global $ac;
        $ac = request('bc', '');
        $this->lurd->ListenAll();
        exit();
    }
}
?>