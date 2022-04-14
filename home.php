<?php	addPackage('forum');
$addJSFiles = Array('postPolls.js');
require_once(FILEROOT . '/includes/HomeManager.class.php');
$homeManager = new HomeManager();
$forumManager = new ForumManager(0);
$forumSearchGames = new ForumSearch('latestGamePosts',array(),$forumManager);
$forumSearchGames->findThreads(1, 5);
?>

<div id="hompageRows">

	<?= $homeManager->addTopNotifications();?>

	<div class="flexWrapper mob-order-3">
		<?= $homeManager->addAnnouncement(3,'ra-gamers-plane','col-1-2 mob-col-1',false,true);?>
		<?= $homeManager->addLookingForAGame('col-1-2 mob-col-1');?>
	</div>
	<div class="flexWrapper mob-order-1">

		<div id="latestPosts" class="homeWidget col-1-3 mob-col-1 mob-order-3">
			<h3 class="headerbar"><a href="/games/list/"><i class="ra ra-all-for-one"></i> Latest Games</a></h3>
			<div class="widgetBody">
				<?php $homeManager->addLatestGames(3); ?>
			</div>

		<?php
			$forumSearchPublic = new ForumSearch('latestPublicPosts' , array() , $forumManager);
			$forumSearchPublic->findThreads(1,2);
			$forumSearchPublic->displayLatestHPWidget('<a href="/forums/search/?search=latestPublicPosts"><i class="ra ra-horn-call"></i> Latest in Public Games</a>','<a href="/forums/search/?search=latestPublicPosts">Latest in Public Games</a>','orange');
			?>
		</div>

		<div id="latestPosts" class="homeWidget col-1-3 mob-col-1 mob-order-2">
		<?php
			$forumSearchCommunity = new ForumSearch('homepage' , array() , $forumManager);
			$forumSearchCommunity->findThreads(1,5);
			$forumSearchCommunity->displayLatestHPWidget('<a href="/forums/search/?search=latestPosts"><i class="ra ra-speech-bubble"></i> Community Posts</a>','<a href="/forums/search/?search=latestPosts">All Latest Posts</a>','orange');
		?>
		</div>

		<div id="latestPosts" class="homeWidget col-1-3 mob-col-1 mob-order-1">
		<?php
			if(!$forumSearchGames->displayLatestHPWidget('<a href="/forums/search/?search=latestGamePosts"><i class="ra ra-d6"></i> Latest Game Posts</a>','<a href="/forums/search/?search=latestGamePosts">Latest Game Posts</a>','red','gamesheaderbar')){
				?>
					<h3 class="headerbar gamesheaderbar"><i class="ra ra-d6"></i> Find a game</h3>
					<div class="noGames">
						<p>You're not in any active games.</p>
						<div class="noGameLink"><a href="/forums/10/">Join a game!</a></div>
					</div>
					<h3 class="headerbar"><a href="/forums/10/"><i class="ra ra-beer"></i> Games Tavern</a></h3>
				<?php
				$homeManager->addLatestPosts($forumManager,10,3);
			}


		?>
		</div>
	</div>
	<?php 	$forumManagerGames = new ForumManager(2);
			if($forumManagerGames->hasForums(ForumManager::FAVOURITE)) {
	?>
			<div class="flexWrapper mob-order-2">
				<div id="yourGames" class="col-1">
					<h3 class="headerbar gamesheaderbar"><a href="/forums/2"><i class="ra ra-bookmark"></i> Bookmarked Games</a></h3>
				<?php
					$forumManagerGames->displayForum(ForumManager::FAVOURITE);
				?>
				</div>
			</div>
	<?php 	}

			if($forumManagerGames->hasForums(ForumManager::NON_FAVOURITE)) { ?>
			<div class="flexWrapper mob-order-2">
				<div id="yourGames" class="col-1">
					<h3 class="headerbar"><a href="/forums/2"><i class="ra ra-d6"></i> Your Games</a></h3>
				<?php
					$forumManagerGames->displayForum(ForumManager::NON_FAVOURITE);
				?>
				</div>
			</div>
	<?php } ?>

</div>