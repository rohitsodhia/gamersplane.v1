<?php	addPackage('forum');
require_once(FILEROOT . '/includes/HomeManager.class.php');
$homeManager = new HomeManager();
$forumSearchGames = new ForumSearch('latestGamePosts');
$forumSearchGames->findThreads(1, 5);
?>


	<?= $homeManager->addTopNotifications();?>

	<div class="flexWrapper">
		<?= $homeManager->addAnnouncement(3,'ra-gamers-plane',true,false);
		if($forumSearchGames->getResultsCount()>0){
			//tips for users in games (using guides forum at the moment, but this should change)
			$homeManager->addAnnouncement(21,'ra-vial',false,true);
		}
		else{
			//tips for users without games
			$homeManager->addAnnouncement(21,'ra-wooden-sign',false,true);
		}?>

	</div>

		<div class="flexWrapper">
			<div id="latestGames" class="homeWidget col-1-3 mob-col-1 mob-order-3">
				<h3 class="headerbar"><i class="ra ra-all-for-one"></i> Latest Games</h3>
				<div class="widgetBody">
<?php

	$latestGames = $mongo->games->find(
		[
			'retired' => null,
			'status'=>'open',
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
			'limit' => ($forumSearchGames->getResultsCount()>0?2:5)
		]
	);
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
				<?if($forumSearchGames->getResultsCount()>0){
					?><br/><?
					$forumSearchPublic = new ForumSearch('latestPublicPosts');
					$forumSearchPublic->findThreads(1, 2);
					$forumSearchPublic->displayLatestHPWidget('<i class="ra ra-horn-call"></i> Latest Public Posts','<a href="/forums/search/?search=latestPublicPosts">Latest Public Posts</a>','orange');
				}?>
			</div>

<div id="latestPosts" class="homeWidget col-1-3 mob-col-1 mob-order-2">
<?php
	$forumSearchCommunity = new ForumSearch('homepage');
	$forumSearchCommunity->findThreads(1,5);
	$forumSearchCommunity->displayLatestHPWidget('<i class="ra ra-speech-bubble"></i> Community Posts','<a href="/forums/search/?search=latestPosts">All Latest Posts</a>','orange');
?>
</div>

<div id="latestPosts" class="homeWidget col-1-3 mob-col-1 mob-order-1">
<?php
	if(!$forumSearchGames->displayLatestHPWidget('<i class="ra ra-d6"></i> Latest Game Posts','<a href="/forums/search/?search=latestGamePosts">Latest Game Posts</a>','red','gamesheaderbar')){
		?>
			<h3 class="headerbar gamesheaderbar"><i class="ra ra-d6"></i> Find a game</h3>
			<div class="noGames">
				<p>You're not in any games yet.</p>
				<div class="noGameLink"><a href="/games/list/">Join a game!</a></div>
				<p>...or read some of the public games below.</p>
			</div>
		<?php
		$forumSearchPublic = new ForumSearch('latestPublicPosts');
		$forumSearchPublic->findThreads(1, 3);
		$forumSearchPublic->displayLatestHPWidget('<i class="ra ra-horn-call"></i> Latest Public Posts','<a href="/forums/search/?search=latestPublicPosts">Latest Public Posts</a>','orange');
	}


?>
</div>
</div>

<div class="flexWrapper">
	<div id="yourGames" class="col-1">
		<h3 class="headerbar gamesheaderbar"><i class="ra ra-d6"></i> Your Games</h3>
	<?php
	$forumManager = new ForumManager(2);
		$forumManager->displayForum();
	?>
	</div>
</div>