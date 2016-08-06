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
 
class asktype extends Control
{
    function asktype()
	{
		parent::__construct();
        //获取url
        $this->currurl = GetCurUrl();
        //获取类别
        require_once DEDEASK.'/data/asktype.inc.php';
        $this->asktypes = $asktypes;
        $this->style = 'admin';
        //载入模型
        $this->type = $this->Model('mtype');
	}
    
    function ac_index()
    {
        $asktypes = array_filter($this->asktypes, array(&$this, 'oneeven'));
        $asktype_sons = array_filter($this->asktypes, array(&$this, 'twoeven'));
        foreach ($asktypes as $key => $asktype) {
            $son = "";
            foreach ($asktype_sons as $asktype_son) {
                if($asktype_son['reid'] == $asktypes[$key]['id']){
                    $son .= '<tr>
                    <td align="center">'.$asktype_son['id'].'</td>
                    <td> |--'.$asktype_son['name'].'</td>
                    <td><input type="text" name="disorders['.$asktype_son['id'].']" value="'.$asktype_son['disorder'].'" /></td>
                    <td align="center"><a href="?ct=asktype&ac=edit&amp;id='.$asktype_son['id'].'&height=200&amp;width=450" class=\'thickbox\'>修改</a> 
                    <a href="#" onClick="javascript:del('.$asktype_son['id'].')">删除</a></td>
                    </tr>';
                }      
            } 
            $asktypes[$key]['son'] = $son;
        }
        //设定变量值
        $GLOBALS['asktypes'] = $asktypes;
		//载入模板
		$this->SetTemplate('asktype_list.htm');
        $this->Display();
    }
    
    //过滤数组单元,获取一级分类
    function oneeven($var)
    {
       return($var['reid'] == 0);
    }
    
    //过滤数组单元,获取二级分类
    function twoeven($var)
    {
       return($var['reid'] != 0);
    }
    
    //更新排序
    function ac_update()
    {
        $disorders = request('disorders', '');
    	$rs = $this->type->update($disorders);
    	if($rs)
    	{
    	    $this->updatecache();
    		ShowMsg("更新排序成功!","?ct=asktype");
    		exit();
    	}else{
    		ShowMsg('更新排序失败','-1');
    		exit();
    	}
    }
    
    //编辑
    function ac_edit()
    {
        $id = request('id', '');
    	$asktype = $this->type->get_onetype($id);
    	if(is_array($asktype))
    	{
    	    $sectorscache = $this->type->get_optiontype(1,$id,$asktype['reid']);
    	    //设定变量值
            $GLOBALS['id'] = $id;
            $GLOBALS['asktype'] = $asktype;
            $GLOBALS['sectorscache'] = $sectorscache;
    		//载入模板
    		$this->SetTemplate('asktype_edit.htm');
            $this->Display();
	    }else{
	        ShowMsg('编辑分类不存在','-1');
    		exit();
	    }
    }
    
    //保存编辑
    function ac_edit_save()
    {
        $data['id'] = request('id', '');
        $data['name'] = request('name', '');
        $data['reid'] = request('reid', '');
        $data['disorder'] = request('disorder', '');
        if($data['name'] == "")
        {
            ShowMsg('分类名称不能为空','?ct=asktype');
			exit();
        }
        $rs = $this->type->save_edit($data);
		if($rs)
		{
		    $this->updatecache();
			ShowMsg('编辑分类成功，将返回分类管理页面','?ct=asktype');
			exit();
		}else{
			ShowMsg('编辑分类成功，将返回分类管理页面','?ct=asktype');
			exit();
		}
    }
    
    //删除
    function ac_delete()
    {
        $id = request('id', '');
        $rs = $this->type->del_type($id);
		if($rs)
		{
		    $this->updatecache();
			ShowMsg('删除分类成功，将返回分类管理页面', '?ct=asktype');
			exit();
		}else{
			ShowMsg('删除分类失败，将返回分类管理页面 ','?ct=asktype');
			exit();
		}
    }
    
    //增加分类
    function ac_add()
    {
        $ml = request('ml', '');
        $id = request('id', '');
        $name = request('name', '');
        if($ml != 1) $sectorscache = $this->type->get_optiontype(2);
        //设定变量值
        $GLOBALS['ml'] = $ml;
        $GLOBALS['id'] = $id;
        $GLOBALS['name'] = $name;
        $GLOBALS['sectorscache'] = $sectorscache;
		//载入模板
		$this->SetTemplate('asktype_add.htm');
        $this->Display();
    }
    
    //增加分类
    function ac_add_save()
    {
        $data['name'] = request('name', '');
        $data['reid'] = request('reid', '');
        if(empty($data['name']))
        {
            ShowMsg('分类名称不能为空', '?ct=asktype');
			exit();  
        }
        $rs = $this->type->save_add($data);
        if($rs)
		{
		    $this->updatecache();
			ShowMsg('增加分类成功，将返回分类管理页面','?ct=asktype');
			exit();
		}else{
			ShowMsg('增加分类成功，将返回分类管理页面','?ct=asktype');
			exit();
		}

    }
    
    //更新栏目缓存
    function updatecache()
    {
        $asktypes = $this->type->get_alltype();
        $path = DEDEASK."/data/cache/asktype.inc";
        $row = serialize($asktypes);
        $configstr = "<"."?php\r\n\$asktypes = '".$row."';";
        file_put_contents($path, $configstr);	
    }
   
}
?>