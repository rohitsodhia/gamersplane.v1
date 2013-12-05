<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">GP's Gamers</h1>
		
		<ul class="clearfix hbMargined">
<?
	$userCount = $mysql->query('SELECT COUNT(userID) FROM users');
	$userCount = $userCount->fetchColumn();
	$usersPerPage = 25;
	
	$page = intval($_GET['page']) > 0?intval($_GET['page']):1;
	$usersOnPage = $mysql->query('SELECT userID, username, IF(lastActivity >= UTC_TIMESTAMP() - INTERVAL 15 MINUTE, 1, 0) online, joinDate, avatarExt FROM users ORDER BY online DESC, username LIMIT '.(($page - 1) * $usersPerPage).', '.$usersPerPage);
	$count = 0;
	foreach ($usersOnPage as $userInfo) {
		$count++;
?>
			<li<?=$count % 5 == 0?' class="last"':''?>>
				<div class="onlineIndicator <?=$userInfo['online']?'online':'offline'?>"></div>
				<a href="<?=SITEROOT.'/user/'.$userInfo['userID']?>" class="avatar">
					<img src="<?=SITEROOT.'/ucp/avatars/'.(file_exists(FILEROOT."/ucp/avatars/{$userInfo['userID']}.png")?$userInfo['userID']:'avatar')?>.png">
				</a>
				<p><a href="<?=SITEROOT.'/user/'.$userInfo['userID']?>"><?=$userInfo['username']?></a></p>
			</li>
<? } ?>
		</ul>
		
<?
	if ($userCount > $usersPerPage) {
		$spread = 2;
		echo "\t\t\t<div id=\"paginateDiv\" class=\"paginateDiv\">";
		$numPages = ceil($userCount / $usersPerPage);
		$firstPage = $page - $spread;
		if ($firstPage < 1) $firstPage = 1;
		$lastPage = $page + $spread;
		if ($lastPage > $numPages) $lastPage = $numPages;
		echo "\t\t\t<div class=\"currentPage\">$page of $numPages</div>\n";
		if (($page - $spread) > 1) echo "\t\t\t\t<a href=\"?page=1\">&lt;&lt; First</a>\n";
		if ($page > 1) echo "\t\t\t\t<a href=\"?page=".($page - 1)."\">&lt;</a>\n";
		for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?page=$count\"".(($count == $page)?' class="currentPage"':'').">$count</a>\n";
		if ($page < $numPages) echo "\t\t\t\t<a href=\"?page=".($page + 1)."\">&gt;</a>\n";
		if (($page + $spread) < $numPages) echo "\t\t\t\t<a href=\"?page=$numPages\">Last &gt;&gt;</a>\n";
		echo "\t\t\t</div>\n";
	}
	
	require_once(FILEROOT.'/footer.php');
?>