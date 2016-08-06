<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 普通问题
 *
 * @version        $Id: question.php 2010/12/3  shaxian $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
class askcomment extends Model
{
    
    /**
     *  获取问题的评论
     *
     * @param     int    $digestid
     * @return    string
     */
    function get_comments($digestid)
    {
        if($digestid)
        {
            $query = "SELECT * FROM `#@__askcomment` WHERE askid ='{$digestid}' ORDER BY dateline DESC";
            $this->dsql->SetQuery($query);
            $this->dsql->Execute();
            $rows = array();
            $commentnum = 0;
            while($row = $this->dsql->GetArray())
            {
                $commentnum++;
                $rows['comment'][] = $row;
            }
            $rows['commentnum'] = $commentnum;
            return $rows;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  保存一个评论
     *
     * @param     array    $data
     * @return    string
     */
    function save($data = array())
    {
        if(is_array($data))
        {
		    $query = "INSERT INTO `#@__askcomment`(askid, uid, username, userip, dateline, content,ifcheck)
		              VALUES ('{$data['askaid']}','{{$data['uid']}}', '{$data['uname']}', '{$data['userip']}', '{$data['timestamp']}', '{$data['content']}','{$data['ifcheck']}')";
    		if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
    		else return FALSE;
    	}else{
            return FALSE;
    	}
    } 
    
    /**
     *  审核
     *
     * @param     array    $ids
     * @return    string
     */
    function check($ids = array())
    {
        if(count($ids) > 0)
        {
            foreach($ids as $id)
        	{
        		$id = preg_replace("#[^0-9]#","",$id);
        		if($id=="") continue;
        		$query = "UPDATE `#@__askcomment` SET ifcheck='1' WHERE id='{$id}' AND ifcheck='0'";
    		    $this->dsql->ExecuteNoneQuery($query);
        	}
    		return TRUE;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  删除
     *
     * @param     int    $id
     * @return    string
     */
    function del($id)
    {
        if($id)
        {
    		$query = "DELETE FROM #@__askcomment WHERE id='{$id}'";
		    if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
		    else return FALSE;
    	}else{
            return FALSE;
    	}
    }
}