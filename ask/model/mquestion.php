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
 
class mquestion extends Model
{   
    
    /**
     *  获取一个问题的基本信息
     *
     * @param     string    $wheresql
     * @param     string    $field
     * @return    array
     */
    function get_one($wheresql = "",$field = '*')
    {
        if($field)
        {
            $row = $this->dsql->GetOne("SELECT $field FROM `#@__ask` WHERE $wheresql");
    		return $row;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  获取相应数量的所有问题
     *
     * @param     string    $wheresql
     * @param     string    $field
     * @param     int       $row
     * @return    array
     */
    function get_all($wheresql = "",$orderby = "",$row = '10',$field = 'id, tid, tidname, tid2, tid2name,title,reward,replies')
    {
        if($field)
        {
            $arrays = array();
            $query = "SELECT $field FROM `#@__ask` WHERE $wheresql $orderby limit 0,$row";
            $this->dsql->SetQuery($query);
            $this->dsql->Execute();
    		while($arr = $this->dsql->GetArray())
    		{
    			$arrays[] = $arr; 
    		}
    		return $arrays;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  获取相应数量的推荐所有问题
     *
     * @param     int       $row
     * @return    array
     */
    function get_digests($row = '10')
    {
        $arrays = array();
        $query = "SELECT a.id, a.title,m.userid FROM `#@__ask` a 
                  LEFT JOIN `#@__member` m ON m.mid=a.uid 
                  WHERE a.digest = 1 ORDER BY dateline DESC LIMIT 0,$row";
        $this->dsql->SetQuery($query);
        $this->dsql->Execute();
		while($arr = $this->dsql->GetArray())
		{
			$arrays[] = $arr; 
		}
		return $arrays;
    }
    
    
   /**
     *  获取一个问题的基本信息包括发布者信息
     *
     * @param     int    $askaid
     * @return    string
     */
    function get_info($askaid,$rs = "")
    {
        if($askaid)
        {
            if($rs == 1) $wheresql = "AND ask.status = 0";
            else $wheresql = "";
            $query = "SELECT ask.*, mem.userid as username, mem.scores,mem.mtype,mem.face FROM `#@__ask` ask 
                      LEFT JOIN `#@__member` mem ON mem.mid=ask.uid 
                      WHERE ask.id='{$askaid}' {$wheresql}";
    		$this->dsql->ExecuteNoneQuery($query);
    		return $this->dsql->GetOne($query);
    	}else{
            return FALSE;
    	}
    }
    
   /**
     *  获取问题的数量
     *
     * @return    string
     */
    function get_total()
    {
        $data['solving'] = 0; //未解决的问题数
		$data['solved'] = 0;  //已解决的问题数
		$query = "SELECT status,COUNT(status) AS dd FROM `#@__ask` GROUP BY status ";
		$this->dsql->Execute('me',$query);
		while($tmparr = $this->dsql->GetArray())
		{
			if($tmparr['status']==0)
			{
				$data['solving'] = $tmparr['dd'];
			}else{
				$data['solved'] += $tmparr['dd'];
			}
		}
		return $data;
    }
    
   /**
     *  针对所有对#@__ask表的update行为
     *
     * @param     string    $set
     * @param     string    $wheresql
     * @return    int
     */
    function update_ask($set = "",$wheresql = "")
    {
        if($wheresql && $set)
        {
            $query = "UPDATE #@__ask SET $set WHERE $wheresql";
            if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
            else return FALSE;
    	}else{
            return FALSE;
    	}
    }
   
     
   /**
     * 检查在有效期内是否存在同样的问题
     *
     * @param     int       $uid 
     * @param     string    $title
     * @return    string
     */
    function get_title($uid = "",$title = "")
    {
        if($uid && $title)
        {
            $row = $this->dsql->GetOne("SELECT id FROM `#@__ask` WHERE uid = '{$uid}' AND title = '{$title}' AND dateline < expiredtime");
    		if(is_array($row)) return TRUE;
    		else return FALSE;
    	}else{
            return TRUE;
    	}
    }
		
    /**
     *  保存新增加的问题
     *
     * @param     string      $type
     * @param     array    $data
     * @return    string
     */
    function save_ask($type = "",$data = array())
    {
        if(is_array($data))
        {
            if($type == 'Y') $status = "-1";
            else $status = "0";
		    $query = "INSERT INTO `#@__ask`(tid, tidname, tid2, tid2name, uid, anonymous, status, title, reward, dateline, expiredtime, ip ,content) VALUES ('{$data['tid']}', '{$data['tidname']}', '{$data['tid2']}', '{$data['tid2name']}', '{$data['uid']}', '{$data['anonymous']}', '{$status}', '{$data['title']}', '{$data['reward']}', '{$data['timestamp']}', '{$data['expiredtime']}', '{$data['userip']}', '{$data['content']}')";
    		if($this->dsql->ExecuteNoneQuery($query)) return TRUE;
    		else return FALSE;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  获取最大的id
     *
     * @param     time    $timestamp
     * @return    string
     */
    function get_maxid($timestamp)
    {
        if($timestamp)
        {
            $row = $this->dsql->GetOne("SELECT max(id) AS id FROM `#@__ask` WHERE dateline = '{$timestamp}'");
    		return $row['id'];
    	}else{
            return FALSE;
    	}
    }
    
    
   /**
     *  获取我的提问
     *
     * @param     int    $uid
     * @param     int    $start
     * @param     int    $end
     * @return    string
     */
    function get_myask($uid = "",$start= "",$end = "")
    {
        $query = "SELECT id, tid, tidname, tid2, tid2name, uid, title, digest, reward, dateline, expiredtime, 
	              solvetime, status, replies FROM `#@__ask` WHERE uid='{$uid}'
	              ORDER BY dateline DESC LIMIT {$start},{$end}";
	    $this->dsql->SetQuery($query);
		$this->dsql->Execute();
		$myasks = array();
		while($row = $this->dsql->GetArray())
		{
			$myasks[] = $row;
		}
		return $myasks;
    }
    
    /**
     *  批量删除一个问题
     *
     * @param     int    $askaid
     * @return    string
     */
    function del($askaid)
    {
        if($askaid){
            $query = "DELETE FROM `#@__ask` WHERE id='{$askaid}'";
    		if($this->dsql->ExecuteNoneQuery($query))
    		{
    		    $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__askanswer` WHERE askid='{$askaid}'");
    		    $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__askcomment` WHERE askid='{$askaid}'");
    		    global $cfg_basedir,$cfg_remote_site;
    		    //启用远程站点则创建FTP类
                if($cfg_remote_site == 'Y')
                {
                    require_once(DEDEINC.'/ftp.class.php');
                    if(file_exists(DEDEDATA."/cache/inc_remote_config.php"))
                    {
                        require_once DEDEDATA."/cache/inc_remote_config.php";
                    }
                    if(empty($remoteuploads)) $remoteuploads = 0;
                    if(empty($remoteupUrl)) $remoteupUrl = '';
                    //初始化FTP配置
                    $ftpconfig = array(
                        'hostname' => $rmhost, 
                        'port' => $rmport,
                        'username' => $rmname,
                        'password' => $rmpwd
                
                    );
                    $ftp = new FTP; 
                    $ftp->connect($ftpconfig);
                }
                $query = "SELECT url FROM `#@__uploads_ask` WHERE arcid='{$askaid}' AND type = 0";
                $this->dsql->SetQuery($query);
                $this->dsql->Execute();
                while($row = $this->dsql->GetArray())
                {
                    if($cfg_remote_site == 'Y' && $remoteuploads == 1)
                    {
                        $ftp->delete_file($row['url']);
                    }else{
                        @unlink($cfg_basedir.$row['url']); 
                    }
                }
                $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__uploads_ask` WHERE arcid ='{$askaid}' AND type = 0");
                return  TRUE;
    		}else{
    		    return  FALSE;
    		}
    	}else{
    	    return  FALSE;
    	}
    }
    
    /**
     * 更新统计信息
     * @return    string
     */
    function update()
    {
        $query = "SELECT id, reid FROM `#@__asktype`";
	    $this->dsql->SetQuery($query);
        $this->dsql->Execute();
        while($row = $this->dsql->GetArray())
        {
            if($row['reid'] == 0)
    		{
    			$this->dsql->SetQuery("SELECT COUNT(*) AS dd FROM `#@__ask` WHERE tid='{$row['id']}' ");
    			$this->dsql->Execute('top');
    			$asknum = $this->dsql->GetArray('top');
    			$this->dsql->ExecuteNoneQuery("UPDATE `#@__asktype` SET asknum='{$asknum['dd']}' WHERE id='{$row['id']}' ");
    		}else{
    			$this->dsql->SetQuery("SELECT COUNT(*) as dd FROM `#@__ask` WHERE tid2='{$row['id']}' ");
    			$this->dsql->Execute('sub');
    			$asknum = $this->dsql->GetArray('sub');
    			$this->dsql->ExecuteNoneQuery("UPDATE `#@__asktype` SET asknum='{$asknum['dd']}' WHERE id='{$row['id']}' ");
    		}
        }
    }
}