<?
	class ThreadManager {
		protected $threadID;
		protected $thread;
		protected $forumManager;

		public function __construct($threadID = null, $forumID = null) {
			if (intval($threadID))	{
				global $mysql, $currentUser;

				$this->threadID = intval($threadID);
				$thread = $mysql->query("SELECT t.threadID, t.forumID, t.locked, t.sticky, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, t.firstPostID, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.threadID = {$this->threadID} LIMIT 1");
				$this->thread = $thread->fetch();
	//			throw new Exception('No thread');
				if (!$this->thread) return false;
				$this->thread = new Thread($this->thread);

				$this->forumManager = new ForumManager($this->thread->forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS);
			} elseif (intval($forumID)) {
				$this->thread = new Thread();
				$this->thread->forumID = $forumID;
				$this->forumManager = new ForumManager($forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS);
			}
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function __set($key, $value) {
			if (property_exists($this, $key)) $this->$key = $value;
		}

		public function getThreadProperty($property) {
			if (preg_match('/(\w+)\[(\w+)\]/', $property, $matches)) return $this->thread->{$matches[1]}[$matches[2]];
			elseif (preg_match('/(\w+)->(\w+)/', $property, $matches)) return $this->thread->$matches[1]->$matches[2];
			else return $this->thread->$property;
		}

		public function getForumProperty($key) {
			return $this->forumManager->getForumProperty($this->thread->forumID, $key);
		}

		public function getFirstPostID() {
			return $this->thread->getFirstPostID();
		}

		public function isGameForum() {
			return $this->forumManager->forums[$this->thread->forumID]->isGameForum();
		}

		public function getPermissions($permission = null) {
			return $this->forumManager->getForumProperty($this->thread->forumID, 'permissions'.($permission != null?"[{$permission}]":''));
		}

		public function getThreadLastRead() {
			if ($this->getForumProperty('markedRead') > $this->getThreadProperty('lastRead')) 
				return $this->getForumProperty('markedRead');
			else 
				return $this->getThreadProperty('lastRead');
		}

		public function getPosts($page = 1) {
			return $this->thread->getPosts($page);
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

		public function saveThread($post) {
			global $mysql;

			if ($this->threadID == null) {
				$mysql->query("INSERT INTO threads SET forumID = {$this->thread->forumID}, sticky = ".$this->thread->getStates('sticky').", locked = ".$this->thread->getStates('locked').", allowRolls = ".$this->thread->getAllowRolls().", allowDraws = ".$this->thread->getAllowDraws().", postCount = 1");
				$this->threadID = $mysql->lastInsertId();

				$post->setThreadID($this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET firstPostID = {$postID}, lastPostID = {$postID} WHERE threadID = {$this->threadID}");

				$this->updateLastRead($postID);
			} else {
				$mysql->query("UPDATE threads SET forumID = {$this->thread->forumID}, sticky = ".($this->thread->getStates('sticky')?1:0).", locked = ".($this->thread->getStates('locked')?1:0).", allowRolls = ".($this->thread->getAllowRolls()?1:0).", allowDraws = ".($this->thread->getAllowDraws()?1:0)." WHERE threadID = ".$this->threadID);
				$postID = $post->savePost();
			}

			$this->thread->savePoll($this->threadID);

			return $postID;
		}

		public function updateLastRead($postID) {
			global $loggedIn, $mysql, $currentUser;
			if ($loggedIn) $mysql->query("INSERT INTO forums_readData_threads SET threadID = {$this->threadID}, userID = {$currentUser->userID}, lastRead = {$postID} ON DUPLICATE KEY UPDATE lastRead = {$postID}");
		}

		public function displayPagination($page) {
			$page = intval($page) > 0?intval($page):1;
			if ($page > ceil($this->postCount / PAGINATE_PER_PAGE)) $page = ceil($this->postCount / PAGINATE_PER_PAGE);

			if ($this->thread->postCount > PAGINATE_PER_PAGE) {
				$spread = 2;
				echo "\t\t\t<div class=\"paginateDiv\">";
				$numPages = ceil($this->thread->postCount / PAGINATE_PER_PAGE);
				$firstPage = $page - $spread;
				if ($firstPage < 1) $firstPage = 1;
				$lastPage = $page + $spread;
				if ($lastPage > $numPages) $lastPage = $numPages;
				echo "\t\t\t\t<div class=\"currentPage\">{$page} of {$numPages}</div>\n";
				if (($page - $spread) > 1) echo "\t\t\t\t<a href=\"?page=1\">&lt;&lt; First</a>\n";
				if ($page > 1) echo "\t\t\t\t<a href=\"?page=".($page - 1)."\">&lt;</a>\n";
				for ($count = $firstPage; $count <= $lastPage; $count++) echo "\t\t\t\t<a href=\"?page=$count\"".(($count == $page)?' class="page"':'').">$count</a>\n";
				
				if ($page < $numPages) echo "\t\t\t\t<a href=\"?page=".($page + 1)."\">&gt;</a>\n";
				if (($page + $spread) < $numPages) echo "\t\t\t\t<a href=\"?page={$numPages}\">Last &gt;&gt;</a>\n";
				echo "\t\t\t</div>\n";
				echo "\t\t\t<br class=\"clear\">\n";
			}
		}
	}
?>