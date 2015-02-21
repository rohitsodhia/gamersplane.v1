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

		public function getThreadID() {
			return $this->threadID;
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
			if ($this->getForumProperty('markedRead') > $this->getThreadProperty('lastRead')) 
				return $this->getForumProperty('markedRead');
			else 
				return $this->getThreadProperty('lastRead');
		}

		public function getPosts() {
			global $mysql;

			if (isset($_GET['view']) && $_GET['view'] == 'newPost') {
				$numPrevPosts = $mysql->query("SELECT COUNT(postID) numPosts FROM posts WHERE threadID = {$threadID} AND postID <= ".$this->getThreadLastRead());
				$numPrevPosts = $numPrevPosts->fetchColumn() + 1;
				$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
			} elseif (isset($_GET['post'])) {
				$post = intval($_GET['post']);
				$numPrevPosts = $mysql->query("SELECT COUNT(postID) FROM posts WHERE threadID = {$this->threadID} AND postID <= {$post}");
				$numPrevPosts = $numPrevPosts->fetchColumn();
				$page = $numPrevPosts?ceil($numPrevPosts / PAGINATE_PER_PAGE):1;
			} else $page = intval($_GET['page']);
			$this->page = intval($page) > 0?intval($page):1;

			return $this->thread->getPosts($this->page);
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

		public function saveThread($post) {
			global $mysql;

			if ($this->threadID == null) {
				$mysql->query("INSERT INTO threads SET forumID = {$this->thread->forumID}, sticky = ".$this->thread->getStates('sticky', true).", locked = ".$this->thread->getStates('locked', true).", allowRolls = ".$this->thread->getAllowRolls(true).", allowDraws = ".$this->thread->getAllowDraws(true).", postCount = 1");
				$this->threadID = $mysql->lastInsertId();

				$post->setThreadID($this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET firstPostID = {$postID}, lastPostID = {$postID} WHERE threadID = {$this->threadID}");

				$this->updateLastRead($postID);
			} else {
				$mysql->query("UPDATE threads SET forumID = {$this->thread->forumID}, sticky = ".($this->thread->getStates('sticky')?1:0).", locked = ".($this->thread->getStates('locked')?1:0).", allowRolls = ".($this->thread->getAllowRolls()?1:0).", allowDraws = ".($this->thread->getAllowDraws()?1:0)." WHERE threadID = ".$this->threadID);
				$postID = $post->savePost();

				$mysql->query("UPDATE threads SET lastPostID = {$postID} WHERE threadID = {$this->threadID}");
			}

			$this->thread->savePoll($this->threadID);

			return $postID;
		}

		public function updateLastRead($postID) {
			global $loggedIn, $mysql, $currentUser;
			if ($loggedIn) $mysql->query("INSERT INTO forums_readData_threads SET threadID = {$this->threadID}, userID = {$currentUser->userID}, lastRead = {$postID} ON DUPLICATE KEY UPDATE lastRead = {$postID}");
		}

		public function displayPagination($page) {
			ForumView::displayPagination($this->getThreadProperty('postCount'), $this->page);
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
		}
	}
?>