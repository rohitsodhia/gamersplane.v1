<?
	class Thread {
		protected $threadID;
		protected $title;
		protected $authorID;
		protected $authorUsername;
		protected $datePosted;
		protected $sticky = false;
		protected $locked = false;
		protected $allowRolls = false;
		protected $allowDraws = false;
		protected $postCount = 0;

		protected $lastPost = null;
		protected $lastRead;

		protected $posts = array();
		
		public function __construct($loadData = null) {
			if (is_array($loadData)) {
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
		}

		public function toggleValue($key) {
			if (in_array($key, array('sticky', 'locked', 'allowRolls', 'allowDraws'))) $this->$key = !$this->$key;
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function newPosts($)
	}
?>