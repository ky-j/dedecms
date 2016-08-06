<?php   if(!defined('DEDEINC')) exit("Request Error!");
/**
 * 模型
 *
 * @version        $Id: menu.php 1 14:22 2010-10-28 tianya $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
 
class menu
{
    var $test = 'dede';
    function GetName($name='')
    {
        return empty($name)? $this->test : $name;
    }
}