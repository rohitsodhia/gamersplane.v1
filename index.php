<?
	require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');

	require_once(FILEROOT.'/header.php');
	if ($loggedIn) 
		require_once(FILEROOT.'/topNotifications.php');
?>
		<div class="clearfix">
			<div id="announcements">
<?
	addPackage('forum');
	$post = $mysql->query('SELECT t.firstPostID FROM threads t WHERE t.forumID = 3 ORDER BY threadID DESC LIMIT 1');
	$post = new Post($post->fetchColumn());
?>
				<h2 class="headerbar"><a href="/forums/thread/<?=$post->getThreadID()?>/"><?=$post->getTitle()?></a></h2>
				<h4><span class="convertTZ"><?=$post->getDatePosted('F j, Y g:i a')?></span> by <a href="/user/<?=$post->getAuthor('userID')?>/" class="username"><?=$post->getAuthor('username')?></a></h4>
				<hr>
<?=printReady(BBCode2Html(filterString($post->getMessage())))?>
<?	if ($loggedIn) { ?>
				<div class="readMore">To comment to this post or to read what others thought, please <a href="/forums/thread/<?=$post->getThreadID()?>/">click here</a>.</div>
<?	} ?>
			</div>
			<div class="sideWidget">
<?
	if ($loggedIn) {
		$usersGames = $mongo->games->find(array('players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'approved' => true))), array('gameID' => true, 'title' => true, 'system' => true, 'gm' => true, 'numPlayers' => true, 'players' => true))->sort(array('start' => -1))->limit(3);
?>
				<div class="loggedIn<?=$usersGames?'':' noGames'?>">
					<h2>Your Games</h2>
<?		if ($usersGames) { ?>
					<div class="games">
<?
			foreach ($usersGames as $gameInfo) {
				$gameInfo['playersInGame'] = -1;
				foreach ($gameInfo['players'] as $player) 
					if ($player['approved']) 
						$gameInfo['playersInGame']++;
				$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
?>
						<div class="gameInfo">
							<p class="title"><a href="/games/<?=$gameInfo['gameID']?>"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
							<p class="details"><u><?=$systems->getFullName($gameInfo['system'], true)?></u> run by <a href="/user/<?=$gameInfo['gm']['userID']?>/" class="username"><?=$gameInfo['gm']['username']?></a></p>
						</div>
<?			} ?>
					</div>
<?		} else { ?>
					<p>You're not in any games yet.</p>
					<div class="noGameLink"><a href="/games/list/">Join a game!</a></div>
<?		} ?>
				</div>
<?	} else { ?>
				<div class="loggedOut">
					We're a gaming community<br>
					Can't have community...<br>
					Without you!

					<div class="tr clearfix">
						<a href="/login" class="login loginLink">Login</a>
						<a href="/register" class="register">Register</a>
					</div>
				</div>
<?	} ?>
			</div>
		</div>

		<div class="clearfix">
			<div id="latestGames" class="homeWidget">
				<h3 class="headerbar">Latest Games</h3>
				<div class="widgetBody">
<?
	if ($loggedIn) 
		$latestGames = $mongo->games->find(
			array(
				'retired' => null,
				'players' => array(
					'$not' => array(
						'$elemMatch' => array(
							'user.userID' => $currentUser->userID,
							'approved' => true
						)
					)
				)
			), array(
				'gameID' => true,
				'title' => true,
				'system' => true,
				'gm' => true,
				'numPlayers' => true,
				'players' => true
			)
		)->sort(array('start' => -1))->limit(5);
	else 
		$latestGames = $mongo->games->find(
			array(
				'retired' => null
			), array(
				'gameID' => true,
				'title' => true,
				'system' => true,
				'gm' => true,
				'numPlayers' => true,
				'players' => true
			)
		)->sort(array('start' => -1))->limit(5);
	$first = true;
	foreach ($latestGames as $gameInfo) {
		foreach ($gameInfo['players'] as $player) 
			if ($player['approved']) 
				$gameInfo['playersInGame']++;
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		if (!$first) 
			echo "					<hr>\n";
		else 
			$first = false;
?>
					<div class="gameInfo">
						<p class="title"><a href="/games/<?=$gameInfo['gameID']?>/"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0?'Full':"{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
						<p class="details"><u><?=$systems->getFullName($gameInfo['system'])?></u> run by <a href="/user/<?=$gameInfo['gm']['userID']?>/" class="username"><?=$gameInfo['gm']['username']?></a></p>
					</div>
<?	} ?>
				</div>
			</div>
			
			<div id="latestPosts" class="homeWidget">
				<h3 class="headerbar">Latest Posts</h3>
				<div class="widgetBody">
<?
	$forumSearch = new ForumSearch('homepage');
	$forumSearch->findThreads();
	$forumSearch->displayLatestHP();
?>
					<div id="latestPostsLink"><a href="/forums/search/?search=latestPosts">Latest Posts</a></div>
				</div>
			</div>

			<div id="partners" class="homeWidget">
				<h3 class="headerbar">Partners</h3>
<?
	$rand = $mongo->execute('Math.random()');
	$partner = $mongo->links->findOne(array('level' => 'Partner', 'random' => array('$gte' => $rand['retval'])));
	if ($partner == null) 
		$partner = $mongo->links->findOne(array('level' => 'Partner', 'random' => array('$lte' => $rand['retval'])));
?>
				<div class="widgetBody">
					<a href="<?=$partner['url']?>" target="_blank"><img src="/images/links/<?=$partner['_id']?>.<?=$partner['image']?>"></a>
					<p><a href="<?=$partner['url']?>" target="_blank"><?=$partner['title']?></a></p>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>