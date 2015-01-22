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

		protected $postCount;
		protected $lastPost = null;
		protected $markedRead = 0;
		protected $newPosts = false;

		protected $permissions = array();
		protected $admin = false;
		protected $children = array();
		protected $threads = array();

		public function __construct($forumID, $forumData = null) {
			$this->forumID = (int) $forumID;
			foreach (get_object_vars($this) as $key => $value) {
				if (in_array($key, array('children', 'threads', 'lastPost', 'admin'))) continue;
				if (!isset($forumData[$key]) && $forumData[$key] != null) throw new Exception('Missing data for '.$this->forumID.': '.$key);
				$this->__set($key, $forumData[$key]);
			}
			$this->heritage = explode('-', $this->heritage);
			array_walk($this->heritage, function (&$value, $key) { $value = intval($value); });
			if ($this->forumID != 0) $this->heritage = array_merge(array(0), $this->heritage);
			if (isset($forumData['lastPostID'])) {
				$this->lastPost = new stdClass();
				$this->lastPost->postID = $forumData['lastPostID'];
				$this->lastPost->userID = $forumData['userID'];
				$this->lastPost->username = $forumData['username'];
				$this->lastPost->datePosted = $forumData['datePosted'];
			}
		}

		public function __set($key, $value) {
			if ($key == 'forumID' && intval($value)) 
				$this->forumID = intval($value);
			elseif (in_array($key, array('title', 'description', 'heritage', 'permissions'))) 
				$this->$key = $value;
			elseif ($key == 'forumType' && in_array(strtolower($value), array('f', 'c'))) 
				$this->forumType = strtolower($value);
			elseif (in_array($key, array('parentID', 'order', 'threadCount', 'markedRead'))) 
				$this->$key = intval($value);
			elseif ($key == 'newPosts') 
				$this->newPosts = $value?true:false;
			elseif ($key == 'gameID' && (intval($value) || $value == null)) $this->gameID = $value != null?intval($value):null;
		}

		public function setChild($childID, $order) {
			$this->children[$order] = $childID;
		}

		public function sortChildren() {
			ksort($this->children);
		}

		public function __get($key) {
			if (isset($this->$key)) return $this->$key;
		}

		public function getThreads($page = 1) {
			global $currentUser, $mysql;

			$offset = (intval($page) - 1) * PAGINATE_PER_PAGE;

			$threads = $mysql->query("SELECT t.threadID, t.locked, t.sticky, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.forumID = {$this->forumID} ORDER BY t.sticky DESC, lp.datePosted DESC LIMIT {$offset}, ".PAGINATE_PER_PAGE);
			foreach ($threads as $thread) 
				$this->threads[] = new Thread($thread);
			var_dump($this->threads);
		}
	}
?>