<?php	require_once(FILEROOT . '/topNotifications.php'); ?>
		<div class="flexWrapper">
			<div id="announcements">
<?php
	addPackage('forum');
	$post = $mysql->query('SELECT t.firstPostID FROM threads t WHERE t.forumID = 3 ORDER BY threadID DESC LIMIT 1');
	$post = new Post($post->fetchColumn());
?>
				<h2 class="headerbar"><a href="/forums/thread/<?=$post->getThreadID()?>/"><?=$post->getTitle()?></a></h2>
				<h4><span class="convertTZ"><?=$post->getDatePosted('F j, Y g:i a')?></span> by <a href="/user/<?=$post->getAuthor('userID')?>/" class="username"><?=$post->getAuthor('username')?></a></h4>
				<hr>
<?=printReady(BBCode2Html(filterString($post->getMessage())))?>
<?php	if ($loggedIn) { ?>
				<div class="readMore">To comment to this post or to read what others thought, please <a href="/forums/thread/<?=$post->getThreadID()?>/">click here</a>.</div>
<?php	} ?>
			</div>
			<div class="sideWidget">
<?php
	if ($loggedIn) {
		$usersGames = $mongo->games->find(
			[
				'players' => [
					'$elemMatch' => [
						'user.userID' => $currentUser->userID,
						'approved' => true
					]
				],
				'retired' => null
			],
			[
				'projection ' => [
					'gameID' => true,
					'title' => true,
					'system' => true,
					'gm' => true,
					'numPlayers' => true,
					'players' => true,
					'customType' => true
				],
				'sort' => ['start' => -1],
				'limit' => 3
			]
		)->toArray();
?>
				<div class="loggedIn<?=count($usersGames) ? '' : ' noGames'?>">
					<h2>Your Games</h2>
					<div class="games">
<?php
		foreach ($usersGames as $gameInfo) {
			$gameInfo['playersInGame'] = -1;
			foreach ($gameInfo['players'] as $player) {
				if ($player['approved']) {
					$gameInfo['playersInGame']++;
				}
			}
			$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
?>
						<div class="gameInfo">
							<p class="title"><a href="/games/<?=$gameInfo['gameID']?>"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0 ? 'Full' : "{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
							<p class="details"><u><?=$gameInfo['customType']?$gameInfo['customType']:$systems->getFullName($gameInfo['system'], true)?></u> run by <a href="/user/<?=$gameInfo['gm']['userID']?>/" class="username"><?=$gameInfo['gm']['username']?></a></p>
						</div>
<?php		} ?>
					</div>
<?php	} else { ?>
					<p>You're not in any games yet.</p>
					<div class="noGameLink"><a href="/games/list/">Join a game!</a></div>
<?php	} ?>
				</div>
			</div>
		</div>

		<div class="flexWrapper">
			<div id="latestGames" class="homeWidget">
				<h3 class="headerbar">Latest Games</h3>
				<div class="widgetBody">
<?php
	if ($loggedIn) {
		$latestGames = $mongo->games->find(
			[
				'retired' => null,
				'players' => [
					'$not' => [
						'$elemMatch' => [
							'user.userID' => $currentUser->userID,
							'approved' => true
						]
					]
				]
			],
			[
				'projection' => [
					'gameID' => true,
					'title' => true,
					'system' => true,
					'gm' => true,
					'numPlayers' => true,
					'players' => true,
					'customType' => true
				],
				'sort' => ['start' => -1],
				'limit' => 4
			]
		);
	} else {
		$latestGames = $mongo->games->find(
			['retired' => null],
			[
				'projection' => [
					'gameID' => true,
					'title' => true,
					'system' => true,
					'gm' => true,
					'numPlayers' => true,
					'players' => true,
					'customType' => true
				],
				'sort' => ['start' => -1],
				'limit' => 4
			]
		);
	}
	$first = true;
	foreach ($latestGames as $gameInfo) {
		$gameInfo['playersInGame'] = -1;
		foreach ($gameInfo['players'] as $player) {
			if ($player['approved']) {
				$gameInfo['playersInGame']++;
			}
		}
		$slotsLeft = $gameInfo['numPlayers'] - $gameInfo['playersInGame'];
		if (!$first) {
			echo "					<hr>\n";
		} else {
			$first = false;
		}
?>
					<div class="gameInfo">
						<p class="title"><a href="/games/<?=$gameInfo['gameID']?>/"><?=$gameInfo['title']?></a> (<?=$slotsLeft == 0 ? 'Full' : "{$gameInfo['playersInGame']}/{$gameInfo['numPlayers']}"?>)</p>
						<p class="details"><u><?=$gameInfo['customType']?$gameInfo['customType']:$systems->getFullName($gameInfo['system'])?></u> run by <a href="/user/<?=$gameInfo['gm']['userID']?>/" class="username"><?=$gameInfo['gm']['username']?></a></p>
					</div>
<?php	} ?>
				</div>
			</div>

			<div id="latestPosts" class="homeWidget">
				<h3 class="headerbar">Latest Community Posts</h3>
				<div class="widgetBody">
<?php
	$forumSearch = new ForumSearch('homepage');
	$forumSearch->findThreads(1,3);
	$forumSearch->displayLatestHP();
?>
					<div class="latestPostsLink"><a href="/forums/search/?search=latestPosts">All Latest Posts</a></div>
				</div>
			</div>

			<div id="latestGamePosts" class="homeWidget">
				<h3 class="headerbar">Latest Game Posts</h3>
				<div class="widgetBody">
<?php
	$forumSearch = new ForumSearch('latestGamePosts');
	$forumSearch->findThreads(1, 3);
	$forumSearch->displayLatestHP();
?>
					<div class="latestPostsLink"><a href="/forums/search/?search=latestGamePosts">Latest Game Posts</a></div>
				</div>
			</div>
		</div>
