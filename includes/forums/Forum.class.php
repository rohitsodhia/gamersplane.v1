<?
	class Forum {
		protected $forumID;
		protected $title;
		protected $description;
		protected $forumType;
		protected $parentID;
		protected $heritage;
		protected $childCount;
		protected $order;
		protected $gameID = null;
		protected $threadCount;

		protected $postCount;
		protected $lastPost = null;
		protected $markedRead = 0;
		protected $newPosts = false;

		protected $permissions = array();
		protected $children = array();
		protected $threads = array();

		public function __construct($forumID = null, $forumData = null) {
			if ($forumID === null) return true;

			$this->forumID = (int) $forumID;
			foreach (get_object_vars($this) as $key => $value) {
				if (in_array($key, array('children', 'threads', 'lastPost'))) continue;
				if (is_array($forumData) && !array_key_exists($key, $forumData)) continue;//throw new Exception('Missing data for '.$this->forumID.': '.$key);
				$this->__set($key, $forumData[$key]);
			}
			$this->heritage = array_map('intval', explode('-', $this->heritage));
			if ($this->forumID != 0) $this->heritage = array_merge([0], $this->heritage);
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
			elseif (in_array($key, array('parentID', 'childCount', 'order', 'threadCount', 'postCount', 'markedRead')))
				$this->$key = intval($value);
			elseif ($key == 'newPosts')
				$this->newPosts = $value?true:false;
			elseif ($key == 'gameID' && (intval($value) || $value == null)) $this->gameID = $value != null?intval($value):null;
		}

		public function __get($key) {
			if (isset($this->$key))
				return $this->$key;
		}

		public function getForumID() {
			return $this->forumID;
		}

		public function getTitle($pr = false) {
			if ($pr)
				return printReady($this->title);
			else
				return $this->title;
		}

		public function getDescription($pr = false) {
			if ($pr)
				return printReady($this->description);
			else
				return $this->description;
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
			if (array_key_exists($permission, $this->permissions))
				return $this->permissions[$permission];
			else
				return $this->permissions;
		}

		public function setChild($childID, $order) {
			$this->children[$order] = $childID;
		}

		public function unsetChild($forumID) {
			unset($this->children[array_search($forumID, $this->children)]);
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

			$threads = $mysql->query("SELECT t.threadID, t.locked, t.sticky, t.publicPosting, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.forumID = {$this->forumID} ORDER BY t.sticky DESC, lp.datePosted DESC LIMIT {$offset}, ".PAGINATE_PER_PAGE);
			foreach ($threads as $thread)
				$this->threads[] = new Thread($thread);
		}

		public function deleteForum() {
			global $mysql;

			$mysql->query("DELETE f, c, t, p, po, popt, pv, pge, pgr, pu, rdf, rdt, r, d FROM forums f INNER JOIN forums c ON c.heritage LIKE CONCAT(f.heritage, '%') LEFT JOIN threads t ON c.forumID = t.forumID LEFT JOIN posts p ON t.threadID = p.threadID LEFT JOIN forums_polls po ON t.threadID = po.threadID LEFT JOIN forums_pollOptions popt ON t.threadID = popt.threadID LEFT JOIN forums_pollVotes pv ON popt.pollOptionID = pv.pollOptionID LEFT JOIN forums_permissions_general pge ON c.forumID = pge.forumID LEFT JOIN forums_permissions_groups pgr ON c.forumID = pgr.forumID LEFT JOIN forums_permissions_users pu ON c.forumID = pu.forumID LEFT JOIN forums_readData_forums rdf ON c.forumID = rdf.forumID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID LEFT JOIN rolls r ON p.postID = r.postID LEFT JOIN deckDraws d ON p.postID = d.postID WHERE f.forumID = {$this->forumID}");
		}

		public function rootHeritage(){
			$heritageArray=$this->getHeritage();
			return $heritageArray[1];
		}
	}
?>
