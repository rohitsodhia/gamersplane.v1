<?
	class Post {
		protected $postID;
		protected $title;
		protected $author;
		protected $message;
		protected $datePosted;
		protected $lastEdit;
		protected $timesEdited;
		protected $postAs;

		protected $rolls = array();
		protected $draws = array();
		
		public function __construct($loadData = null) {
			if ($loadData == null) return true;

			foreach (get_object_vars($this) as $key => $value) {
				if (in_array($key, array('author', 'rolls', 'draws'))) continue;
				if (!array_key_exists($key, $loadData)) continue;//throw new Exception('Missing data for '.$this->forumID.': '.$key);
				$this->$key = $loadData[$key];
			}
			$this->author = new stdClass();
			$this->author->userID = $loadData['userID'];
			$this->author->username = $loadData['username'];
			$this->author->avatarExt = $loadData['avatarExt'];
		}

		public function __set($key, $value) {
			if (property_exists($this, $key)) $this->$key = $value;
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function addRoll($rollInfo) {
			$rollObj = RollFactory::getRoll($rollInfo['type']);
			$rollObj->forumLoad($rollInfo);
			$this->rolls[] = $rollObj;
		}

		public function addDraw($drawInfo) {
			$this->draws[] = $drawInfo;
		}
	}
?>