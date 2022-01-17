<?
	class Thread {
		protected $threadID;
		protected $forumID;
		protected $title;
		protected $authorID;
		protected $authorUsername;
		protected $datePosted;
		protected $states = array('sticky' => false, 'locked' => false , 'publicPosting' => false);
		protected $allowRolls = false;
		protected $allowDraws = false;
		protected $postCount = 0;

		protected $firstPostID = 0;
		protected $lastPost = null;
		protected $lastRead = 0;

		protected $posts = array();
		protected $poll = null;

		protected $loaded = array();

		protected $pageSize=PAGINATE_PER_PAGE;

		public function __construct($loadData = null) {
			$this->poll = new ForumPoll();

			if ($loadData == null) return true;

			if (!isset($loadData['threadID'], $loadData['title'])) throw new Exception('Need more thread info');
			foreach ($loadData as $key => $value)
				if (property_exists($this, $key)) $this->$key = $loadData[$key];
			$this->states['sticky'] = $loadData['sticky'];
			$this->states['locked'] = $loadData['locked'];
			$this->states['publicPosting'] = $loadData['publicPosting'];
			if (isset($loadData['lp_postID'], $loadData['lp_authorID'], $loadData['lp_username'], $loadData['lp_datePosted'])) {
				$this->lastPost = new stdClass();
				$this->lastPost->postID = $loadData['lp_postID'];
				$this->lastPost->authorID = $loadData['lp_authorID'];
				$this->lastPost->username = $loadData['lp_username'];
				$this->lastPost->datePosted = $loadData['lp_datePosted'];
			}
		}

		public function toggleValue($key) {
			if (in_array($key, array('sticky', 'locked', 'allowRolls', 'allowDraws', 'publicPosting'))) {
				if ($key == 'sticky' || $key == 'locked' || $key == 'publicPosting') $this->states[$key] = !$this->states[$key];
				else $this->$key = !$this->$key;
			}
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function __set($key, $value) {
			if (property_exists($this, $key)) $this->$key = $value;
		}

		public function getStates($key = null, $int = false) {
			if (array_key_exists($key, $this->states)) {
				if ($int) return $this->states[$key]?1:0;
				return $this->states[$key];
			} else return $this->states;
		}

		public function setState($key, $value) {
			if (array_key_exists($key, $this->states) && is_bool($value)) $this->states[$key] = $value;
		}

		public function setAllowRolls($value) {
			if (is_bool($value)) $this->allowRolls = $value;
		}

		public function getAllowRolls($int = false) {
			if ($int) return $this->allowRolls?1:0;
			return $this->allowRolls;
		}

		public function setAllowDraws($value) {
			if (is_bool($value)) $this->allowDraws = $value;
		}

		public function getAllowDraws($int = false) {
			if ($int) return $this->allowDraws?1:0;
			return $this->allowDraws;
		}

		public function getFirstPostID() {
			return $this->firstPostID;
		}

		public function getLastPost($key = null) {
			if (property_exists($this->lastPost, $key))
				return $this->lastPost->$key;
			else
				return $this->lastPost;
		}

		public function newPosts($markedRead) {
			global $loggedIn;
			if (!$loggedIn)
				return false;

			if ($this->lastPost->postID > $this->lastRead && $this->lastPost->postID > $markedRead)
				return true;
			else
				return false;
		}

		public function getPosts($page) {
			if (sizeof($this->posts))
				return $this->posts;

			global $loggedIn, $currentUser, $mysql;

			if ($page > ceil($this->postCount / $this->pageSize)) $page = ceil($this->postCount / $this->pageSize);
			$start = ($page - 1) * $this->pageSize;
			$posts = $mysql->query("SELECT p.postID, p.threadID, p.title, u.userID, u.username, um.metaValue avatarExt, u.lastActivity, p.message, p.postAs, p.datePosted, p.lastEdit, p.timesEdited FROM posts p LEFT JOIN users u ON p.authorID = u.userID LEFT JOIN usermeta um ON u.userID = um.userID AND um.metaKey = 'avatarExt' WHERE p.threadID = {$this->threadID} ORDER BY p.datePosted LIMIT {$start}, ".$this->pageSize);
			foreach ($posts as $post)
				$this->posts[$post['postID']] = new Post($post);

			$rolls = $mysql->query("SELECT postID, rollID, type, reason, roll, indivRolls, results, visibility, extras FROM rolls WHERE postID IN (".implode(',', array_keys($this->posts)).") ORDER BY rollID");
			foreach ($rolls as $rollInfo)
				$this->posts[$rollInfo['postID']]->loadRoll($rollInfo);

			$draws = $mysql->query("SELECT postID, drawID, deckID, type, cardsDrawn, reveals, reason FROM deckDraws WHERE postID IN (".implode(',', array_keys($this->posts)).") ORDER BY drawID");
			foreach ($draws as $drawInfo)
				$this->posts[$drawInfo['postID']]->addDraw($drawInfo['deckID'], $drawInfo);

			return $this->posts;
		}

		public function getPoll() {
			if (in_array('poll', $this->loaded))
				return true;
			try {
				$this->poll = new ForumPoll($this->threadID);
				$this->loaded[] = 'poll';
				return true;
			} catch (Exception $e) { return false; }
		}

		public function getPollProperty($key) {
			return $this->poll->$key;
		}

		public function savePoll($theadID = null) {
			$this->poll->savePoll($theadID);
		}

		public function deletePoll() {
			$this->poll->delete();
			$this->poll = new ForumPoll();
			return true;
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