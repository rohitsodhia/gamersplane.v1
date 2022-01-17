<?PHP
	class Post {
		protected $postID;
		protected $threadID;
		protected $title;
		protected $author;
		protected $message;
		protected $datePosted;
		protected $lastEdit = null;
		protected $timesEdited = 0;
		protected $postAs;

		protected $rolls = [];
		protected $draws = [];

		protected $modified = false;
		protected $edited = false;

		public function __construct($loadData = null) {
			if ($loadData == null) return true;

			if ((int) $loadData == $loadData) {
				global $mysql;

				$loadData = $mysql->query("SELECT p.postID, p.threadID, p.title, u.userID, u.username, um.metaValue avatarExt, u.lastActivity, p.message, p.postAs, p.datePosted, p.lastEdit, p.timesEdited FROM posts p LEFT JOIN users u ON p.authorID = u.userID LEFT JOIN usermeta um ON u.userID = um.userID AND um.metaKey = 'avatarExt' WHERE p.postID = {$loadData}")->fetch();
			}
			if (is_array($loadData)) {
				foreach (get_object_vars($this) as $key => $value) {
					if (in_array($key, ['author', 'rolls', 'draws', 'modified', 'edited'])) {
						continue;
					}
					if (!array_key_exists($key, $loadData)) {
						continue;//throw new Exception('Missing data for '.$this->forumID.': '.$key);
					}
					$this->$key = $loadData[$key];
				}
				$this->author = new stdClass();
				$this->author->userID = $loadData['userID'];
				$this->author->username = $loadData['username'];
				$this->author->avatarExt = $loadData['avatarExt'];
				$this->author->lastActivity = $loadData['lastActivity'];
			}
		}

		public function __set($key, $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}

		public function __get($key) {
			if (property_exists($this, $key)) {
				return $this->$key;
			}
		}

		public function getPostID() {
			return $this->postID;
		}

		public function setThreadID($threadID) {
			if (intval($threadID)) {
				$this->threadID = intval($threadID);
			}
		}

		public function getThreadID() {
			return $this->threadID;
		}

		public function setTitle($value) {
			$title = sanitizeString(htmlspecialchars_decode($value));
			if ($title != $this->getTitle()) {
				$this->modified = true;
			}
			$this->title = $title;
		}

		public function getTitle($pr = false) {
			if ($pr) {
				return printReady($this->title);
			} else {
				return $this->title;
			}
		}

		public function getAuthor($key = null) {
			if ($this->author && property_exists($this->author, $key)) {
				return $this->author->$key;
			} else {
				return $this->author;
			}
		}

		public function setMessage($value) {
			global $currentUser;
			$mysql = DB::conn('mysql');

			$isForumAdmin = $mysql->query("SELECT userID FROM forumAdmins WHERE userID = {$currentUser->userID} AND forumID = 0");
			$message = sanitizeString($value, $isForumAdmin->rowCount() ? '!strip_tags' : '');
			if ($message != $this->getMessage()) {
				$this->modified = true;
			}
			$this->message = $message;
		}

		public function getMessage($pr = false) {
			if ($pr) {
				return printReady($this->message);
			} else {
				return $this->message;
			}
		}

		public static function cleanNotes($message) {
			global $currentUser;

			preg_match_all('/\[note="?(\w[\w\. +;,]+?)"?](.*?)\[\/note\][\n\r]*/ms', $message, $matches, PREG_SET_ORDER);
			if (sizeof($matches)) {
				foreach ($matches as $match) {
					$noteTo = preg_split('/[^\w\.]+/', $match[1]);
					if (!in_array($currentUser->username, $noteTo)) {
						$message = str_replace($match[0], '', $message);
					}
				}
			}

			preg_match_all('/\[private="?(\w[\w\. +;,]+?)"?](.*?)\[\/private\][\n\r]*/ms', $message, $matches, PREG_SET_ORDER);
			if (sizeof($matches)) {
				foreach ($matches as $match) {
					$noteTo = preg_split('/[^\w\.]+/', $match[1]);
					if (!in_array($currentUser->username, $noteTo)) {
						$message = str_replace($match[0], '', $message);
					} else {
						$message = str_replace($match[0], $match[2].chr(13), $message);
					}

				}
			}

			$message = preg_replace("/\[npc=\"?(.*?)\"?\](.*?)\[\/npc\]*/ms", "", $message);

			return trim($message);
		}

		public function getDatePosted($format = null) {
			if ($format != null) {
				return date($format, strtotime($this->datePosted));
			} else {
				return $this->datePosted;
			}
		}

		public function getLastEdit() {
			return $this->lastEdit;
		}

		public function setPostAs($value) {
			$this->postAs = intval($value) ? intval($value) : null;
		}

		public function addRollObj($rollObj) {
			$this->rolls[] = $rollObj;
		}

		public function loadRoll($rollInfo) {
			$rollObj = RollFactory::getRoll($rollInfo['type']);
			$rollObj->forumLoad($rollInfo);
			$this->rolls[] = $rollObj;
		}

		public function addDraw($deckID, $drawInfo) {
			$this->draws[$deckID] = $drawInfo;
		}

		public function updateEdited() {
			$this->edited = true;
			$this->timesEdited += 1;
		}

		public function getPostAs() {
			return $this->postAs;
		}

		public function savePost() {
			global $currentUser;
			$mysql = DB::conn('mysql');
			$mongo = DB::conn('mongo');

			if ($this->postID == null) {
				$addPost = $mysql->prepare("INSERT INTO posts SET threadID = {$this->threadID}, title = :title, authorID = {$currentUser->userID}, message = :message, messageFullText = :messageFullText, datePosted = :datePosted, postAs = ".($this->postAs?$this->postAs:'NULL'));
				$addPost->bindValue(':title', $this->title);
				$addPost->bindValue(':message', $this->message);
				$addPost->bindValue(':messageFullText', Post::extractFullText($this->message));
				$addPost->bindValue(':datePosted', date('Y-m-d H:i:s'));
				$addPost->execute();
				$this->postID = $mysql->lastInsertId();
			} else {
				$updatePost = $mysql->prepare("UPDATE posts SET title = :title, message = :message, messageFullText = :messageFullText, postAs = " . ($this->postAs ? $this->postAs : 'NULL') . ($this->edited ? ", lastEdit = NOW(), timesEdited = {$this->timesEdited}" : '') . " WHERE postID = {$this->postID}");
				$updatePost->bindValue(':title', $this->title);
				$updatePost->bindValue(':message', $this->message);
				$updatePost->bindValue(':messageFullText', Post::extractFullText($this->message));

				$updatePost->execute();
			}

			foreach ($this->rolls as $roll) {
				$roll->forumSave($this->postID);
			}

			if (sizeof($this->draws)) {
				$addDraw = $mysql->prepare("INSERT INTO deckDraws SET postID = {$this->postID}, deckID = :deckID, type = :type, cardsDrawn = :cardsDrawn, reveals = :reveals, reason = :reason");
				foreach($this->draws as $deckID => $draw) {
					$gameID = (int) $mysql->query("SELECT f.gameID FROM threads t INNER JOIN forums f ON f.forumID = t.forumID WHERE t.threadID = {$this->threadID} LIMIT 1")->fetchColumn();
					$mongo->games->updateOne(
						['gameID' => $gameID, 'decks.deckID' => (int) $deckID], ['$inc' => ['decks.$.position' => (int) $draw['draw']]]
					);
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

		public function loadRolls(){
			if($this->postID!=null){
				$mysql = DB::conn('mysql');
				$rolls = $mysql->query("SELECT postID, rollID, type, reason, roll, indivRolls, results, visibility, extras FROM rolls WHERE postID =".$this->postID." ORDER BY rollID");
				foreach ($rolls as $rollInfo) {
					$this->loadRoll($rollInfo);
				}
			}
		}

		public function getModified() {
			return $this->modified;
		}

		public function getNpc(){
			$text=$this->message;
			return Post::extractPostingNpc($text);
		}

		public static function extractPostingNpc($text){
			$text=preg_replace('/\[code\](.*?)\[\/code\]/ms', "", $text);
			preg_match_all('/\[npc=\"?(.*?)\"?\](.*?)\[\/npc\]/ms', $text, $matches, PREG_SET_ORDER);

			if ($matches && count($matches)==1) {
				return array('name' => $matches[0][1],'avatar'=>$matches[0][2]);
			}

			return null;
		}

		public function getPollResults(){
			global $currentUser;

			$mongo = DB::conn('mongo');

			$voteCollection=$mongo->threads->findOne(['threadID' => ((int)$this->getThreadID())]);

			$ret = array('voted'=>false,'votes'=>array(),'publicVotes'=>array());
			if($this->postID){
				if($voteCollection && is_countable($voteCollection["votes"])){
					foreach ($voteCollection["votes"] as $voteItem) {
						if($voteItem['postID']==$this->postID){
							$voteNum=(int)$voteItem['vote'];
							if(!array_key_exists($voteNum,$ret['votes'])){
								$ret['votes'][$voteNum]=array('votes'=>0,'me'=>false,'html'=>'');
							}

							$ret['votes'][$voteNum]['votes']=$ret['votes'][$voteNum]['votes']+1;

							$myVote=false;
							if($voteItem['userID']==$currentUser->userID){
								$ret['votes'][$voteNum]['me']=true;
								$ret['voted']=true;
								$myVote=true;
							}

							if($voteItem['username']){
								$ret['votes'][$voteNum]['html'].='<span class="pollAvatar'.($myVote?' pollIVoted':'').'" title='.htmlspecialchars($voteItem['username']).' style="background-image:url('.User::getAvatar($voteItem['userID']).');"></span>';
							}
							else{
								$ret['votes'][$voteNum]['html'].='<i class="ra ra-gamers-plane'.($myVote?' pollIVoted':'').'"></i>';
							}
						}
					}
				}
			}
			return $ret;
		}

		public static function extractFullText($message) {

			//remove notes and private
			$message = Post::cleanNotes($message);

			//remove the contents of these tags
			$in = array(
				'/\[code\](.*?)\[\/code\]/ms',
				'/\[img\](.*?)\[\/img\]/ms',
				"/\[youtube\]https:\/\/youtu.be\/(.*?)\[\/youtube\]/ms",
				"/[\r\n]*\[style\](.*?)\[\/style\][\r\n]*/ms",
				"/\[map\](.*?)\[\/map\]/ms",
				"/\[spotify\](.*?)\[\/spotify\]/ms"
				);
			$out = array('','','','','','');


			$message = preg_replace($in, $out, $message);

			//replace all other tags
			$message = preg_replace('/\[.*?\]/ms', ' ', $message);
			//collapse the whitespae
			$message = str_replace("\r\n", "\n", $message);
			$message = str_replace("\n", " ", $message);
			$message = preg_replace('/\s+/', ' ', $message);
			$message = trim($message);

			return $message;

		}


		public function delete() {
			$mysql = DB::conn('mysql');

			$mysql->query('DELETE FROM posts, rolls, deckDraws USING posts LEFT JOIN rolls ON posts.postID = rolls.postID LEFT JOIN deckDraws ON posts.postID = deckDraws.postID WHERE posts.postID = ' . $this->postID);
		}

		public function dumpObj() {
			var_dump($this);
		}
	}
?>
