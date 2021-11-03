<?
	class HomeManager {

		public function __construct() {
            //nothing to construct yet
		}


        public function addAnnouncement($forumId, $iconClass, $announcementClass, $addHeaderFooter,$randomPinned){
            global $mysql,$post;
			$postItem = null;

			if($randomPinned){
				$postItem = $mysql->query("SELECT t.firstPostID FROM threads t WHERE t.forumID = {$forumId} AND sticky=1 ORDER BY RAND() LIMIT 1")->fetchColumn();
			}

			if(!$postItem) {
				$postItem = $mysql->query("SELECT t.firstPostID FROM threads t WHERE t.forumID = {$forumId}  ORDER BY threadID DESC LIMIT 1")->fetchColumn();
			}

            $post = new Post($postItem);
?>
			<div class="announcements <?=$announcementClass?>">
				<h2 class="headerbar announcementsheaderbar"><i class="ra <?=$iconClass?>"></i> <a href="/forums/thread/<?=$post->getThreadID()?>/"><?=$post->getTitle()?></a> <i class="openClose openClose-open" data-announce="<?=$forumId?>" data-threadid='<?=$post->getThreadID()?>'></i></h2>
				<div class="announcementPost">
					<?if($addHeaderFooter){?>
					<h4><span class="convertTZ"><?=$post->getDatePosted('F j, Y g:i a')?></span> by <a href="/user/<?=$post->getAuthor('userID')?>/" class="username"><?=$post->getAuthor('username')?></a></h4>
					<hr>
					<?}?>
					<div class="announcementMsg"><?=printReady(BBCode2Html(filterString($post->getMessage())))?></div>
					<?if($addHeaderFooter){?>
					<div class="readMore">To comment to this post or to read what others thought, please <a href="/forums/thread/<?=$post->getThreadID()?>/">click here</a>.</div>
					<?}?>
				</div>
			</div>
<?
        }

		public function addLatestGames($showCount){
			global $mongo,$currentUser,$systems;

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
					'limit' => $showCount
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
		<?php	}
		}

		public function addTopNotifications(){
			global $mongo,$currentUser;

			$invitedTo = $mongo->games->find(
				[
					'invites' => [
						'$elemMatch' => [
							'userID' => $currentUser->userID
						]
					],
					'retired' => null
				],
				['projection' => [
					'gameID' => true,
					'title' => true,
					'players' => true
				]]
			)->toArray();

			$pending = $mongo->games->find(
				[
					'players' => [
						'$elemMatch' => [
							'user.userID' => $currentUser->userID,
							'isGM' => true
						]
					],
					'retired' => null
				],
				['projection' => [
					'gameID' => true,
					'title' => true,
					'players' => true,
					'status' => true
				]]
			)->toArray();

			$threadNotifications = $mongo->users->findOne(
				[
					'userID' => $currentUser->userID
				],
				['projection' => [
					'threadNotifications' => true
				]]
			);

			$notifications = Array();

			if (count($pending)) {
				$pendingIDs = [];
				$pendingPlayers = [];
				$pendingChars = [];
				$openGames = 0;
				foreach ($pending as $game) {

					if($game['status']=='open'){
						$openGames++;
					}

					$pendingIDs[] = $game['gameID'];
					foreach ($game['players'] as $player) {
						if (!$player['approved']) {
							if (!isset($pendingPlayers[$game['gameID']])) {
								$pendingPlayers[$game['gameID']] = 0;
							}
							$pendingPlayers[$game['gameID']]++;
						}
						if (is_countable($player['characters']) && sizeof($player['characters'])) {
							foreach ($player['characters'] as $character) {
								if (!$character['approved']) {
									if (!isset($pendingChars[$game['gameID']])) {
										$pendingChars[$game['gameID']] = 0;
									}
									$pendingChars[$game['gameID']]++;
								}
							}
						}
					}
				}

				if($openGames > 0){
					$notification='<div class="notify notifyOpenGames col-1-2 mob-col-1">You have <a href="/games/my/">'.$openGames.' game'.($openGames!=1?'s':'').' open for applications</a></div>';
					$notifications[]=$notification;
				}
			}


			if (($pendingPlayers && sizeof($pendingPlayers) > 0) || ($pendingChars && sizeof($pendingChars) > 0)) {
				$notification='';
				if (sizeof($pendingPlayers) || sizeof($pendingChars)) {
					foreach ($pending as $game) {
						$gameID = $game['gameID'];
						if ($pendingPlayers[$gameID] || $pendingChars[$gameID]) {
							$notification=$notification.'<div class="notify notifyWaiting col-1-2 mob-col-1">You have ';
							if ($pendingPlayers[$gameID] > 0) {
								$notification=$notification.($pendingPlayers[$gameID].' player'.($pendingPlayers[$gameID] > 1 ? 's' : ''));
							}
							if ($pendingPlayers[$gameID] && $pendingChars[$gameID]){
								$notification=$notification.' and ';
							}
							if ($pendingChars[$gameID] > 0) {
								$notification=$notification.($pendingChars[$gameID].' character'.($pendingChars[$gameID] > 1 ? 's' : ''));
							}
							$notification=$notification.' pending in <a href="/games/'.$gameID.'">'.$game['title'].'</a></div>';

						}
					}
				}
				$notification=$notification.'';
				$notifications[]=$notification;

			}

			if (sizeof($invitedTo)) {
				$notification='';

				foreach ($invitedTo as $game) {
					$notification=$notification.'<div class="notify notifyJoin col-1-2 mob-col-1">You have been invited to join <a href="/games/'.$game['gameID'].'">'.$game['title'].'</a></div>';
				}
				$notification=$notification.'';
				$notifications[]=$notification;
			}

			if($threadNotifications && is_countable($threadNotifications["threadNotifications"])){
				foreach ($threadNotifications["threadNotifications"] as $threadNotification) {
					$notification='<div class="notify notifyThread notifyThread-'.$threadNotification['notificationType'].' col-1-2 mob-col-1"><a data-postid="'.$threadNotification['postID'].'" href="/forums/thread/'.$threadNotification['threadID'].'/?p='.$threadNotification['postID'].'#p'.$threadNotification['postID'].'">'.$threadNotification['forumTitle'].' &gt; '.$threadNotification['threadTitle'].'</a></div>';
					$notifications[]=$notification;
				}
			}

			//new users suggest making an introduction
			if (strtotime('-14 Days') < strtotime($currentUser->joinDate)){
				$mysql = DB::conn('mysql');
				$introPosts=$mysql->query("SELECT count(p.postID) FROM posts p INNER JOIN threads t ON p.threadID = t.threadID WHERE t.forumID=14 AND authorID={$currentUser->userID}")->fetchColumn();

				if($introPosts==0){
					$notification='<div class="notify notifyIntroduction col-1-2 mob-col-1">Say hello in the <a href="/forums/14">Introductions</a> forum</div>';
					$notifications[]=$notification;
				}
			}

			if(!empty($notifications)){
				echo '<div class="flexWrapper"><div id="notifications" class="col-1">';
				echo '<h2 class="headerbar notificationsheaderbar"><i class="ra ra-ringing-bell"></i> Notifications</h2>';

				echo '<div id="notificationMsgs" class="flexWrapper">';
				foreach ($notifications as $notification){
					echo $notification;
				}
				echo "</div></div></div>";
			}
		}

		public function addLatestPosts($forumManager,$forumId,$showCount){
			global $mysql, $currentUser;
			$results=$mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID={$forumId} ORDER BY lp.datePosted DESC LIMIT {$showCount}")->fetchAll(PDO::FETCH_OBJ);

			$first = true;
			$forumReadId=$forumManager->getForumProperty($forumId, 'markedRead');
			foreach ($results as $result) {
				if (!$first) echo "					<hr>\n";
				else $first = false;

				$newPosts = $result->lastPostID > $forumReadId && $result->lastPostID > $result->lastRead?true:false;

				ForumSearch::displayLatestPostResultHP($result,$newPosts);
			}
		}
    }
?>