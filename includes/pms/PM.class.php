<?
	class PM {
		protected $pmID;
		protected $title;
		protected $message;
		protected $datestamp;
		protected $read;
		protected $replyTo;

		protected $sender;
		protected $recipients = array();

		public function __construct($pmID = null, $pmData = null) {
			if ($pmID === null) return false;

			$this->pmID = (int) $pmID;
			foreach (get_object_vars($this) as $key => $value) {
//				if (in_array($key, array('children', 'threads', 'lastPost'))) continue;
				if (!array_key_exists($key, $pmData)) continue;//throw new Exception('Missing data for '.$this->forumID.': '.$key);
				$this->$key = $pmData[$key];
			}

			var_dump($this);
		}

		public function getForumID() {
			return $this->forumID;
		}

		public function getTitle($pr = false) {
			if ($pr) return printReady($this->title);
			else return $this->title;
		}

		public function getDescription($pr = false) {
			if ($pr) return printReady($this->description);
			else return $this->description;
		}

		public function getType() {
			return $this->forumType;
		}

		public function getParentID() {
			return $this->parentID;
		}

		public function getHeritage($string = false) {
			if ($string) {
				$heritage = array();
				foreach ($this->heritage as $forumID) 
					if ($forumID != 0) 
						$heritage[] = sql_forumIDPad($forumID);
				return implode('-', $heritage);
			} else return $this->heritage;
		}

		public function getPermissions($permission = null) {
			if (in_array($permission, $this->permissions)) 
				return $this->permissions[$permission];
			else 
				return $this->permissions;
		}

		public function setChild($childID, $order) {
			$this->children[$order] = $childID;
		}

		public function getChildren() {
			return $this->children;
		}

		public function sortChildren() {
			ksort($this->children);
		}

		public function getGameID() {
			return $this->gameID;
		}

		public function isGameForum() {
			return $this->gameID?true:false;
		}

		public function getMarkedRead() {
			return $this->markedRead;
		}

		public function getThreads($page = 1) {
			global $currentUser, $mysql;

			$page = intval($page) > 0?intval($page):1;
			$offset = ($page - 1) * PAGINATE_PER_PAGE;

			$threads = $mysql->query("SELECT t.threadID, t.locked, t.sticky, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.forumID = {$this->forumID} ORDER BY t.sticky DESC, lp.datePosted DESC LIMIT {$offset}, ".PAGINATE_PER_PAGE);
			foreach ($threads as $thread) 
				$this->threads[] = new Thread($thread);
		}
	}
?>