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
				$thread = $mysql->query("SELECT t.threadID, t.forumID, t.locked, t.sticky, t.allowRolls, t.allowDraws, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, t.firstPostID, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.threadID = {$this->threadID} LIMIT 1");
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
			} else
				$page = intval($_GET['page']);
			$this->page = intval($page) > 0?intval($page):1;
		}

		public function getPage(){
			return $this->page;
		}

		public function getPosts() {
			global $mysql;

			return $this->thread->getPosts($this->page);
		}

		public function getKeyPost() {
			global $mysql;

			$posts = $this->thread->getPosts($this->page);
			$checkFor = '';
			if (isset($_GET['view']) && $_GET['view'] == 'newPost')
				$checkFor = 'newPost';
			elseif (isset($_GET['p']) && intval($_GET['p']))
				$checkFor = intval($_GET['p']);
			elseif ($this->page != 1)
				return $mysql->query("SELECT message FROM posts WHERE postID = {$this->thread->firstPostID}")->fetch();
			else
				return $posts[$this->thread->firstPostID];

			foreach ($posts as $post) {
				if ($checkFor == 'newPost' && ($post->getPostID() > $this->getThreadLastRead() || $this->thread->getLastPost('postID') == $post->getPostID()))
					return $post;
				elseif ($post->getPostID == $checkFor)
					return $post;
			}
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

			$newPost=!($post->getPostID());

			if(!$newPost){
				$this->removeOldMentions($post);
			}

			if ($this->threadID == null) {
				$mysql->query("INSERT INTO threads SET forumID = {$this->thread->forumID}, sticky = ".$this->thread->getStates('sticky', true).", locked = ".$this->thread->getStates('locked', true).", allowRolls = ".$this->thread->getAllowRolls(true).", allowDraws = ".$this->thread->getAllowDraws(true).", postCount = 1");
				$this->threadID = $mysql->lastInsertId();

				$post->setThreadID($this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET firstPostID = {$postID}, lastPostID = {$postID} WHERE threadID = {$this->threadID}");
				$mysql->query("UPDATE forums SET threadCount = threadCount + 1 WHERE forumID = {$this->thread->forumID}");

				$this->updateLastRead($postID);

			} else {
				$mysql->query("UPDATE threads SET forumID = {$this->thread->forumID}, sticky = ".($this->thread->getStates('sticky')?1:0).", locked = ".($this->thread->getStates('locked')?1:0).", allowRolls = ".($this->thread->getAllowRolls()?1:0).", allowDraws = ".($this->thread->getAllowDraws()?1:0)." WHERE threadID = ".$this->threadID);

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
			<div id="breadcrumbs">
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

		public function enrichThread(){
			if($this->thread && $this->thread->forumID==10 && $this->page==1){  //games tavern

				global $mysql;
				$authorId=$mysql->query("SELECT AuthorId FROM posts WHERE postID = {$this->thread->firstPostID}")->fetchColumn();

				if($authorId){
					$mongo = DB::conn('mongo');
					$gameInfo = $mongo->games->findOne(['recruitmentThreadId' => ($this->threadID),
														'gm.userID' => (int)($authorId)]);


					if($gameInfo){

						echo "<div class='tavernTags'>";
						if($gameInfo['status']=='open'){
							echo "<span class='badge badge-gameOpen'>Open</span>";
						}
						else{
							echo "<span class='badge badge-gameClosed'>Closed</span>";
						}

						require_once(FILEROOT . '/includes/Systems.class.php');
						$systems = Systems::getInstance();

						echo "<span class='badge badge-system badge-system-".$gameInfo['system']."'>".($gameInfo["customType"]?$gameInfo["customType"]:$systems->getFullName($gameInfo['system']))."</span>";

						if($gameInfo['public']){
							echo "<span class='badge badge-gamePrivate'>Private</span>";
						}
						else{
							echo "<span class='badge badge-gamePublic'>Public</span>";
						}

						echo "<span class='badge badge-gameFrequency'>".$gameInfo["postFrequency"]["timesPer"]." / ".($gameInfo["postFrequency"]["perPeriod"]=="d"?"day":"week")."</span>";

						echo "<span class='badge badge-gameGm'>".$gameInfo["gm"]["username"]."</span>";

						echo "</div>";
						echo "<div class='tavernGame'>";
						echo printReady(BBCode2Html($gameInfo['description']));
						echo "</div>";

						echo "<hr/>";
						echo "<form action='/games/".$gameInfo['gameID']."/ method='post' class='alignRight'><button type='submit' name='visitGameDetails' class='fancyButton'>Game details</button></form>";
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
				<span>
					<form id="threadOptions" method="post" action="/forums/process/modThread/">
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
			$message = preg_replace("/\[quote(?:=\"([\w\.]+?)\")?\](.*?)\[\/quote\]/sm", "", $message);

			$ret = Array();
			preg_match_all('/\@([0-9a-zA-Z\-\.\_]+)/', $message, $matches, PREG_SET_ORDER);

			if (sizeof($matches)) {
				foreach ($matches as $match) {
					$checkUsername=rtrim($match[1],".-_ \n\r\t\v\0");
					$mentionUserId = $mysql->query("SELECT userID FROM users WHERE username = '{$checkUsername}'")->fetchColumn();
					if($mentionUserId && !in_array($mentionUserId,$ret)){
						$ret[] = $mentionUserId;
					}
				}
			}

			return $ret;
		}

		private function removeOldMentions($post){

			//It is possible that this can simply be replaced with the code below - but without indexes I'm leery of performance
			/*
						$mongo->users->updateMany(
							[],
							['$pull' => [
								'mentions' => ['postID'=>((int) $this->postID)]
								]
							]
						);
			*/
			global $mysql;
			$mongo = DB::conn('mongo');

			$oldMessage = $mysql->query("SELECT message FROM posts WHERE postID = {$post->postID}")->fetchColumn();

			$userIds = ThreadManager::getUserIdsFromMentions($oldMessage);

			foreach ($userIds as $userId) {
				$mongo->users->updateOne(
					['userID' => ((int)$userId)],
					['$pull' => [
						'threadNotifications' => ['postID'=>((int) $post->getPostID())]
						]
					]
				);
			}
		}

		private function addMentions($post){
			global $mysql;
			$mongo = DB::conn('mongo');

			$userIds = ThreadManager::getUserIdsFromMentions($post->message);

			if (count($userIds)>0) {

				//strip "Re: " if present
				$postTitle=$post->getTitle();
				if(substr($postTitle,0,4)=='Re: '){
					$postTitle=substr($postTitle,4);
				}


				foreach ($userIds as $mentionUserId) {
					$mongo->users->updateOne(
						['userID' => ((int)$mentionUserId)],
						['$push' => [
							'threadNotifications' => [
								'threadID' => (int)$this->threadID,
								'postID' => ((int) $post->getPostID()),
								'forumTitle'=>$this->getForumProperty('title'),
								'threadTitle' => $postTitle,
								'notificationType' => ThreadNotificationTypeEnum::MENTION
							]
						]]
					);
				}
			}
		}


		private function addThreadNotification($notificationType, $post){

			if($notificationType==ThreadNotificationTypeEnum::NEW_POST){
				return;  //Not putting these on the homepage.  But if we use push notifications then this would be the point to intercept them
			}

			$gameID=$this->forumManager->forums[$this->thread->forumID]->getGameID();
			if($gameID){
				global $mysql, $currentUser, $mongo;

				$threadIdAsInt = (int)$this->threadID;
				$postIdAsInt = (int)$post->getPostID();

				$gameInfo = $mongo->games->findOne(
					['gameID' => $gameID]
				);

				if($notificationType==ThreadNotificationTypeEnum::MAJOR_EDIT){
					$pull = ['$pull' => [
						'threadNotifications' => ['postID'=>$postIdAsInt]
						]
					];
				}
				else if($notificationType==ThreadNotificationTypeEnum::NEW_POST){
					$pull = ['$pull' => [
						'threadNotifications' => ['threadID'=>$threadIdAsInt,'notificationType'=>$notificationType]
						]
					];
				}

				//strip "Re: " if present
				$postTitle=$post->getTitle();
				if(substr($postTitle,0,4)=='Re: '){
					$postTitle=substr($postTitle,4);
				}


				foreach ($gameInfo['players'] as &$player) {
					$playerUserId=$player['user']['userID'];
					if($playerUserId!=$currentUser->userID && $player['approved']){

						//pull previous notifications
						$mongo->users->updateOne(
							['userID' => ((int)$playerUserId)],
							$pull
						);

						$mongo->users->updateOne(
							['userID' => ((int)$playerUserId)],
							['$push' => [
								'threadNotifications' => [
									'threadID' => $threadIdAsInt,
									'postID' => $postIdAsInt,
									'forumTitle'=>$this->getForumProperty('title'),
									'threadTitle' => $postTitle,
									'notificationType' => $notificationType
								]
							]]
						);
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