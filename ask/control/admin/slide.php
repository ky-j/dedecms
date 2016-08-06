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
 
class slide extends Control
{
    function slide()
	{
		parent::__construct();
		$this->style = 'admin';
        //获取url
        $this->currurl = GetCurUrl();
        //载入模型
        $this->answer = $this->Model('askanswer');
        $this->question = $this->Model('mquestion');
	}
	
    function ac_index()
    {
        global $cfg_soft_lang;
        if(file_exists(DEDEASK."/data/cache/slide.inc")) {
            require_once(DEDEASK."/data/cache/slide.inc");
            if($cfg_soft_lang == 'utf-8')
            {
                $row = AutoCharset(unserialize(utf82gb($data)));
            }else{
                $row = unserialize($data);
            }
        }
        //设定变量值
		$GLOBALS['row'] = $row;
		//载入模板
		$this->SetTemplate('slide.htm');
        $this->Display();
    }
    
    function ac_save()
    {
        global $cfg_soft_lang;
        $data[0]['pic'] = request('pic1', '');
        $data[1]['pic'] = request('pic2', '');
        $data[2]['pic'] = request('pic3', '');
        $data[0]['url'] = request('url1', '');
        $data[1]['url'] = request('url2', '');
        $data[2]['url'] = request('url3', '');
        $data[0]['description'] = request('description1', '');
        $data[1]['description'] = request('description2', '');
        $data[2]['description'] = request('description3', '');
        $mpath = DEDEASK."/data/cache/slide.inc";
        
        if($cfg_soft_lang == 'utf-8')
        {
            $data = AutoCharset($data,'utf-8','gb2312');
            $data = serialize($data);
            $data = gb2utf8($data);
        }else{
            $data = serialize($data);
        }
        $configstr = "<"."?php\r\n\$data = '".$data."';";
        file_put_contents($mpath, $configstr);	
        ShowMsg('修改幻灯片成功','?ct=slide');
    	exit();
    }
}
?>