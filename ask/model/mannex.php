<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 *  附件
 *
 * @version        $Id: question.php 2010/12/3  shaxian $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
class mannex extends Model
{   
    /**
     *  删除
     *
     * @param     array    $ids
     * @return    string
     */
    function del($ids)
    {
        global $cfg_basedir,$cfg_remote_site,$remoteuploads,$ftp;
    	if(count($ids) > 0)
        {
            foreach($ids as $id)
        	{
        		$id = preg_replace("#[^0-9]#","",$id);
        		if($id=="") continue;
        		$row = $this->dsql->GetOne("SELECT url FROM `#@__uploads_ask` WHERE aid='{$id}'");
                $truefile = $cfg_basedir.$row['url'];
                if($row['url'] != "")
                {
                    if($cfg_remote_site =='Y' && $remoteuploads == 1)
                    {
                        $ftp->delete_file($row['url']);
                    }else{
                        @unlink($truefile); 
                    }
                    $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__uploads_ask` WHERE aid='{$id}'");
                }else{
                    continue;
                }  
        	}
    		return TRUE;
    	}else{
            return FALSE;
    	}
    }
    
    /**
     *  清除
     *
     * @param     array    $ids
     * @return    string
     */
    function clear()
    {
        global $cfg_basedir,$cfg_remote_site,$remoteuploads,$ftp;
        $query = "SELECT aid,url FROM `#@__uploads_ask` WHERE arcid = 0";
        $this->dsql->SetQuery($query);
        $this->dsql->Execute();
        while($myrow = $this->dsql->GetArray())
        {
            $truefile = $cfg_basedir.$myrow['url'];
            if($myrow['url'] != "")
            {
                if($cfg_remote_site =='Y' && $remoteuploads == 1)
                {
                    $ftp->delete_file($myrow['url']);
                }else{
                    @unlink($truefile); 
                }
                $this->dsql->ExecuteNoneQuery("DELETE FROM `#@__uploads_ask` WHERE aid='{$myrow['aid']}'");
            }
        }
        return TRUE;
    }
}