<?
	class Post {
		protected $postID;
		protected $threadID;
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

		public function setThreadID($threadID) {
			if (intval($threadID)) $this->threadID = intval($threadID);
		}

		public function setTitle($value) {
			$this->title = sanitizeString(html_entity_decode($value));
		}

		public function getTitle($pr = false) {
			if ($pr) return printReady($this->title);
			else return $this->title;
		}

		public function setMessage($value) {
			$this->message = sanitizeString($value);
		}

		public function getMessage($pr = false) {
			if ($pr) return printReady($this->message);
			else return $this->message;
		}

		public function setPostAs($value) {
			$this->postAs = intval($value)?intval($value):null;
		}

		public function addRollObj($rollObj) {
			$this->rolls[] = $rollObj;
		}

		public function loadRoll($rollInfo) {
			$rollObj = RollFactory::getRoll($rollInfo['type']);
			$rollObj->forumLoad($rollInfo);
			$this->rolls[] = $rollObj;
		}

		public function addDraw($drawInfo) {
			$this->draws[] = $drawInfo;
		}

		public function savePost() {
			global $mysql, $currentUser;

			$addPost = $mysql->prepare("INSERT INTO posts SET threadID = {$this->threadID}, title = :title, authorID = {$currentUser->userID}, message = :message, datePosted = :datePosted, postAs = ".($this->postAs?$this->postAs:'NULL'));
			$addPost->bindValue(':title', $this->title);
			$addPost->bindValue(':message', $this->message);
			$addPost->bindValue(':datePosted', date('Y-m-d H:i:s'));
			$addPost->execute();
			$this->postID = $mysql->lastInsertId();

			foreach ($this->rolls as $roll) 
				$roll->forumSave($this->postID);

			if (sizeof($this->draws)) {
				$addDraw = $mysql->prepare("INSERT INTO deckDraws SET postID = {$this->postID}, deckID = :deckID, type = :type, cardsDrawn = :cardsDrawn, reveals = :reveals, reason = :reason");
				foreach($draws as $deckID => $draw) {
					$mysql->query("UPDATE decks SET position = position + {$draw['draw']} WHERE deckID = {$deckID}");
					$addDraw->bindValue('deckID', $deckID);
					$addDraw->bindValue('type', $draw['type']);
					$addDraw->bindValue('cardsDrawn', $draw['cardsDrawn']);
					$addDraw->bindValue('reveals', str_repeat('0', $draw['draw']));
					$addDraw->bindValue('reason', $draw['reason']);
					$addDraw->execute();
				}
			}

			return $this->postID;
		}

		public function dumpObj() {
			var_dump($this);
		}
	}
?>