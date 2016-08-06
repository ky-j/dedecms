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
 
class browser extends Control
{	
	function browser()
	{
		parent::__construct();
		//载入帮助
		$this->helper('question',DEDEASK);
	}
	
	function ac_index()
	{
		$tid = request('tid', '');
		$tid  = is_numeric($tid)? $tid : 0;
		$tid2 = request('tid2', '');
		$tid2  = is_numeric($tid2)? $tid2 : 0;
		$lm = request('lm', '');
		$lm  = is_numeric($lm)? $lm : 0;
		$appname = request('appname', '');
		$page = request('page', '');
		$page  = is_numeric($page)? $page : 0;
		$subtypeinfos = array();
		$tidstr = $nav = $navtitle = $multistr = $wheresql = '';
		
		if($tid == 0 && $tid2 == 0 && $lm == 0 && $appname == 0)
		{
			ShowMsg("提交的数据有误!","index.php");
            exit();
		}
		if($tid)
		{
			$this->dsql->Execute('me',"SELECT * FROM `#@__asktype` WHERE id='{$tid}'");
			if(!$typeinfo = $this->dsql->getarray())
			{
				ShowMsg('指定栏目不存在，请返回','index.php');
				exit;
			}
			$wheresql .= "tid='{$tid}' ";
			$multistr .="tid={$tid}";
			$tidstr = "tid={$tid}";
			$navtitle = $typeinfo['name'];
			$nav = " <a href=\"?ct=browser&tid={$tid}\">".$typeinfo['name'].'</a>'.$GLOBALS['cfg_ask_symbols'];
			$toptypeinfo = $typeinfo;
		
		}elseif($tid2){
			$this->dsql->Execute('me',"SELECT * FROM `#@__asktype` WHERE id='{$tid2}' ");
			if(!$typeinfo = $this->dsql->getarray())
			{
				ShowMsg('指定栏目不存在，请返回','index.php');
				exit;
			}
			$wheresql .= "tid2='{$tid2}'";
			$multistr .="tid2={$tid2}";
			$tidstr = "tid2={$tid2}";
			$toptypeinfo = $this->dsql->getone("SELECT id, name, asknum FROM `#@__asktype` WHERE id='".$typeinfo['reid']."' LIMIT 1");
			$navtitle = $typeinfo['name'].' '.$toptypeinfo['name'];
			$nav .= " <a href=\"?ct=browser&tid=".$toptypeinfo['id']."\">".$toptypeinfo['name']."</a> {$GLOBALS['cfg_ask_symbols']} <a href=\"?ct=browser&tid2=".$tid2."\">".$typeinfo['name']."</a>".$GLOBALS['cfg_ask_symbols'];
		}
		
		if($tid || $tid2)
		{
			$query = "SELECT id, name, asknum FROM #@__asktype WHERE reid='".$toptypeinfo['id']."' ORDER BY disorder asc, id asc";
			$this->dsql->Execute('me',$query);
			while($row = $this->dsql->getarray())
			{
				$subtypeinfos[] = $row;
			}
		}
		
		if(!empty($appname))
		{
		    $wheresql .= " and appname = '{$appname}'";
		    $apname = '';
		    if($appname == 1) $apname = 'DedeCMS';
		    else if($appname == 2) $apname = 'DedeEIMS';
		    else if($appname == 3) $apname = '织梦淘宝客';
		    $nav .= ' '.$GLOBALS['cfg_ask_symbols'].' '.$apname;
		    if(!$tid && !$tid2)
		    {
		        $toptypeinfo['name'] = $apname;
		        $multistr .= $mulappname ="appname={$appname}";
		    }else{
		        $multistr .= $mulappname ="&appname={$appname}";
		    }
		}else{
		    $mulappname = "";
		}
				
		$orderby = 'ORDER BY';
		$all = array( 0 => "",1 => "",2 => "",3 => "",4 => "",5 => "",6 => "");
		if(empty($lm))
		{
			$wheresql .= ' and status>=0';
			$orderby .= ' disorder DESC, dateline DESC';
			$all[0] = ' class="select"';
		}elseif($lm == 1){
			//精彩问题
			$wheresql .= ' and digest=1';
			$orderby .= ' replies DESC, dateline DESC';
			$nav .= ' 精彩推荐';
			$all[1] = ' class="thisclass"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '精彩推荐';
		}elseif($lm == 2){
			//待解决
			$wheresql .= ' and status=0';
			$orderby .= ' disorder DESC, dateline DESC';
			$nav .= ' 待解决问题';
			$all[2] = ' class="select"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '待解决问题';
		}elseif($lm == 3){
			//已解决
			$wheresql .= ' and status=1';
			$orderby .= ' solvetime DESC';
			$nav .= ' 待解决问题';
			$all[3] = ' class="select"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '待解决问题';
		}elseif($lm == 4){
			//高分
			$wheresql .= ' and status=0';
			$orderby .= ' reward DESC';
			$nav .= ' 高分问题';
			$all[4] = ' class="select"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '高分问题';
		}elseif($lm == 5){
			//零回答
			$wheresql .= ' and replies=0 and status=0';
			$orderby .= ' disorder DESC, dateline DESC';
			$nav .= ' 零回答问题';
			$all[5] = ' class="select"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '零回答问题';
		}elseif($lm == 6){
			//快到期
			$wheresql .= ' and status=0';
			$orderby .= ' expiredtime asc, dateline DESC';
			$nav .= ' 快到期问题';
			$all[6] = ' class="select"';
			if(!$tid && !$tid2) $toptypeinfo['name'] = '快到期问题';
		}else{
			ShowMsg('指定栏目不存在，请返回','index.php');
			exit;
		}
		
		if(!empty($lm) && ($tid || $tid2 || $appname)) $multistr .="&lm={$lm}";
		else if(!empty($lm)) $multistr .="lm={$lm}";
		$navtitle = $navtitle == '' ? $GLOBALS['cfg_ask_sitename'] : $navtitle.' '.$GLOBALS['cfg_ask_sitename'];
		$nav = $GLOBALS['cfg_ask_position'].$nav;
		
		$wheresql = trim($wheresql);
		if(preg_match("#^and#",$wheresql)) $wheresql = substr($wheresql,3);
		$wheresql = 'WHERE '.trim($wheresql);

		$row = $this->dsql->GetOne("SELECT count(*) as dd FROM `#@__ask` $wheresql");
		$askcount = $row['dd'];
		$realpages = @ceil($askcount/$GLOBALS['cfg_ask_tpp']);
		if($page > $realpages) $page = $realpages;
		$page = isset($page) ? max(1, intval($page)) : 1;
		$start_limit = ($page - 1) * $GLOBALS['cfg_ask_tpp'];
		$multipage = multi($askcount, $GLOBALS['cfg_ask_tpp'], $page, "?ct=browser&$multistr");
		
		$query = "SELECT id, tid, tidname, tid2, tid2name, title, reward, dateline, status, expiredtime solvetime, replies
		FROM `#@__ask` $wheresql $orderby LIMIT $start_limit, {$GLOBALS['cfg_ask_tpp']}";
		
		$this->dsql->Execute('me',$query);
		$asks = array();
		while($row = $this->dsql->getarray())
		{
			if($row['status'] == 1) $row['status'] = 'qa_ico_2.jpg'; //已解决
			else if($row['status'] == 2) $row['status'] = 'qa_ico_2.jpg'; //关闭
			else if($row['status'] == 3) $row['status'] = 'qa_ico_2.jpg'; //过期
            else $row['status'] = 'qa_ico_1.gif'; //正常
            if($GLOBALS['cfg_ask_rewrite'] == 'Y') $row['qurl'] = $row['id'].'.html';
            else $row['qurl'] = '?ct=question&askaid='.$row['id'];
			$asks[] = $row;
		}
		
		//设定变量值
        $arrs = array('asks','toptypeinfo','subtypeinfos','nav','tid','tid2','all','mulappname','tidstr',
		              'multipage','appname');
		foreach ($arrs as $val) {
            $GLOBALS[$val] = $$val;  
		}
		//载入模板
		$this->SetTemplate('browser.htm');
        $this->Display();
	}
		
}

?>