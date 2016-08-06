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
 
class question extends Control
{
    function question()
	{
	    parent::__construct();
		global $cfg_ml,$cfg_ask_guestview,$cfg_ask_guestask;
		$this->cfg_ml = $cfg_ml;
		if($cfg_ask_guestview == 'N' && empty($this->cfg_ml->M_ID))
		{
			ShowMsg('您尚未登录，请先登录',$GLOBALS['cfg_ask_member']);
			exit;
		}

		//载入帮助
		$this->helper('question',DEDEASK);
		//载入模型
        $this->question = $this->Model('mquestion');
        $this->type = $this->Model('mtype');
        $this->scores = $this->Model('mscores');
        $this->answer = $this->Model('askanswer');
        $this->comment = $this->Model('askcomment');
        
	}
	
	//浏览问题
    function ac_index()
    {
        $askaid = request('askaid', '');
        $askaid = is_numeric($askaid)? $askaid : 0;
        if(!is_numeric($askaid))
        {
            ShowMsg("您提交的参数有问题",'-1');
            exit;  
        }
		$question = $this->question->get_info($askaid);
		if(!is_array($question))
		{
            ShowMsg("您浏览的问题不存,请重新操作!",'-1');
            exit;  
		}
		if($question)
		{
			if($question['status'] == 1)
			{
				$question['dbstatus'] = 1;
				$question['status'] = 'solved';
			}else if($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
				$question['dbstatus'] = 2;
				$question['status'] = 'epired';
				//设置一个问题已过期
				$set = "solvetime=expiredtime, status = '2'";
				$wheresql = "id='{$askaid}'";
				$this->question->update_ask($set,$wheresql);
			}else if($question['status'] == -1 && $question['uid'] != $this->cfg_ml->M_ID){
				ShowMsg('该问题还未通过审核,请耐心等待...', '-1');
			    exit;
			}else{
				$question['dbstatus'] = 0;
				$question['status'] = 'no_solve';
			}
			$question['toendsec'] = $question['expiredtime'] - $GLOBALS['cfg_ask_timestamp'];
			$question['toendday'] = floor($question['toendsec']/86400);
			$question['toendhour'] = floor(($question['toendsec']%86400)/3600);
			//头像
			$question['face'] = empty($question['face'])? 'static/images/user.gif' : $question['face'];
			//判断问题是否属于当前登陆者
			$publisher = 0;
			if($question['uid'] == $this->cfg_ml->M_ID) $publisher = 1;
		}else{
			ShowMsg('回答的问题不存在', '-1');
			exit;
		}
	
		//获取积分头衔
		$question['honor'] = gethonor($question['scores']);
		//网站title
		$navtitle = $question['title'].'-'.$GLOBALS['cfg_ask_sitename'];
		//当前位置
		$nav = $GLOBALS['cfg_ask_position'].' <a href="?ct=browser&tid='.$question['tid'].'">'.$question['tidname'].'</a>';
		if($question['tid2'])
		{
			$nav .= ' '.$GLOBALS['cfg_ask_symbols'].' <a href="?ct=browser&tid2='.$question['tid2'].'">'.$question['tid2name'].'</a>';
			$navtitle .= ' '.$question['tid2name'];
		}
		$nav .=' '.$GLOBALS['cfg_ask_symbols'].' '.$question['title'];		
	    
	    //获取问题的答案
		$rows = $this->answer->get_answers($askaid);
		$answers = array();
		$first = $goodrateper = $badrateper = $goodrate = $badrate = $ratenum = $answernum = $myanswer = 0;
		if(count($rows) > 0){
    		foreach ($rows as $key => $row) {
                //获取回答者的积分等级
    			$row['honor'] = gethonor($row['scores']);
    			//判断问题是否是自己回答的问题
    			if($this->cfg_ml->M_ID == $row['uid']) $myanswer = 1;
    			//判断是否已经有最佳答案了
    			if($row['id'] == $question['bestanswer'])
    			{
    				$digestanswer = $row;
    				$ratenum = $row['goodrate'] + $row['badrate'];
    				$goodrate = $row['goodrate'];
    				$badrate = $row['badrate'];
    				$goodrateper = @ceil($goodrate*100/$ratenum);
    				$badrateper = 100 - $goodrateper;
    				//设定变量值
                    $arrs = array('digestanswer','goodrate','badrate','goodrateper','badrateper','ratenum');
            		foreach ($arrs as $val) {
                        $GLOBALS[$val] = $$val;  
            		}
    			}else{
    			    $answernum = $answernum + 1;
    			    $row['floor'] = $answernum;
    				$answers[] = $row;
    			}
    		}
    	}
    	//设定变量值
        $arrs = array('nav','navtitle','question','publisher','myanswer','answernum','answers');
		foreach ($arrs as $val) {
            $GLOBALS[$val] = $$val;  
		}
		//载入模板
		$this->SetTemplate('question.htm');
        $this->Display();
    }

	//提问问题
	function ac_ask()
	{
        //载入模板
		$this->SetTemplate('ask1.htm');
        $this->Display();
	}
	
	//提问问题
	function ac_ask_complete()
	{
	    $title = request('title', '');
	    $title = strip_tags($title);
	    //获取栏目信息
		$tids = "var class_level_1=new Array( \n";
		$tid2s = "var class_level_2=new Array( \n";
		foreach($GLOBALS['asktypes'] as $asktype) {
			if($asktype['reid'] == 0){
				$tids .= 'new Array("'.$asktype['id'].'","'.$asktype['name'].'"),'."\n";
			}else{
				$tid2s .= 'new Array("'.$asktype['reid'].'","'.$asktype['id'].'","'.$asktype['name'].'"),'."\n";
			}
		}
		$tids = substr($tids,0,-2)."\n";
		$tid2s = substr($tid2s,0,-2)."\n";
		$tids .= ');';
		$tid2s .= ');';
		//网站title
		$navtitle = '提问-'.$GLOBALS['cfg_ask_sitename'];
		//当前位置
		$nav = $GLOBALS['cfg_ask_position'].' 提问';
        //设定变量值
        $arrs = array('nav','tids','tid2s','navtitle');
		foreach ($arrs as $val) {
            $GLOBALS[$val] = $$val;  
		}
	    //载入模板
		$this->SetTemplate('ask3.htm');
        $this->Display();
	}

	//保存提问问题
	function ac_ask_save()
	{
		$data['title'] = strip_tags(request('title', ''));
		$data['content'] = request('content', '');
		$data['anonymous'] = request('anonymous', '');
		$data['reward'] = request('reward', '');
		$data['scores'] = request('scores', '');
		$data['faqkey'] = request('faqkey', '');
		$data['vdcode'] = request('vdcode', '');
		$data['safeanswer'] = request('safeanswer', '');
		$ClassLevel1 = request('ClassLevel1', '');
		$ClassLevel2 = request('ClassLevel2', '');
		$data['uid'] = $this->cfg_ml->M_ID;
		$data['timestamp'] = $GLOBALS['cfg_ask_timestamp'];
		$data['scores'] = empty($data['scores'])? 0 : intval(preg_replace("/[\d]/",'', $data['scores']));
		//检查问题名称
		if($data['title'] == '')
		{
			ShowMsg('问题名称不能为空',"-1");
			exit;
		}else if(strlen($data['title']) > 80){
			ShowMsg('问题不能大于80字节',"-1");
			exit;
		}else if(strlen($data['title']) < 8){
			ShowMsg('问题不能小于8字节',"-1");
			exit;
		}
		//检查问题内容
		if(empty($data['content']))
		{
			ShowMsg('问题说明内容不能为空!',"-1");
			exit;
		}
	    //检查验证码
		if(preg_match("#7#",$GLOBALS['safe_gdopen'])){
		    $svali = GetCkVdValue();
            if(strtolower($data['vdcode']) != $svali || $svali=='')
            {
                ResetVdValue();
                ShowMsg('验证码错误！', '-1');
                exit();
            }
        }
        //检查验证问题
        $faqkey = isset($data['faqkey']) && is_numeric($data['faqkey']) ? $data['faqkey'] : 0;
        if($GLOBALS['gdfaq_ask'] == 'Y')
        {
            global $safefaqs; 
            if($safefaqs[$faqkey]['answer'] != $data['safeanswer'] || $data['safeanswer'] =='')
            {
                ShowMsg('验证问题答案错误', '-1');
                exit();
            }
        }
        $data['title'] = preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($data['title'], 1));
        $data['content']  = preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($data['content'], -1));
		$data['anonymous'] = (!empty($data['anonymous'])) ? 1 : 0;
		$data['tid'] = $data['tid2']  = 0;
		$data['tidname'] = $data['tid2name'] = '';
		$data['userip'] = getip();
		$data['reward'] = intval($data['reward']);
		if($data['reward'] < 0) $data['reward'] = 0;
		//采用匿称时对扣除相应的积分
		$needscore = $data['anonymous'] * $GLOBALS['cfg_ask_anscore'] + $data['reward'];
		//判断积分情况
		if($this->cfg_ml->M_Scores < $needscore)
		{
			ShowMsg('积分不足，请核查所需积分','-1');
			exit;
		}
		//处理栏目
		$ClassLevel1 = intval($ClassLevel1);
		if($ClassLevel1 < 1)
		{
			ShowMsg('未指定栏目或栏目id不正确，请返回','-1');
			exit;
		}
		$ClassLevel2 = intval($ClassLevel2);
		if($ClassLevel2 != 0) $where = " WHERE id in ($ClassLevel1,$ClassLevel2)";
		else $where = "WHERE id='$ClassLevel1'";
		$rows = $this->type->get_asktype($where);
		foreach ($rows as $row) {
			if($row['id'] == $ClassLevel1)
			{
				$data['tidname'] = $row['name'];
				$data['tid'] = $row['id'];
			}elseif($row['id'] == $ClassLevel2 && $row['reid'] == $ClassLevel1){
				$data['tid2name'] = $row['name'];
				$data['tid2'] = $row['id'];
			}
		}
	
		//计算过期时间
		$data['expiredtime'] = $GLOBALS['cfg_ask_timestamp'] + 86400 * $GLOBALS['cfg_ask_expiredtime'];
		//检查在有效期内是否存在同样的问题
		$rs = $this->question->get_title($data['uid'],$data['title']);
		if($rs){
            ShowMsg('请不要重复发布同一问题,请耐心等待解答..', "index.php");
    		exit; 
		}
		//保存问题
		$rs = $this->question->save_ask($GLOBALS['cfg_ask_ifcheck'],$data);
		if($rs) 
		{
		    //获取最大的id
		    $maxid = $this->question->get_maxid($GLOBALS['cfg_ask_timestamp']);
    		//更新栏目统计信息
    		$this->type->update_asktype($data['tid']);
    		if($data['tid2'] > 0) $this->type->update_asktype($data['tid2']);
    		//积分处理
    		$this->scores->update_scores($data['uid'],$needscore);
    		//清理附加的缓存，并将id写入数据库
    		clearmyaddon($maxid, $data['title']);
    		ShowMsg('发布提问成功，如果未显示问题，说明管理人员尚未审核你的问题', "?ct=question&askaid=".$maxid);
    		exit; 
    	}else{
    		ShowMsg('发布提问失败', "-1");
    		exit; 
    	}	
	}
	
	//修改问题
	function ac_edit()
	{
        global $config;
	    $askaid = request('askaid', '');
	    $askaid = is_numeric($askaid)? $askaid : 0;
		//获取问题的基本信息
		$question = $this->question->get_one("id='{$askaid}'");
		//对问题进行判断
		if(!is_array($question))
		{
		    ShowMsg('非法操作，请返回','-1');
			exit;
		}
		if($this->cfg_ml->isAdmin != 1)
		{
    		if($question['uid'] != $this->cfg_ml->M_ID)
    		{
    			ShowMsg('非法操作，请返回','-1');
    			exit;
    		}else if($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
    			ShowMsg('问题已经过期','-1');
    			exit;
    		}else if($question['status'] >= 1){
    			ShowMsg('问题已经解决或者已过期,不能被修改!','-1');
    			exit;
    		}
    	}
		//网站title
		$navtitle = '问题修改-'.$GLOBALS['cfg_ask_sitename'];
		//当前位置
		$nav = $GLOBALS['cfg_ask_position'].' 问题修改';
		//设定变量值
        $arrs = array('nav','question','navtitle');
		foreach ($arrs as $val) {
            $GLOBALS[$val] = $$val;  
		}
	    //载入模板
		$this->SetTemplate('ask_edit.htm');
        $this->Display();
	}
	
	//保存修改问题
	function ac_edit_save()
	{
	    $data['askaid'] = request('askaid', '');
	    $data['askaid'] = is_numeric($data['askaid'])? $data['askaid'] : 0;
	    $data['title'] = request('title', '');
		$data['content'] = request('content', '');
		$data['faqkey'] = request('faqkey', '');
		$data['vdcode'] = request('vdcode', '');
		$data['safeanswer'] = request('safeanswer', '');
		//获取问题的基本信息
		$question = $this->question->get_one("id='{$data['askaid']}'");
		//对问题进行判断
		if($question['uid'] != $this->cfg_ml->M_ID && $this->cfg_ml->isAdmin != 1)
		{
			ShowMsg('非法操作，请返回','-1');
			exit;
		}else if($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']  && $this->cfg_ml->isAdmin != 1){
			ShowMsg('问题已经过期','-1');
			exit;
		}else if($question['status'] == 1 && $this->cfg_ml->isAdmin != 1){
			ShowMsg('问题已经解决,不能被修改!','-1');
			exit;
		}
		//检查问题名称
		if($data['title'] == '')
		{
			ShowMsg('问题名称不能为空');
			exit;
		}else if(strlen($data['title']) > 80){
			ShowMsg('问题不能大于80字节');
			exit;
		}else if(strlen($data['title']) < 8){
			ShowMsg('问题不能小于8字节');
			exit;
		}
		//检查问题内容
		if(empty($data['content']))
		{
			ShowMsg('问题说明内容不能为空!');
			exit;
		}
	    //检查验证码
		if(preg_match("#7#",$GLOBALS['safe_gdopen'])){
		    $svali = GetCkVdValue();
            if(strtolower($data['vdcode']) != $svali || $svali=='')
            {
                ResetVdValue();
                ShowMsg('验证码错误！', '-1');
                exit();
            }
        }
        //检查验证问题
        $faqkey = isset($data['faqkey']) && is_numeric($data['faqkey']) ? $data['faqkey'] : 0;
        if($GLOBALS['gdfaq_ask'] == 'Y')
        {
            global $safefaqs; 
            if($safefaqs[$faqkey]['answer'] != $data['safeanswer'] || $data['safeanswer'] =='')
            {
                ShowMsg('验证问题答案错误', '-1');
                exit();
            }
        }
        $data['title']  = preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($data['title'], 1));
        $data['content']  = preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($data['content'], -1));
        //保存修改问题
        $set = "title = '{$data['title']}',content = '{$data['content']}'";
        $wheresql = "id ='{$data['askaid']}'";
        $rs = $this->question->update_ask($set,$wheresql);
        if($rs)
        {
            //保存附加信息
    		if($addition == 1) $this->question->update_additions($addi,$data['askaid']);
            clearmyaddon($data['askaid'], $data['title']);
			ShowMsg("编辑成功!","?ct=question&askaid=".$data['askaid']);
			exit;
		}else{
		    ShowMsg("编辑失败!","?ct=question&askaid=".$data['askaid']);
			exit; 
		}
	}

	//删除问题
	function ac_del()
	{
	    $askaid = request('askaid', '');
	    $askaid = is_numeric($askaid)? $askaid : 0;
		if($this->cfg_ml->isAdmin != 1)
		{
			ShowMsg('非法操作，请返回','-1');
			exit;
		}
		$rs = $this->question->del($askaid);
		if($rs) 
		{
		    $this->question->update();
			ShowMsg("删除成功！","index.php");
			exit;
		}else{
		    ShowMsg("删除失败！","index.php");
			exit; 
		}
	}
	
	//提高悬赏
	function ac_upreward()
	{
	   	$askaid = request('askaid', '');
	   	$askaid = is_numeric($askaid)? $askaid : 0;
	   	$step = request('step', '');
	   	$upreward = request('upreward', '');
	   	$uid = $this->cfg_ml->M_ID;
	   	//获取问题的基本信息
        $wheresql = "id='{$askaid}' AND status='0'";
        $field = "id, uid, dateline, solvetime, status, expiredtime,reward";
		$question = $this->question->get_one($wheresql,$field);
		if($question)
		{
			if($question['uid'] != $uid)
			{
				ShowMsg('非法操作，请返回','-1');
				exit;
			}elseif($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
				ShowMsg('问题已经过期','-1');
				exit;
			}
		}else{
			ShowMsg('回答的问题不存在','-1');
			exit;
		}
	
		if(empty($step))
		{
		    //设定变量值
            $GLOBALS['question'] = $question;
		    //载入模板
    		$this->SetTemplate('upreward.htm');
            $this->Display();
		}else{
			$upreward = intval($upreward);
			$upreward = max(0,$upreward);
	
			if($upreward > $this->cfg_ml->M_Scores)
			{
				ShowMsg('积分不足，请核查所需积分','-1');
				exit;
			}
	        //积分处理
    		$this->scores->update_scores($uid,$upreward);
			//保存提高悬赏
			$rs = $this->question->update_ask("reward=reward+{$upreward}","id='{$askaid}'");
			if($rs){
    			ShowMsg('修改积分成功!',"?ct=question&askaid=".$askaid);
    			exit();
    		}else{
    		    ShowMsg('修改积分失败!',"?ct=question&askaid=".$askaid);
    			exit();
    		}
		}
	}
	
	//无满意答案，结束问题
	function ac_toend()
	{
	    $askaid = request('askaid', '');
	    $askaid = is_numeric($askaid)? $askaid : 0;
	    $uid = $this->cfg_ml->M_ID;
	    //获取问题的基本信息
        $wheresql = "id='{$askaid}' AND status='0'";
        $field = "id, uid, dateline, solvetime, status, expiredtime,reward";
		$question = $this->question->get_one($wheresql,$field);
		if($question)
		{
			if($question['uid'] != $uid)
			{
				ShowMsg('非法操作，请返回','-1');
				exit;
			}elseif($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
				ShowMsg('问题已经过期','-1');
				exit;
			}
		}else{
			ShowMsg('回答的问题不存在','-1');
			exit;
		}
	    //保存提高悬赏
		$rs = $this->question->update_ask("solvetime='{$GLOBALS['cfg_ask_timestamp']}', status='1'","uid='{$uid}' AND id='{$askaid}'");
		if($rs)
		{
			ShowMsg("设置成功！","?ct=question&askaid=".$askaid);
			exit;
		}else{
		    ShowMsg("设置失败！","?ct=question&askaid=".$askaid);
			exit;
		}
	}
	
	//回复问题
	function ac_answer()
	{
		global $cfg_ask_guestask,$cfg_cmspath;
		$content = request('content', '');
		$data['askaid'] = request('askaid', '');
		$data['askaid'] = is_numeric($data['askaid'])? $data['askaid'] : 0;
		$anonymous = request('anonymous', '');
		//检查是否已经存在答复
		$rs = $this->answer->get_answer($this->cfg_ml->M_ID,$data['askaid']);
		
		if($this->cfg_ml->M_ID < 1)
		{
			$gourl = $cfg_cmspath.'/ask/?ct=question&askaid='.$data['askaid'];
			ShowMsg('您尚未登录需要登录后才能回复问题！',$cfg_cmspath.'/member/login.php?gourl='.urlencode($gourl));
			exit;
		}
		if($this->cfg_ml->M_Spacesta < 0)
		{
			ShowMsg('您还没有通过审核,暂时不能提问,请耐心等....','-1');
			exit;
		}
		if($rs)
		{
            ShowMsg('请勿重复回复同一问题!','-1');
			exit; 
		}
		if($content == '')
		{
			ShowMsg('回答不能为空!','-1');
			exit;
		}else if(strlen($content) > 10000)
		{
			ShowMsg('回答不能大于10000字节','-1');
			exit;
		}
		//获取问题的基本信息
        $wheresql = "id='{$data['askaid']}'";
        $field = "tid, tid2, uid, dateline, expiredtime, solvetime";
		$question = $this->question->get_one($wheresql,$field);
		if($question)
		{
			if($question['uid'] == $this->cfg_ml->M_ID)
			{
				ShowMsg('提问者自己不能回答自己的问题', '-1');
				exit;
			}else if($question['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
				ShowMsg('问题已经过期','-1');
				exit;
			}
			$data['tid'] = $question['tid'];
			$data['tid2'] = $question['tid2'];
			$data['userip'] = getip();
		}else{
			ShowMsg('回答的问题不存在','-1');
			exit;
		}
		$data['anonymous'] = 0;
		if($GLOBALS['cfg_ask_guestanswer'] == 'Y')
		{
			$data['anonymous'] = empty($anonymous)? 0 : 1;
		}
		
		$data['content'] = isset($content) ? preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($content, -1)) : '';
		$data['uid'] = $this->cfg_ml->M_ID;
		$data['username'] = $this->cfg_ml->M_LoginID;
		$data['timestamp'] = $GLOBALS['cfg_ask_timestamp'];
        //保存回复
		$rs = $this->answer->save_answer($GLOBALS['cfg_ask_ifkey'],$data);
        if(!$rs){
			ShowMsg("回答问题失败,请联系管理员!","-1");
			exit;
    	}
    	//获取回复的最大id
    	$maxid = $this->answer->get_maxid($data['timestamp']);
		$ids = array($data['askaid'],$maxid);
		clearmyaddon($ids, "回复");
		//回复数增加
		$rs = $this->question->update_ask("replies=replies+1","id='{$data['askaid']}'");
		$rs = $this->question->update_ask("lastanswer=".time(),"id='{$data['askaid']}'");
		$answerscore = intval($GLOBALS['cfg_ask_answerscore']);
		//只要回答问题就增加积分
		if($GLOBALS['cfg_ask_ifanscore'] == 'Y') $this->scores->add_scores($data['uid'],$GLOBALS['cfg_ask_answerscore']);
		ShowMsg('回答问题成功,如果未显示答案，请等待管理人员审核...',"?ct=question&askaid=".$data['askaid']);
		exit;
	}
	
	//修改答案
    function ac_reply()
	{
		$id = request('id', '');
		$id  = is_numeric($id)? $id : 0;
		//获取答案的基本信息
		$row = $this->answer->get_one("id='{$id}'","uid,askid,content");
		if(is_array($row))
		{
			if($row['uid'] != $this->cfg_ml->M_ID && $this->cfg_ml->isAdmin != 1)
			{
				ShowMsg('非法操作，请返回', '-1');
				exit;
			}
		}else{
			ShowMsg('答复不存在','-1');
			exit;
		}
		//设定变量值
        $GLOBALS['row'] = $row;
	    //载入模板
		$this->SetTemplate('edit_reply.htm');
        $this->Display();
	}
	
	//保存修改答案
    function ac_modifyanswer()
	{
		$content = request('content', '');
		$id = request('id', '');
		$id  = is_numeric($id)? $id : 0;
		$askaid = request('askaid', '');
		$askaid = is_numeric($askaid)? $askaid : 0;		
		$content = isset($content) ? preg_replace("#{$GLOBALS['cfg_replacestr']}#","***",HtmlReplace($content, -1)) : '';
		$uid = $this->cfg_ml->M_ID;
		$username = $this->cfg_ml->M_LoginID;
		$timestamp = $GLOBALS['cfg_ask_timestamp'];
		//获取答复具体信息
		$answer = $this->answer->get_info($id,1);
		if(is_array($answer))
		{
		    if($this->cfg_ml->isAdmin != 1)
		    {
    			if($answer['uid'] != $uid)
    			{
    				ShowMsg('非法操作，请返回', "-1");
    				exit;
    			}elseif($answer['status'] != 0){
    				ShowMsg('问题已经解决', "-1");
    				exit;
    			}elseif($answer['expiredtime'] < $timestamp){
    				ShowMsg('问题已经过期', "-1");
    				exit;
    			}
    		}
		}else{
			ShowMsg('回答的问题不存在',"-1");
			exit;
		}
		if(trim($content) == '')
		{
			ShowMsg('回答内容不能为空!',"-1");
			exit;
		}else if(strlen($content) > 10000){
			ShowMsg('回答不能大于10000字节',"-1");
			exit;
		}
		$rs = $this->answer->update_answer("content='{$content}'","id='{$id}'");
		if($rs)
		{
		    $ids = array($askaid,$id);
		    clearmyaddon($ids, "回复");
			ShowMsg('您的回答已经修改成功！将返回问题页面',"?ct=question&askaid=".$askaid);
			exit;
		}else{
			ShowMsg('修改答案失败，将返回问题页面',"-1");
			exit;
		} 
	}
	
	//删除答复
    function ac_reply_del()
	{
	    //判断是否为管理员
		if($this->cfg_ml->isAdmin != 1)
		{
		    ShowMsg("无权进行此项操作!",'-1');
		    exit;
		}
		$id = request('id', '');
		$id  = is_numeric($id)? $id : 0;
		$rs = $this->answer->del($id);
		if($rs)
		{
			ShowMsg("成功删除回复!","member_operations.php");
			exit;
		}else{
		    ShowMsg("删除回复失败！","member_operations.php");
			exit;
		}
	}
	
	//采纳答案
    function ac_adopt()
	{
		$id = request('id', '');
		$id  = is_numeric($id)? $id : 0;
		$answer = $this->answer->get_info_adopt($id);
		if(is_array($answer))
		{
			if($answer['uid'] != $this->cfg_ml->M_ID)
			{
				ShowMsg('非法操作，请返回', '-1');
				exit;
			}elseif($answer['status'] != 0){
				ShowMsg('问题已经解决', '-1');
				exit;
			}elseif($answer['expiredtime'] < $GLOBALS['cfg_ask_timestamp']){
				ShowMsg('问题已经过期', '-1');
				exit;
			}
		}else{
			ShowMsg('回答的问题不存在','-1');
			exit;
		}
          
		//提问奖励+系统奖励
		$reward = $answer['reward'] + $GLOBALS['cfg_ask_bestanswer'];
		//保存
		$set = "solvetime='{$GLOBALS['cfg_ask_timestamp']}', status='1', bestanswer='{$id}'";
		$rs = $this->question->update_ask($set,"id='{$answer['askid']}'");
		if($rs)
		{
		    $this->answer->update_answer('ifanswer = 1',"id='{$id}'");
		    //积分
		    $this->scores->add_scores($answer['answeruid'],$reward);
			ShowMsg("采纳成功!","?ct=question&askaid=".$answer['askid']);
			exit;
		}else{
		    ShowMsg("采纳失败!",'-1');
			exit;  
		}
	}
	
	//对最佳答案的评价
	function ac_rate()
	{
		$type = request('type', '');
		$rate = request('rate', '');
		$askaid = request('askaid', '');
        $askaid = is_numeric($askaid)? $askaid : 0;
        $type = strip_tags($type);
        $rate = strip_tags($rate);
		if($type == 'bad') $rate = 'badrate';
		else $rate = 'goodrate';
		$cookiename = 'rated'.$askaid;
		if(!isset($_COOKIE[$cookiename])) $_COOKIE[$cookiename] = 0;
		if((!$_COOKIE[$cookiename] == $askaid))
		{	
		    $this->answer->update_answer("{$rate}={$rate}+1","id='{$askaid}'");
			makecookie($cookiename,$askaid,3600);
		}
		$row = $this->answer->get_one("id='{$askaid}'","goodrate, badrate");
		$goodrate = $row['goodrate'];
		$badrate = $row['badrate'];
		if(($goodrate + $badrate) > 0)
		{
			$goodrateper = ceil($goodrate*100/($badrate+$goodrate));
			$badrateper = 100-$goodrateper;
		}else{
			$goodrateper = $badrateper = 0;
		}
		$total=$goodrate+$badrate;
		$aid=$askaid;
		AjaxHead();
		$poststr ="<dl>
					<dt><strong>您觉得最佳答案好不好？ </strong></dt>
					<dd> <a href=\"#\"  onclick=\"rate('mark',$askaid,'good')\"><img src=\"static/images/mark_g.gif\" width=\"14\" height=\"16\" />好</a> <span>$goodrateper% ($goodrate)</span> </dd>
                    <dd> <a href=\"#\"  onclick=\"rate('mark',$askaid,'bad')\"><img src=\"static/images/mark_b.gif\" width=\"14\" height=\"16\" />不好</a> <span>$badrateper% ($badrate)</span></dd>
                    <dt>(目前有 $total 个人评价)</dt>
				   </dl>";
	   echo $poststr;
	}
}
?>