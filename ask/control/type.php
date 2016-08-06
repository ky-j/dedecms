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
 
class type extends Control
{
    function type()
	{
	    parent::__construct();
		$this->type = $this->Model('mtype');
	}
	
    function ac_index()
    {
        $asktypes = $this->type->get_alltype();
        //当前位置
		$nav = $GLOBALS['cfg_ask_position'].'<a href="#">全部分类</a>';
		if(!count($asktypes) > 0)
		{
            ShowMsg('目前还没有分类，请浏览其他内容！','-1');
    	    exit(); 
		}
		//设定变量值
		$GLOBALS['nav'] = $nav;
		$GLOBALS['asktypes'] = $asktypes;
		//载入模板
		$this->SetTemplate('type.htm');
        $this->Display();
    }
}
?>