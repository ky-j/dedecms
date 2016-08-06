    <?php
        $add_channel_menu = array();
        //如果为游客访问，不启用左侧菜单
        if(!empty($cfg_ml->M_ID))
        {
            $channelInfos = array();
            $dsql->Execute('addmod',"SELECT id,nid,typename,useraddcon,usermancon,issend,issystem,usertype,isshow FROM `#@__channeltype` ");	
            while($menurow = $dsql->GetArray('addmod'))
            {
                $channelInfos[$menurow['nid']] = $menurow;
                //禁用的模型
                if($menurow['isshow']==0)
                {
                    continue;
                }
                //其它情况
                if($menurow['issend']!=1 || $menurow['issystem']==1 
                || ( !preg_match("#".$cfg_ml->M_MbType."#", $menurow['usertype']) && trim($menurow['usertype'])!='' ) )
                {
                    continue;
                }
                $menurow['ddcon'] = empty($menurow['useraddcon']) ? 'archives_add.php' : $menurow['useraddcon'];
                $menurow['list'] = empty($menurow['usermancon']) ? 'content_list.php' : $menurow['usermancon'];
                $add_channel_menu[] = $menurow;
            }
            unset($menurow);
		?>
    <div id="mcpsub">
      <div class="topGr"></div>
      <div id="menuBody">
      	<!-- 我的织梦菜单-->
      	<?php
      	if($menutype == 'mydede')
      	{
      	?>
        <h2 class="menuTitle" onclick="menuShow('menuFirst')" id="menuFirst_t"><b></b>会员互动</h2>
        <ul id="menuFirst">
        	<li class="icon mystow"><a href="<?php echo $cfg_ask_basehost;?>/member/mystow.php"><b></b>我的收藏夹</a></li>
        <?php
        $dsql->Execute('nn','Select indexname,indexurl From `#@__sys_module` where ismember=1 ');
        while($nnarr = $dsql->GetArray('nn'))
        {
        	@preg_match("/\/(.+?)\//is", $nnarr['indexurl'],$matches);
        	$nnarr['class'] = isset($matches[1]) ? $matches[1] : 'channel';
        	$nnarr['indexurl'] = str_replace("**","=",$nnarr['indexurl']);
        	if($cfg_ask_isdomain == 'Y'){
        	    $nnarr['indexurl'] = str_replace('..','',$nnarr['indexurl']);
        	    $nnarr['indexurl'] = $cfg_ask_basehost.$nnarr['indexurl'];
        	    if(preg_match('#myask#i',$nnarr['indexurl'])) $nnarr['indexurl'] = $cfg_ask_domain.'/?ct=myask';
        	} 
        ?>
        <li class="<?php echo $nnarr['class'];?>"><a href="<?php echo $nnarr['indexurl']; ?>"><b></b><?php echo $nnarr['indexname']; ?>模块</a></li>
        <?php
        }
        ?>
        </ul>
        <?php
      }
      ?>
      </div>
      <div class="buttomGr"></div>
    </div>
    <?php } ?>
    <!--左侧操作菜单项 -->