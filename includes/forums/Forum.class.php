<?
	class Forum {
		protected $forumID;
		protected $title;
		protected $description;
		protected $forumType;
		protected $parentID;
		protected $heritage;
		protected $order;
		protected $gameID;
		protected $threadCount;

		protected $threads = array();
		
		public function __construct($loadData = null) {
			if (is_array($loadData)) {
				foreach (get_object_vars($this) as $key => $value) {
					if (!isset($loadData[$key])) throw new Exception('Missing data');
					$this->$key = $loadData[$key];
				}
				$this->heritage = explode('-', $this->heritage);
				array_walk($this->heritage, function ($value, $key) { return intval($value); });
			} elseif (is_int($loadData)) {
				
			}
		}

		public function __set($key, $value) {
			if ($key == 'forumID' && intval($value)) 
				$this->forumID = intval($value);
			elseif (in_array($key, array('title', 'description'))) 
				$this->$key = $value;
			elseif ($key == 'forumType' && in_array(strtolower($value), array('f', 'c'))) 
				$this->forumType = strtolower($value);
			elseif (in_array($key, array('parentID', 'order', 'threadCount'))) 
				$this->$key = intval($value);
			elseif ($key == 'gameID' && (intval($value) || $value == null)) $this->gameID = $value;
		}

		public function __get($key) {
			if (isset($this->$key)) return $this->$key;
		}

		public function getThreads($offset = 0) {
			global $mysql, $currentUser;

			$offset = intval($offset);

			$threads = $mysql->query("SELECT t.threadID, t.locked, t.sticky, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID INNER JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = ".($currentUser->userID?$currentUser->userID:0)." WHERE t.forumID = {$this->forumID} ORDER BY t.sticky DESC, lp.datePosted DESC LIMIT {$offset}, ".PAGINATE_PER_PAGE);
			foreach ($threads as $thread) $this->threads[] = new Thread($thread);

			return true;
		}
	}
?>