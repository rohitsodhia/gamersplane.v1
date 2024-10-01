<?
	class ThreadManager {
		protected $threadID;
		protected $thread;
		protected $forumManager;
		protected $page = 1;

		public function __construct($threadID = null, $forumID = null) {
			if (intval($threadID))	{
				global $mysql, $currentUser;

				$this->threadID = intval($threadID);
				$thread = $mysql->query("SELECT t.threadID, t.forumID, t.locked, t.sticky, t.allowRolls, t.allowDraws, t.publicPosting, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, t.firstPostID, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead, t.discordWebhook FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.threadID = {$this->threadID} LIMIT 1");
				$this->thread = $thread->fetch();
	//			throw new Exception('No thread');
				if (!$this->thread)
					return false;
				$this->thread = new Thread($this->thread);

				$this->forumManager = new ForumManager($this->thread->forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS);
			} elseif (intval($forumID)) {
				$this->thread = new Thread();
				$this->thread->forumID = $forumID;
				$this->thread->setAllowRolls(true);
				$this->forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS);
			}

			$pageSize = intval($_GET['pageSize']);
			if($pageSize && $pageSize>20){
				$this->thread->pageSize=$pageSize;
			}
		}

		public function __get($key) {
			if (property_exists($this, $key))
				return $this->$key;
		}

		public function __set($key, $value) {
			if (property_exists($this, $key))
				$this->$key = $value;
		}

		public function getThreadID() {
			return $this->threadID;
		}

		public function getThreadProperty($property) {
			if (preg_match('/(\w+)\[(\w+)\]/', $property, $matches))
				return $this->thread->{$matches[1]}[$matches[2]];
			elseif (preg_match('/(\w+)->(\w+)/', $property, $matches))
				return $this->thread->$matches[1]->$matches[2];
			else
				return $this->thread->$property;
		}

		public function getForumProperty($key) {
			return $this->forumManager->getForumProperty($this->thread->forumID, $key);
		}

		public function getFirstPostID() {
			return $this->thread->getFirstPostID();
		}

		public function getLastPost($key = null) {
			return $this->thread->getLastPost($key);
		}

		public function isGameForum() {
			return $this->forumManager->forums[$this->thread->forumID]->isGameForum();
		}

		public function getPermissions($permission = null) {
			if (($permission=="write") && ($this->getThreadProperty('states[publicPosting]'))){
				return 1;
			}
			if(!$this->forumManager){  //failed permissions in c'tor
				return 0;
			}
			return $this->forumManager->getForumProperty($this->thread->forumID, 'permissions'.($permission != null?"[{$permission}]":''));
		}

		public function getThreadLastRead() {
			if ($this->forumManager->maxRead($this->thread->forumID) > $this->getThreadProperty('lastRead'))
				return $this->forumManager->maxRead($this->thread->forumID);
			else
				return $this->getThreadProperty('lastRead');
		}

		public function setPage() {
			global $mysql;

			if (isset($_GET['view']) && $_GET['view'] == 'newPost') {
				$numPrevPosts = $mysql->query("SELECT COUNT(postID) numPosts FROM posts WHERE threadID = {$this->threadID} AND postID <= ".$this->getThreadLastRead());
				$numPrevPosts = $numPrevPosts->fetchColumn() + 1;
				$page = $numPrevPosts?ceil($numPrevPosts / $this->thread->pageSize):1;
			} elseif (isset($_GET['view']) && $_GET['view'] == 'lastPost') {
				$numPrevPosts = $this->getThreadProperty('postCount');
				$page = $numPrevPosts?ceil($numPrevPosts / $this->thread->pageSize):1;
			} elseif (isset($_GET['p']) && intval($_GET['p'])) {
				$post = intval($_GET['p']);
				$numPrevPosts = $mysql->query("SELECT COUNT(postID) FROM posts WHERE threadID = {$this->threadID} AND postID <= {$post}");
				$numPrevPosts = $numPrevPosts->fetchColumn();
				$page = $numPrevPosts?ceil($numPrevPosts / $this->thread->pageSize):1;
			} elseif (isset($_GET['b'])) {
				if(intval($_GET['b'])){
					$post = intval($_GET['b']);
					$numPrevPosts = $mysql->query("SELECT COUNT(postID) FROM posts WHERE threadID = {$this->threadID} AND postID <= {$post}");
					$numPrevPosts = $numPrevPosts->fetchColumn();
					$start=$numPrevPosts-11;
				}
				else{
					$numPrevPosts = $mysql->query("SELECT COUNT(postID) FROM posts WHERE threadID = {$this->threadID}");
					$numPrevPosts = $numPrevPosts->fetchColumn();
					$start=$numPrevPosts-10;
				}
				$pageSize=10;
				if($start<0){
					$start=0;
					$pageSize=$numPrevPosts-1;
				}
				$this->thread->pageSize=$pageSize;
				$this->thread->getPostsFromStart($start);
				$page = 1;
			} else{
				$page = intval($_GET['page']);
			}
			$this->page = intval($page) > 0?intval($page):1;
		}

		public function getPage(){
			return $this->page;
		}

		public function getPosts() {
			global $mysql;

			return $this->thread->getPosts($this->page);
		}

		public function getKeyPostSnippet() {
			global $mysql;

			$posts = $this->thread->getPosts($this->page);
			$checkFor = '';
			if (isset($_GET['view']) && $_GET['view'] == 'newPost')
				$checkFor = 'newPost';
			elseif (isset($_GET['p']) && intval($_GET['p']))
				$checkFor = intval($_GET['p']);
			elseif ($this->page != 1)
				return ForumSearch::getMetaAttribute($mysql->query("SELECT message FROM posts WHERE postID = {$this->thread->firstPostID}")->fetchColumn());
			else
				return ForumSearch::getMetaAttribute($posts[$this->thread->firstPostID]->message);

			foreach ($posts as $post) {
				if ($checkFor == 'newPost' && ($post->getPostID() > $this->getThreadLastRead() || $this->thread->getLastPost('postID') == $post->getPostID()))
					return ForumSearch::getMetaAttribute($post->message);
				elseif ($post->getPostID() == $checkFor)
					return ForumSearch::getMetaAttribute($post->message);
			}

			return "";
		}

		public function updatePostCount() {
			global $mysql;

			$count = $mysql->query("SELECT COUNT(postID) FROM posts WHERE threadID = {$this->threadID}")->fetchColumn();
			$mysql->query("UPDATE threads SET postCount = {$count} WHERE threadID = {$this->threadID} LIMIT 1");
		}

		public function getPoll() {
			return $this->thread->getPoll();
		}

		public function getPollProperty($key) {
			return $this->thread->getPollProperty($key);
		}

		public function deletePoll() {
			return $this->thread->deletePoll();
		}

		public function getVotesCast() {
			return $this->thread->getVotesCast();
		}

		public function getVoteTotal() {
			return $this->thread->getVoteTotal();
		}

		public function getVoteMax() {
			return $this->thread->getVoteMax();
		}

		public function saveThread($post, $majorEdit = false) {
			global $mysql;

			$newPost = !($post->getPostID());

			if (!$newPost) {
				$this->removeOldMentions($post);
			}

			if ($this->threadID == null) {
				$insertThread = $mysql->prepare("INSERT INTO threads SET forumID = {$this->thread->forumID}, sticky = ".$this->thread->getStates('sticky', true).", locked = ".$this->thread->getStates('locked', true).", allowRolls = ".$this->thread->getAllowRolls(true).", allowDraws = ".$this->thread->getAllowDraws(true).", postCount = 1, publicPosting = ".($this->thread->getStates('publicPosting',true)?1:0).", discordWebhook = :discordWebhook, firstPostID = -1, lastPostID = -1");
				$insertThread->bindValue(':discordWebhook', $this->getThreadProperty('discordWebhook'));
				$insertThread->execute();
				$this->threadID = $mysql->lastInsertId();

				$post->setThreadID($this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET firstPostID = {$postID}, lastPostID = {$postID} WHERE threadID = {$this->threadID}");
				$mysql->query("UPDATE forums SET threadCount = threadCount + 1 WHERE forumID = {$this->thread->forumID}");

				$this->updateLastRead($postID);
			} else {
				$updateThread = $mysql->prepare("UPDATE threads SET forumID = {$this->thread->forumID}, sticky = ".($this->thread->getStates('sticky')?1:0).", locked = ".($this->thread->getStates('locked')?1:0).", allowRolls = ".($this->thread->getAllowRolls()?1:0).", allowDraws = ".($this->thread->getAllowDraws()?1:0).", publicPosting = ".($this->thread->getStates('publicPosting')?1:0).", discordWebhook = :discordWebhook WHERE threadID = ".$this->threadID);
				$updateThread->bindValue(':discordWebhook', $this->getThreadProperty('discordWebhook'));
				$updateThread->execute();

				$postID = $post->savePost();

				if($newPost){
					$mysql->query("UPDATE threads SET lastPostID = {$postID} WHERE threadID = {$this->threadID}");
				}

				$this->updatePostCount();
				$this->updateLastRead($postID);
			}

			$this->thread->savePoll($this->threadID);

			if($newPost){
				$this->addThreadNotification(ThreadNotificationTypeEnum::NEW_POST,$post);
			}

			if($majorEdit){
				$this->majorChange($post);
			}

			$this->addMentions($post);

			return $postID;
		}

		public function updateLastRead($postID) {
			global $loggedIn, $mysql, $currentUser;
			if ($loggedIn && $postID > $this->getThreadProperty('lastRead'))
				$mysql->query("INSERT INTO forums_readData_threads SET threadID = {$this->threadID}, userID = {$currentUser->userID}, lastRead = {$postID} ON DUPLICATE KEY UPDATE lastRead = {$postID}");
		}

		public function displayPagination() {
			ForumView::displayPagination($this->getThreadProperty('postCount'), $this->page,array(), $this->thread->pageSize,true);
		}

		public function isLastPage() {
			$numPages = ceil($this->getThreadProperty('postCount') / $this->thread->pageSize);
			return $this->page>=$numPages;
		}

		public function deletePost($post) {
			global $mysql;

			$post->delete();
			if ($post->getPostID() == $this->getLastPost('postID')) {
				$newLPID = $mysql->query("SELECT postID FROM posts WHERE threadID = {$this->threadID} ORDER BY datePosted DESC LIMIT 1")->fetchColumn();
				$mysql->query("UPDATE threads SET lastPostID = {$newLPID} WHERE threadID = {$this->threadID}");
			}
			$this->updatePostCount();
		}

		public function deleteThread() {
			global $mysql;

			$mysql->query("DELETE FROM threads, posts, rolls, deckDraws USING threads LEFT JOIN posts ON threads.threadID = posts.threadID LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE threads.threadID = {$this->threadID}");
			$mysql->query("UPDATE forums SET threadCount = threadCount - 1 WHERE forumID = {$this->thread->forumID}");
		}

		public function displayBreadcrumbs($pathOptions,$post,$quoteID){
			?>
			<div class="breadcrumbs">
				<?
				$this->forumManager->displayForumBreadcrumbs();

				if ($pathOptions[0] == 'editPost') {
					echo ">\t\t\t\t\t<a href=\"/forums/thread/".$this->getThreadID()."/?p=".$post->postID."#".$post->postID."\">" . printReady($this->getThreadProperty('title')) . "</a>";
				} elseif ($pathOptions[0] == 'post' ) {
					if($quoteID){
						echo ">\t\t\t\t\t<a href=\"/forums/thread/".$this->getThreadID()."/?p=".$quoteID."#p".$quoteID."\">" . printReady($this->getThreadProperty('title')) . "</a>";
					}
					else{
						echo ">\t\t\t\t\t<a href=\"/forums/thread/".$this->getThreadID()."/?view=newPost#newPost\">" . printReady($this->getThreadProperty('title')) . "</a>";
					}
				}
				?>
			</div>
			<?
		}

		public function enrichThread() {
			if ($this->thread && $this->thread->forumID == 10 && $this->page == 1) {  //games tavern
				$mysql = DB::conn('mysql');
				$authorId = $mysql->query("SELECT authorId FROM posts WHERE postID = {$this->thread->firstPostID}")->fetchColumn();

				if ($authorId) {
					$gameInfo = $mysql->query("SELECT games.gameID, games.status, games.public, games.system, games.customSystem, games.postFrequency, users.username gmUsername, games.description FROM games INNER JOIN users ON games.gmID = users.userID WHERE games.recruitmentThreadId = {$this->threadID} AND games.gmID = {$authorId} LIMIT 1");
					if($gameInfo->rowCount()){
						$gameInfo = $gameInfo->fetch();

						echo "<div class='tavernTags'>";
						if($gameInfo['status']){
							echo "<span class='badge badge-gameOpen'>Open</span>";
						}
						else{
							echo "<span class='badge badge-gameClosed'>Closed</span>";
						}

						require_once(FILEROOT . '/includes/Systems.class.php');
						$systems = Systems::getInstance();

						echo "<span class='badge badge-system badge-system-".$gameInfo['system']."'>".($gameInfo["customSystem"]?$gameInfo["customSystem"]:$systems->getFullName($gameInfo['system']))."</span>";

						if($gameInfo['public']){
							echo "<span class='badge badge-gamePrivate'>Private</span>";
						}
						else{
							echo "<span class='badge badge-gamePublic'>Public</span>";
						}

						$gameInfo['postFrequency'] = json_decode($gameInfo['postFrequency'], true);

						echo "<span class='badge badge-gameFrequency'>".$gameInfo["postFrequency"]["timesPer"]." / ".($gameInfo["postFrequency"]["perPeriod"]=="d"?"day":"week")."</span>";

						echo "<span class='badge badge-gameGm'>".$gameInfo["gmUsername"]."</span>";

						echo "</div>";
						echo "<div class='tavernGame'>";
						echo printReady(BBCode2Html($gameInfo['description']));
						echo "</div>";

						echo "<hr/>";
						echo "<div class='alignRight'><a href='/games/".$gameInfo['gameID']."'/ class='fancyButton'>Game details</a></div>";
						echo "<hr/>";
					}
				}
			}
		}

		public function addThreadIcon(){
			$this->forumManager->addForumIcon($this->thread->forumID);
		}

		public function addModerationButtons(){
			if ($this->getPermissions('moderate')) {
				$sticky = $this->thread->getStates('sticky') ? 'unsticky' : 'sticky';
				$lock = $this->thread->getStates('locked') ? 'unlock' : 'lock';
			?>
				<span class="moderationButtons">
					<form class="threadOptions" method="post" action="/forums/process/modThread/">
						<input type="hidden" name="threadID" value="<?=$this->threadID?>">
						<button type="submit" name="sticky" title="<?=ucwords($sticky)?> Thread" alt="<?=ucwords($sticky)?> Thread" class="<?=$sticky?>"></button>
						<button type="submit" name="lock" title="<?=ucwords($lock)?> Thread" alt="<?=ucwords($lock)?> Thread" class="<?=$lock?>"></button>
						<button type="submit" name="move" title="Move Thread" alt="Move Thread" class="move"></button>
					</form>
			</span>
			<?php
			}
		}

		public function majorChange($post){
			$postId = $post->getPostID();
			global $mysql, $currentUser;
			$lastPosts=$mysql->query("SELECT postID FROM posts WHERE threadID = {$this->threadID} AND postID<{$postId} ORDER BY datePosted DESC LIMIT 1");

			if($lastPosts->rowCount()==1){
				$lastPostRead=$lastPosts->fetch(PDO::FETCH_OBJ);
				$mysql->query("UPDATE forums_readData_threads SET lastRead = {$lastPostRead->postID} WHERE lastRead>{$postId} AND threadID = {$this->threadID} AND userID <> {$currentUser->userID}");
			}
			else{
				$mysql->query("DELETE FROM forums_readData_threads WHERE threadID = {$this->threadID} AND userID <> {$currentUser->userID}");
			}

			$this->addThreadNotification(ThreadNotificationTypeEnum::MAJOR_EDIT,$post);
		}

		private static function getUserIdsFromMentions($message)
		{
			global $mysql;

			//remove quotes
			$message = preg_replace("/\[quote(?:=\"([\w\.]+?)\")?\](((?R)|.)*?)\[\/quote\]/ms", "", $message);

			$ret = Array();
			preg_match_all('/\@([0-9a-zA-Z\-\.\_]+)/', $message, $matches, PREG_SET_ORDER);

			if (sizeof($matches)) {
				foreach ($matches as $match) {
					$checkUsername = rtrim($match[1],".-_ \n\r\t\v\0");
					$mentionUserId = $mysql->query("SELECT userID FROM users WHERE username = '{$checkUsername}'")->fetchColumn();
					if($mentionUserId && !in_array($mentionUserId,$ret)){
						$ret[] = (int) $mentionUserId;
					}
				}
			}

			return $ret;
		}

		private function removeOldMentions($post){
			$mysql = DB::conn('mysql');

			$oldMessage = $mysql->query("SELECT message FROM posts WHERE postID = {$post->postID}")->fetchColumn();

			$userIds = ThreadManager::getUserIdsFromMentions($oldMessage);
			if (sizeof($userIds)) {
				$mysql->query("DELETE FROM forumNotifications WHERE postID = {$post->getPostID()} AND userID IN (" . implode(', ', $userIds) . ")");
			}
		}

		private function addMentions($post){
			$mysql = DB::conn('mysql');

			$userIds = ThreadManager::getUserIdsFromMentions($post->message);

			if (count($userIds)>0) {

				//strip "Re: " if present
				$postTitle=$post->getTitle();
				if(substr($postTitle,0,4)=='Re: '){
					$postTitle=substr($postTitle,4);
				}


				$upsertNotification = $mysql->prepare("INSERT INTO forumNotifications SET userID = :userID, threadID = :threadID, notificationType = :notificationType, postID = :postID");
				foreach ($userIds as $mentionUserId) {
					$upsertNotification->execute(['userID' => $mentionUserId, 'threadID' => $this->threadID, 'notificationType' => ThreadNotificationTypeEnum::MENTION, 'postID' => $post->getPostID()]);
				}
			}
		}

		private function addThreadNotification($notificationType, $post){
			global $currentUser;
			$mysql = DB::conn('mysql');

			$threadID = (int) $this->threadID;

			//strip "Re: " if present
			$postTitle = $post->getTitle();
			if (substr($postTitle, 0, 4) == 'Re: ') {
				$postTitle = substr($postTitle, 4);
			}

			if ($notificationType == ThreadNotificationTypeEnum::NEW_POST || $notificationType == ThreadNotificationTypeEnum::MAJOR_EDIT) {
				$discordWebhook = $mysql->query("SELECT discordWebhook FROM threads WHERE threadID = {$threadID} LIMIT 1")->fetchColumn();

				if ($discordWebhook) {
					$userAvatar = "https://" . getenv('APP_URL') . User::getAvatar($currentUser->userID);
					$avatar = $userAvatar;
					$postAsName = $currentUser->username;
					$postAsId = $post->getPostAs();

					if ($postAsId && $postAsId != 'p') {
						$postAsName = $mysql->query("SELECT name FROM characters WHERE characterID = {$postAsId} LIMIT 1")->fetchColumn();
						if ($postAsName) {
							if (file_exists(FILEROOT . "/characters/avatars/{$postAsId}.jpg")) {
								$avatar = "https://" . getenv('APP_URL') . "/characters/avatars/{$postAsId}.jpg";
							}
						}
					} else {
						$npc = Post::extractPostingNpc($post->getMessage());
						if ($npc) {
							$avatar = $npc["avatar"];
							$postAsName = $npc["name"];
						}
					}

					$in = [
						"/\[quote(?:=\"([\w\.]+?)\")?\](((?R)|.)*?)\[\/quote\]/ms",
						"/\[snippets=\"?(.*?)\"?\](.*?)\[\/snippets\]/ms",
						"/\[abilities=\"?(.*?)\"?\](.*?)\[\/abilities\]/ms",
						"/\[poll=\"?(.*?)?\"([^\]]*)\](.*?)\[\/poll\]/ms"
					];
					$out = ['', '', '', 'Poll (\1) \3'];

					$discordMessage = preg_replace($in, $out, $post->getMessage());
					$discordMessage = ForumSearch::getTextSnippet(Post::extractFullText($discordMessage), 200);

					$data = [
						'username' => $postAsName,
						'avatar_url' => $avatar,
						'embeds' => [
							[
								'url' => 'https://' . getenv('APP_URL') . "/forums/thread/{$this->threadID}/?p={$post->postID}#p{$post->postID}",
								'title' => $postTitle,
								'color' => 13395456, //#cc6600
								'description' => $discordMessage,
								'footer' => [
									'text' => $currentUser->username . ($notificationType == ThreadNotificationTypeEnum::MAJOR_EDIT ? " ~ edited post" : ""),
									'icon_url' => $userAvatar
								]
							]
						]
					];

					$options = [
						'http' => [
							'header'  => "Content-type: application/json\r\n",
							'method'  => 'POST',
							'content' => json_encode($data),
							'ignore_errors' => true
						]
					];

					set_error_handler(
						function ($severity, $message, $file, $line) {
						}
					);
					try {
						$context  = stream_context_create($options);
						file_get_contents($discordWebhook, false, $context);
					}
						catch (Exception $e) {
					}
					restore_error_handler();
				}

				return;
			}

			$gameID = $this->forumManager->forums[$this->thread->forumID]->getGameID();
			if ($gameID) {
				$postID = (int)$post->getPostID();

				if ($notificationType == ThreadNotificationTypeEnum::MAJOR_EDIT) {
					$pull = ['$pull' => [
						'threadNotifications' => ['postID'=>$postID]
					]];
				} elseif ($notificationType == ThreadNotificationTypeEnum::NEW_POST) {
					$pull = ['$pull' => [
						'threadNotifications' => ['threadID' => $threadID, 'notificationType' => $notificationType]
					]];
				}

				$approvedPlayers = $mysql->query("SELECT userID FROM players WHERE gameID = {$gameID} AND approved = 1")->fetchAll();
				foreach ($approvedPlayers as $player) {
					if ($player['userID'] != $currentUser->userID){
						$upsertNotification = $mysql->prepare("INSERT INTO forumNotifications SET userID = :userID, threadID = :threadID, notificationType = :notificationType, postID = :postID");
						$upsertNotification->execute(['userID' => $player['userID'], 'threadID' => $threadID, 'notificationType' => $notificationType, 'postID' => $postID]);
					}
				}
			}
		}
	}

	abstract class ThreadNotificationTypeEnum{
		const NEW_POST = 1;
		const MAJOR_EDIT = 2;
		const MENTION = 3;
	}

?>
