<?php
/**
 * 
 * @version        2011/2/11  沙羡 $
 * @package        DedeCMS.Libraries
 * @copyright      Copyright (c) 2007 - 2011, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 *
 **/
 
$page_start_time = microtime(TRUE);
define('APPNAME','ask');
require_once(dirname(__file__).'/../include/common.inc.php');
require_once(DEDEINC.'/request.class.php');

define('DEDEASK',dirname(__FILE__));
define('LIB',dirname(__FILE__).'/libraries');

//对站点根网址最后/进行过滤
$cfg_basehost = preg_replace("#/$#",'',$cfg_basehost);

//载入配置文件
require_once(DEDEASK.'/data/common.inc.php');


$ct = Request('ct', 'index');
$ac = Request('ac', 'index');

// 统一应用程序入口
RunApp($ct, $ac);
