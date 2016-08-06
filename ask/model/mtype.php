<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 分类(普通)
 *
 * @version        $Id: question.php 2010/12/3  shaxian $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
class mtype extends Model
{   
    /**
     * 发表文章时的栏目信息
     *
     * @param     string    $wheresql
     * @return    array
     */
    function get_asktype($wheresql = "")
    {
        $query = "SELECT id, name, reid FROM `#@__asktype` $wheresql ORDER BY disorder DESC";
        $this->dsql->SetQuery($query);
        $this->dsql->Execute();
        $asktypes = array();
        while($asktype = $this->dsql->GetArray())
        {
            $asktypes[] = $asktype;        
        }
        return $asktypes;
    }
    
   /**
     * 更新一个栏目的统计信息
     *
     * @param     int    $tid
     * @return    int
     */
    function update_asktype($tid = "")
    {
        if($tid)
        {
            $query = "UPDATE `#@__asktype` SET asknum=asknum+1 WHERE id='$tid'";
            $this->dsql->ExecuteNoneQuery($query);
            return TRUE;
        }else{
            return FALSE;   
        }

    }
     
    /**
     * 更新分类排序
     * @return    string
     */
    function update($disorders = array())
    {
        if(is_array($disorders))
        {
            foreach($disorders as $key => $disorder)
        	{
        		$query = "UPDATE `#@__asktype` SET disorder='$disorder' WHERE id='$key'";
        		$this->dsql->ExecuteNoneQuery($query); 
        	}
        	return TRUE;
        }else{
            return FALSE;  
        }
    }
    
    /**
     * 获取所有栏目
     * @return    string
     */
    function get_alltype()
    {
        $query = "SELECT * FROM `#@__asktype` ORDER BY disorder DESC, id ASC";
        $this->dsql->SetQuery($query);
        $this->dsql->Execute();
        $tids = $tid2s = $asktypes = array();
        while($asktype = $this->dsql->GetArray())
        {
        	if($asktype['reid'] == 0)
        	{
        		$tids[] = $asktype;
        	}else{
        		$tid2s[] = $asktype;
        	}
        
        }
        foreach($tids as $tid)
        {
        	$asktypes[] = $tid;
        	foreach($tid2s as $key => $tid2)
        	{
        		if($tid2['reid'] == $tid['id'])
        		{
        			$asktypes[] = $tid2;
        			unset($tid2s[$key]);
        		}
        	}
        }
        return $asktypes;
    }
    
    /**
     * 增加和编辑时的所有栏目获取
     * @param    int $type 1：编辑,2：增加
     * @param    int $id
     * @param    int $reid
     * @return    string
     */
    function get_optiontype($type = 1,$id = "",$reid = "")
    {
        $sectorscache = '<option value="0">无(作为一级分类)</option>';
        if($type == 2) $wheresql = "WHERE reid=0";
        else $wheresql = "WHERE reid=0 and id<>'$id'";
        $query = "SELECT * FROM `#@__asktype` {$wheresql} ORDER BY disorder ASC, id ASC";
		$this->dsql->SetQuery($query);
		$this->dsql->Execute();
		while($topsector = $this->dsql->GetArray())
		{
			$check = '';
			if($reid != 0 && $topsector['id'] == $reid) $check = 'selected';
			$sectorscache .= '<option value="'.$topsector['id'].'" '. $check.'>'.$topsector['name'].'</option>';
		}
        return $sectorscache;
    }
    
    /**
     * 获取一个栏目
     * @param    int $id
     * @return    string
     */
    function get_onetype($id = "")
    {
        $rs = $this->dsql->GetOne("SELECT * FROM `#@__asktype` WHERE id='{$id}'");
        return $rs;
    }
    
    /**
     * 保存一个编辑之后栏目
     * @param     array  $data
     * @return    string
     */
    function save_edit($data = array())
    {
        $query = "UPDATE `#@__asktype` SET name='{$data['name']}', reid='{$data['reid']}', disorder='{$data['disorder']}'
                WHERE id='{$data['id']}' ";
		if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
        else return FALSE;  
    }
    
   /**
     * 删除一个栏目
     * @param     array  $id
     * @return    string
     */
    function del_type($id = "")
    {
        if($id){
            $query = "DELETE FROM `#@__asktype` WHERE id='$id' OR reid='$id' "; 
            if($this->dsql->ExecuteNoneQuery($query))
            {
                $query = "SELECT id FROM `#@__ask` WHERE tid='$id' OR tid2='$id'";
                $this->dsql->SetQuery($query);
                $this->dsql->Execute();
                $askids = array();
                while($arr = $this->dsql->GetArray())
                {
                    $askids[] = $arr['id'];
                }
                foreach ($askids as $askid) {
                    $query = "DELETE FROM `#@__askcomment` WHERE askid='$id' ";
                    $this->dsql->ExecuteNoneQuery($query);
                }
                $query2 = "DELETE FROM `#@__askanswer` WHERE tid='$id' OR tid2='$id' "; 
                $query3 = "DELETE FROM `#@__askanswer` WHERE tid='$id' OR tid2='$id' "; 
                $this->dsql->ExecuteNoneQuery($query2);
                $this->dsql->ExecuteNoneQuery($query3);
                return TRUE;   
            }else{
                return FALSE;
            } 
        }else{
           return FALSE;
        } 
    }
    
    /**
     * 保存一个新增的栏目
     * @param     array  $data
     * @return    string
     */
    function save_add($data = array())
    {
        if(is_array($data))
        {
            $query = "INSERT INTO `#@__asktype`(name, reid) VALUES('{$data['name']}','{$data['reid']}');";
    		if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
            else return FALSE; 
        }else{
            return FALSE;
        } 
    }
    
    
}