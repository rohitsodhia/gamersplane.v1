<?
	class HomeManager {

		public function __construct() {
            //nothing to construct yet
		}


        public function addAnnouncement($forumId, $iconClass, $announcementClass, $addHeaderFooter,$randomPinned){
            global $post;
			$mysql = DB::conn('mysql');
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
				<h3 class="headerbar announcementsheaderbar"><i class="ra <?=$iconClass?>"></i> <a href="/forums/thread/<?=$post->getThreadID()?>/"><?=$post->getTitle()?></a> <i class="openClose openClose-open" data-announce="<?=$forumId?>" data-threadid='<?=$post->getThreadID()?>'></i></h3>
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

		public function addLookingForAGame($announcementClass){
            global $currentUser;
			$mysql = DB::conn('mysql');
			$gamer = $mysql->query("SELECT lfgMeta.metaValue AS lfgStatus, users.userID, users.username, users.joinDate, gamesMeta.metaValue AS games, avatarExt.metaValue AS avatarExt FROM usermeta AS lfgMeta INNER JOIN users ON lfgMeta.userID = users.userID INNER JOIN usermeta AS gamesMeta ON users.userID = gamesMeta.userID INNER JOIN usermeta AS avatarExt ON users.userID = avatarExt.userID WHERE (gamesMeta.metaKey = 'games') AND (lfgMeta.metaKey = 'lookingForAGame') AND (avatarExt.metaKey = 'avatarExt') AND (users.lastActivity >= UTC_TIMESTAMP() - INTERVAL 1 WEEK) ORDER BY RAND() LIMIT 1;")->fetch(PDO::FETCH_OBJ);

			?>
			<div class="announcements <?=$announcementClass?>">
				<h3 class="headerbar lfgheaderbar"><i class="ra ra-health"></i> <a href="/ucp">Looking for a game</a></h3>
				<div class="lfgSection">
<?


			if($gamer){
				$badges='';
				if (strtotime('-14 Days') < strtotime($gamer->joinDate)){
					$badges='<span class="badge badge-newMember">New member</span>';
				}
				?>
					<div class="lfgLhs">
						<a href="/user/<?=$gamer->userID?>/"><img src="/ucp/avatars/<?=$gamer->userID?>.<?=$gamer->avatarExt?>" onerror="this.onerror=null;this.src='/ucp/avatars/avatar.png';"/></a>
						<?=$badges?>
					</div>
					<div class="lfgRhs">
						<h3><a href="/user/<?=$gamer->userID?>/"><?=$gamer->username?></a></h3>
						<div class="lfgGame"><?=$gamer->games?></div>
						<hr/>
						<p class="lfgActions"><a href="/pms/send/?userID=<?=$gamer->userID?>/"><i class="ra ra-quill-ink"></i> Send <?=$gamer->username?> a message</a></p>
					</div>
				<?
			} else {
				?>
					<div class="noLfg">It looks like nobody is looking for a game at the moment.
						<hr/>
					<p class="alignRight"><a href="/ucp">Change your looking for a game status.</a></p></div>
				<?
			}
			?>
			</div>
			</div>
			<?
		}

		public function addLatestGames($showCount){
			global $currentUser;
			$mysql = DB::conn('mysql');
			$systems = Systems::getInstance();

			$getLatestGames = $mysql->query("SELECT games.gameID, games.title, games.system, games.system, gm.userID, gm.username, games.numPlayers, COUNT(approvedPlayers.gameID) playersInGame FROM games INNER JOIN users gm ON games.gmID = gm.userID INNER JOIN players approvedPlayers ON games.gameID = approvedPlayers.gameID AND approvedPlayers.approved = 1 LEFT JOIN players userGames ON games.gameID = userGames.gameID AND userGames.userID = {$currentUser->userID} WHERE userGames.userID != {$currentUser->userID} GROUP BY games.gameID ORDER BY `start` LIMIT {$showCount}");
			$first = true;
			foreach ($getLatestGames->fetchAll() as $gameInfo) {
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
			global $currentUser;
			$mysql = DB::conn('mysql');

			$getGameInvites = $mysql->query("SELECT games.gameID, games.title FROM games INNER JOIN gameInvites ON games.gameID = gameInvites.gameID WHERE games.retired IS NULL AND gameInvites.userID = {$currentUser->userID} GROUP BY games.gameID");
			$getPending = $mysql->query("SELECT games.gameID, games.title, games.status, COUNT(players.userID) pendingPlayers, COUNT(characters.userID) pendingCharacters FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID AND gmCheck.isGM = 1 AND gmCheck.userID = {$currentUser->userID} LEFT JOIN players ON games.gameID = players.gameID AND players.approved = 0 LEFT JOIN characters ON games.gameID = characters.gameID AND characters.approved = 0 WHERE games.retired IS NULL GROUP BY games.gameID ORDER BY games.start");
			// $getPendingPlayers = $mysql->query("SELECT games.gameID, games.title, games.status, COUNT(players.userID) pendingPlayers FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID AND gmCheck.isGM = 1 AND gmCheck.userID = {$currentUser->userID} INNER JOIN players ON games.gameID = players.gameID AND players.approved = 0 WHERE games.retired IS NULL GROUP BY games.gameID ORDER BY games.start");
			// $getPendingCharacters = $mysql->query("SELECT games.gameID, games.title, games.status, COUNT(characters.userID) pendingCharacters FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID AND gmCheck.isGM = 1 AND gmCheck.userID = {$currentUser->userID} INNER JOIN characters ON games.gameID = characters.gameID AND characters.approved = 0 WHERE games.retired IS NULL GROUP BY games.gameID ORDER BY games.start");
			$threadNotifications = $mysql->query("SELECT forumSubs.`type` notificationType, forumSubs.postID, forumSubs.ID threadID, posts.title threadTitle, forums.title forumTitle FROM forumSubs INNER JOIN threads ON forumSubs.ID = threads.threadID INNER JOIN posts ON threads.firstPostID = posts.postID INNER JOIN forums ON threads.forumID = forums.forumID WHERE forumSubs.userID = {$currentUser->userID} AND forumSubs.subscribed_to = 't'");

			$notifications = [];

			if ($getPending->rowCount()) {
				foreach ($getPending->fetchAll() as $game) {
					$gameID = $game['gameID'];
					$notification = '<div class="notify notifyWaiting col-1-2 mob-col-1">You have ';
					if ($game['pendingPlayers']) {
						$notification .= $game['pendingPlayers'] . ' player' . ($game['pendingPlayers'] > 1 ? 's' : '');
					}
					if ($game['pendingPlayers'] && $game['pendingCharacters']){
						$notification .= ' and ';
					}
					if ($game['pendingCharacters']) {
						$notification .= $game['pendingCharacters'] . ' character' . ($game['pendingCharacters'] > 1 ? 's' : '');
					}
					$notification .= ' pending in <a href="/games/'.$gameID.'">'.$game['title'].'</a></div>';
					$notifications[] = $notification;
				}
			}

			if ($getGameInvites->rowCount()) {
				foreach ($getGameInvites->fetchAll() as $game) {
					$notifications[] = '<div class="notify notifyJoin col-1-2 mob-col-1">You have been invited to join <a href="/games/'.$game['gameID'].'">'.$game['title'].'</a></div>';
				}
			}

			$hasMentions = false;
			if($threadNotifications->rowCount()) {
				foreach ($threadNotifications->fetchAll() as $threadNotification) {
					$notifications[] = '<div class="notify notifyThread notifyThread-'.$threadNotification['notificationType'].' col-1-2 mob-col-1"><a data-postid="'.$threadNotification['postID'].'" href="/forums/thread/'.$threadNotification['threadID'].'/?p='.$threadNotification['postID'].'#p'.$threadNotification['postID'].'">'.$threadNotification['forumTitle'].' &gt; '.$threadNotification['threadTitle'].'</a></div>';
				}
				$hasMentions = true;
			}

			//new users suggest making an introduction
			if (strtotime('-14 Days') < strtotime($currentUser->joinDate)){
				$introPosts = $mysql->query("SELECT count(p.postID) FROM posts p INNER JOIN threads t ON p.threadID = t.threadID WHERE t.forumID=14 AND authorID={$currentUser->userID} LIMIT 1")->fetchColumn();

				if (!$introPosts) {
					$notifications[] = '<div class="notify notifyIntroduction col-1-2 mob-col-1">Say hello in the <a href="/forums/14">Introductions</a> forum</div>';
				}
			}

			if (!empty($notifications)){
				echo '<div class="flexWrapper"><div id="notifications" class="col-1">';
				echo '<h2 class="headerbar notificationsheaderbar"><i class="ra ra-ringing-bell"></i> Notifications'.($hasMentions?'<span id="clearMentions">clear @</span>':'').'</h2>';

				echo '<div id="notificationMsgs" class="flexWrapper">';
				foreach ($notifications as $notification){
					echo $notification;
				}
				echo "</div></div></div>";
			}
		}

		public function addLatestPosts($forumManager,$forumId,$showCount){
			global $currentUser;
			$mysql = DB::conn('mysql');

			$results=$mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID={$forumId} ORDER BY lp.datePosted DESC LIMIT {$showCount}")->fetchAll(PDO::FETCH_OBJ);

			$first = true;
			$forumReadId=$forumManager->getForumProperty($forumId, 'markedRead');
			foreach ($results as $result) {
				if (!$first) echo "					<hr>\n";
				else $first = false;

				$newPosts = $result->lastPostID > $forumReadId && $result->lastPostID > $result->lastRead?true:false;

				ForumSearch::displayLatestPostResultHP($result,$newPosts, $forumManager->isFavGame($result->forumID));
			}
		}
    }
?>
