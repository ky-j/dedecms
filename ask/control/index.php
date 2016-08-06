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
 
class index extends Control
{
    // 为兼容PHP4需使用同名函数作为析构
    function index()
    {
        //载入模型
        parent::__construct();
        $this->question = $this->Model('mquestion');
        $this->scores = $this->Model('mscores');
    }
    
    function ac_index()
    { 
        global $cfg_soft_lang;
        $row = 8;
        //推荐问题
        $digests = $this->question->get_digests(7);
       
        //待解决的问题
        $notoks = $this->question->get_all('status=0','ORDER BY disorder DESC, dateline DESC',$row);
        
        //新解决的问题
        $solutions = $this->question->get_all('status=1','ORDER BY solvetime DESC',$row);
		 
		//高分悬赏问题
        $rewards = $this->question->get_all('status=0','ORDER BY reward DESC',$row);
		
		//获取问题数
		$solvenum = $this->question->get_total();
		
		//首页幻灯片
		if(file_exists(DEDEASK."/data/cache/slide.inc")) {
            require_once(DEDEASK."/data/cache/slide.inc");
            if($cfg_soft_lang == 'utf-8')
            {
                $row = AutoCharset(unserialize(utf82gb($data)));
            }else{
                $row = unserialize($data); 
            } 
        }
        
        //处理链接地址
	    if($GLOBALS['cfg_ask_rewrite'] == 'Y')
	    {
	        $digests = makerewurl($digests,'id');
	        $notoks = makerewurl($notoks,'id');
	        $solutions = makerewurl($solutions,'id');
	        $rewards = makerewurl($rewards,'id');
	    }else{
	        $digests = makeurl($digests,'id');
	        $notoks = makeurl($notoks,'id');
	        $solutions = makeurl($solutions,'id');
	        $rewards = makeurl($rewards,'id');
	    }
        
        //设定变量值
        $GLOBALS['row'] = $row;
		$GLOBALS['digests'] = $digests;
		$GLOBALS['notoks'] = $notoks;
		$GLOBALS['rewards'] = $rewards;
		$GLOBALS['solutions'] = $solutions;
		$GLOBALS['solvenum'] = $solvenum;
		//载入模板
		$this->SetTemplate('index.htm');
        $this->Display();
    }
    
    //更新总积分排行
    function ac_scores()
    {
        $memberlists = $this->scores->get_scores();
        if(count($memberlists) > 0)
        {   
            $row = serialize($memberlists);
            $configstr = "<"."?php\r\n\$memberlists = '".$row."';";
            file_put_contents(DEDEASK.'/data/cache/scores.inc', $configstr);	
        }
    }
}
?>