<?
	class HomeManager {

		public function __construct() {
            //nothing to construct yet
		}


        public function addAnnouncement($forumId,$iconClass,$addHeaderFooter,$randomPinned){
            global $mysql;
			if($randomPinned){
				$postQuery = $mysql->query("SELECT t.firstPostID FROM threads t WHERE t.forumID = {$forumId} AND sticky=1 ORDER BY RAND() LIMIT 1");
			}else{
				$postQuery = $mysql->query("SELECT t.firstPostID FROM threads t WHERE t.forumID = {$forumId}  ORDER BY threadID DESC LIMIT 1");
			}

            $post = new Post($postQuery->fetchColumn());
?>
			<div class="announcements col-1-2 mob-col-1">
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
					'players' => true
				]]
			)->toArray();
			if (count($pending)) {
				$pendingIDs = [];
				$pendingPlayers = [];
				$pendingChars = [];
				foreach ($pending as $game) {
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
			}

			$notifications=Array();
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
    }
?>