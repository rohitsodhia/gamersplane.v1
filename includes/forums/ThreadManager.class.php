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

			$newPost=!($post->getPostID());

			if(!$newPost){
				$this->removeOldMentions($post);
			}

			if ($this->threadID == null) {
				$stmt=$mysql->prepare("INSERT INTO threads SET forumID = {$this->thread->forumID}, sticky = ".$this->thread->getStates('sticky', true).", locked = ".$this->thread->getStates('locked', true).", allowRolls = ".$this->thread->getAllowRolls(true).", allowDraws = ".$this->thread->getAllowDraws(true).", postCount = 1, publicPosting = ".($this->thread->getStates('publicPosting',true)?1:0).", discordWebhook = :discordWebhook");
				$stmt->bindValue(':discordWebhook', $this->getThreadProperty('discordWebhook'));
				$stmt->execute();
				$this->threadID = $mysql->lastInsertId();

				$post->setThreadID($this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET firstPostID = {$postID}, lastPostID = {$postID} WHERE threadID = {$this->threadID}");
				$mysql->query("UPDATE forums SET threadCount = threadCount + 1 WHERE forumID = {$this->thread->forumID}");

				$this->updateLastRead($postID);

			} else {
				$stmt=$mysql->prepare("UPDATE threads SET forumID = {$this->thread->forumID}, sticky = ".($this->thread->getStates('sticky')?1:0).", locked = ".($this->thread->getStates('locked')?1:0).", allowRolls = ".($this->thread->getAllowRolls()?1:0).", allowDraws = ".($this->thread->getAllowDraws()?1:0).", publicPosting = ".($this->thread->getStates('publicPosting')?1:0).", discordWebhook = :discordWebhook WHERE threadID = ".$this->threadID);
				$stmt->bindValue(':discordWebhook', $this->getThreadProperty('discordWebhook'));
				$stmt->execute();

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

			global $mysql, $currentUser, $mongo;
			$threadIdAsInt = (int)$this->threadID;

			//strip "Re: " if present
			$postTitle=$post->getTitle();
			if(substr($postTitle,0,4)=='Re: '){
				$postTitle=substr($postTitle,4);
			}

			if($notificationType==ThreadNotificationTypeEnum::NEW_POST || $notificationType==ThreadNotificationTypeEnum::MAJOR_EDIT){
				$discordWebhook = $mysql->query("SELECT discordWebhook FROM threads WHERE threadID = {$threadIdAsInt}")->fetchColumn();

				if($discordWebhook){
					$userAvatar="https://".getenv('APP_URL').User::getAvatar($currentUser->userID);
					$avatar=$userAvatar;
					$postAsName=$currentUser->username;
					$postAsId= $post->getPostAs();

					if($postAsId && $postAsId!='p'){
						$charInfo = $mongo->characters->findOne(['characterID' => $postAsId]);
						if($charInfo){
							if (file_exists(FILEROOT . "/characters/avatars/{$postAsId}.jpg")) {
								$avatar="https://".getenv('APP_URL')."/characters/avatars/{$postAsId}.jpg";
							}
							$postAsName=$charInfo['name'];
						}
					} else {
						$npc = Post::extractPostingNpc($post->getMessage());
						if ($npc) {
							$avatar=$npc["avatar"];
							$postAsName=$npc["name"];
						}
					}

					$in = array(
						"/\[quote(?:=\"([\w\.]+?)\")?\](((?R)|.)*?)\[\/quote\]/ms",
						"/\[snippets=\"?(.*?)\"?\](.*?)\[\/snippets\]/ms",
						"/\[abilities=\"?(.*?)\"?\](.*?)\[\/abilities\]/ms",
						"/\[poll=\"?(.*?)?\"([^\]]*)\](.*?)\[\/poll\]/ms"
						);
					$out = array('','','','Poll (\1) \3');


					$discordMessage = preg_replace($in, $out, $post->getMessage());



					$discordMessage=ForumSearch::getTextSnippet(Post::extractFullText($discordMessage),200);

					$data = array(
						'username' => $postAsName,
						'avatar_url'=> $avatar,
						'embeds'=>array(
							array(
								'url'=>'https://'.getenv('APP_URL').'/forums/thread/'.($this->threadID).'/?p='.($post->postID).'#p'.($post->postID),
								'title'=>$postTitle,
								'color' => 13395456, //#cc6600
								'description'=>$discordMessage,
								'footer'=> array(
									'text'=>$currentUser->username.($notificationType==ThreadNotificationTypeEnum::MAJOR_EDIT?" ~ edited post":""),
									'icon_url'=>$userAvatar
								)
							)
						)
					);

					$options = array(
						'http' => array(
							'header'  => "Content-type: application/json\r\n",
							'method'  => 'POST',
							'content' => json_encode($data),
							'ignore_errors' => true
						)
					);

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

			$gameID=$this->forumManager->forums[$this->thread->forumID]->getGameID();
			if($gameID){
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
