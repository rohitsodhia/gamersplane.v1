<?
	class Thread {
		protected $threadID;
		protected $forumID;
		protected $title;
		protected $authorID;
		protected $authorUsername;
		protected $datePosted;
		protected $sticky = false;
		protected $locked = false;
		protected $allowRolls = false;
		protected $allowDraws = false;
		protected $postCount = 0;

		protected $firstPostID = 0;
		protected $lastPost = null;
		protected $lastRead = 0;

		protected $posts = array();
		protected $poll = null;

		protected $loaded = array();
		
		public function __construct($loadData = null) {
			$this->poll = new ForumPoll();
			
			if ($loadData == null) return true;

			if (!isset($loadData['threadID'], $loadData['title'])) throw new Exception('Need more thread info');
			foreach ($loadData as $key => $value) 
				if (property_exists($this, $key)) $this->$key = $loadData[$key];
			if (isset($loadData['lp_postID'], $loadData['lp_authorID'], $loadData['lp_username'], $loadData['lp_datePosted'])) {
				$this->lastPost = new stdClass();
				$this->lastPost->postID = $loadData['lp_postID'];
				$this->lastPost->authorID = $loadData['lp_authorID'];
				$this->lastPost->username = $loadData['lp_username'];
				$this->lastPost->datePosted = $loadData['lp_datePosted'];
			}
		}

		public function toggleValue($key) {
			if (in_array($key, array('sticky', 'locked', 'allowRolls', 'allowDraws'))) $this->$key = !$this->$key;
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function __set($key, $value) {
			if (property_exists($this, $key)) $this->$key = $value;
		}

		public function newPosts($markedRead) {
			if ($this->lastPost->postID > $this->lastRead && $this->lastPost->postID > $markedRead) return true;
			else return false;
		}

		public function getPosts($page = 1) {
			if (sizeof($this->posts)) return $this->posts;

			global $loggedIn, $currentUser, $mysql;

			$page = intval($page) > 0?intval($page):1;
			if ($page > ceil($this->postCount / PAGINATE_PER_PAGE)) $page = ceil($this->postCount / PAGINATE_PER_PAGE);
			$start = ($page - 1) * PAGINATE_PER_PAGE;
			$posts = $mysql->query("SELECT p.postID, p.title, u.userID, u.username, um.metaValue avatarExt, p.message, p.postAs, p.datePosted, p.lastEdit, p.timesEdited FROM posts p LEFT JOIN users u ON p.authorID = u.userID LEFT JOIN usermeta um ON u.userID = um.userID AND um.metaKey = 'avatarExt' WHERE p.threadID = {$this->threadID} ORDER BY p.datePosted LIMIT {$start}, ".PAGINATE_PER_PAGE);
			if ($loggedIn) $mysql->query("INSERT INTO forums_readData_threads SET threadID = {$this->threadID}, userID = {$currentUser->userID}, lastRead = {$this->lastPost->postID} ON DUPLICATE KEY UPDATE lastRead = {$this->lastPost->postID}");
			foreach ($posts as $post) $this->posts[$post['postID']] = new Post($post);

			$rolls = $mysql->query("SELECT postID, rollID, type, reason, roll, indivRolls, results, visibility, extras FROM rolls WHERE postID IN (".implode(',', array_keys($this->posts)).") ORDER BY rollID");
			foreach ($rolls as $rollInfo) 
				$this->posts[$rollInfo['postID']]->addRoll($rollInfo);
			
			$draws = $mysql->query("SELECT postID, drawID, type, cardsDrawn, reveals, reason FROM deckDraws WHERE postID IN (".implode(',', array_keys($this->posts)).") ORDER BY drawID");
			foreach ($draws as $drawInfo) 
				$this->posts[$drawInfo['postID']]->addDraw($drawInfo);

			return $this->posts;
		}

		public function getPoll() {
			if (in_array('poll', $this->loaded)) return true;
			try {
				$this->poll = new ForumPoll($this->threadID);
				$this->loaded[] = 'poll';
				return true;
			} catch (Exception $e) { return false; }
		}

		public function getPollProperty($key) {
			return $this->poll->$key;
		}

		public function getVotesCast() {
			return $this->poll->getVotesCast();
		}

		public function getVoteTotal() {
			return $this->poll->getVoteTotal();
		}

		public function getVoteMax() {
			return $this->poll->getVoteMax();
		}
	}
?>