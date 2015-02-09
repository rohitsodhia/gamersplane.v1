<?
	class ForumPoll {
		protected $threadID;
		protected $question;
		protected $options = array();
		protected $optionsPerUser = 1;
		protected $pollLength;
		protected $allowRevoting = false;

		public function __construct($threadID = null) {
			if ($threadID == null) return true;

			global $mysql, $currentUser;

			$this->threadID = (int) $threadID;
			$poll = $mysql->query("SELECT p.poll, p.optionsPerUser, p.pollLength, p.allowRevoting FROM forums_polls p WHERE p.threadID = {$this->threadID}");
			if ($poll->rowCount()) {
				$poll = $poll->fetch();
				$this->question = $poll['poll'];
				$this->optionsPerUser = $poll['optionsPerUser'];
				$this->pollLength = $poll['pollLength'];
				$this->allowRevoting = $poll['allowRevoting'];

				$options = $mysql->query("SELECT po.pollOptionID, po.option, IFNULL(v.votes, 0) votes, IF(pv.votedOn IS NOT NULL, 1, 0) voted FROM forums_pollOptions po LEFT JOIN (SELECT pollOptionID, COUNT(pollOptionID) votes FROM forums_pollVotes GROUP BY pollOptionID) v ON po.pollOptionID = v.pollOptionID LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID AND userID = {$currentUser->userID} WHERE po.threadID = {$this->threadID}");
				if ($options->rowCount()) {
					$options = $options->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_OBJ);
					array_walk($options, function (&$value, $key) { $value = $value[0]; });
					$this->options = $options;
				}
			} else throw new Exception('No poll');
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function setQuestion($value) {
			$this->question = sanitizeString(html_entity_decode($value));
		}

		public function getQuestion($pr = false) {
			if ($pr) return printReady($this->question);
			else return $this->question;
		}

		public function parseOptions($value) {
			$this->options = preg_split('/\n/', $value);
			array_walk($this->options, function (&$value, $key) { $value = sanitizeString($value); });			
		}

		public function getOptions($key = null) {
			if ($key == null) return $this->options;
			elseif (array_key_exists($key, $this->options)) return (object) array_merge(array('pollOptionID' => $key), (array) $this->options[$key]);
			else return null;
		}

		public function setOptionsPerUser($value) {
			$this->optionsPerUser = intval($value);
		}

		public function getOptionsPerUser() {
			return $this->optionsPerUser;
		}

		public function setAllowRevoting($value = null) {
			$this->allowRevoting = $value != null?true:false;
		}

		public function getAllowRevoting() {
			return $this->allowRevoting;
		}

		public function addVotes($votes) {
			global $mysql, $currentUser;

			if ($this->getAllowRevoting()) $this->clearVotesCast();
			$addVote = $mysql->prepare("INSERT INTO forums_pollVotes SET userID = {$currentUser->userID}, pollOptionID = :vote, votedOn = NOW()");
			foreach ($votes as $vote) {
				$addVote->bindParam(':vote', $vote);
				$addVote->execute();
			}
		}

		public function getVotesCast() {
			$cast = array();
			foreach ($this->options as $option) 
				if ($option->voted) $cast[] = $option->pollOptionID;
			return $cast;
		}

		public function clearVotesCast() {
			global $mysql, $currentUser;

			$mysql->query("DELETE v FROM forums_pollVotes v INNER JOIN forums_pollOptions o USING (pollOptionID) WHERE o.threadID = {$this->threadID} AND v.userID = {$currentUser->userID}");
		}

		public function getVoteTotal() {
			$total = 0;
			foreach ($this->options as $option) 
				$total += $option->votes;
			return $total;
		}

		public function getVoteMax() {
			$max = 0;
			foreach ($this->options as $option) 
				if ($option->votes > $max) $max = $option->votes;
			return $max;
		}
	}
?>