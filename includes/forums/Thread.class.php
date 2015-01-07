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
		protected $lastPost = array();
		protected $postCount;

		protected $lastRead;

		protected $posts = array();
		
		public function __construct($loadData = null) {
			if (is_array($loadData)) {
				if (!isset($loadData['threadID'], $loadData['title'])) throw new Exception('Need more thread info');
				foreach ($loadData as $key => $value) 
					if (isset($this->$key)) $this->$key = $loadData[$key];
				if (isset($loadData['lp_postID'], $loadData['lp_authorID'], $loadData['lp_username'], $loadData['lp_datePosted'])) 
					$this->lastPost = array(
						'postID' => $loadData['lp_postID'],
						'authorID' => $loadData['lp_authorID'],
						'username' => $loadData['lp_username'],
						'datePosted' => $loadData['lp_datePosted']
					);
				
			}
		}

		public function toggleValue($key) {
			if (in_array($key, array('sticky', 'locked', 'allowRolls', 'allowDraws'))) $this->$key = !$this->$key;
		}

		public function __get($key) {
			if (isset($this->$key)) return $this->$key;
		}
	}
?>