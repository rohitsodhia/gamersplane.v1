<?
	class ForumPoll {
		protected $threadID;
		protected $question;
		protected $options = array();
		protected $oldOptions = array();
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

		public function setThreadID($value) {
			$this->threadID = intval($value);
		}

		public function setQuestion($value) {
			$this->question = sanitizeString(html_entity_decode($value));
		}

		public function getQuestion($pr = false) {
			if ($pr) return printReady($this->question);
			else return $this->question;
		}

		public function parseOptions($value) {
			if (sizeof($this->options)) $this->oldOptions = $this->options;
			$this->options = array();
			$options = preg_split('/\n/', $value);
			array_walk($options, function (&$value, $key) { $value = sanitizeString($value); });
			foreach ($options as $option) 
				if (strlen($option)) $this->options[] = $option;
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

		public function savePoll($threadID = null) {
			global $mysql;

			if (strlen($this->question) == 0 || sizeof($this->options) == 0) return null;

			if ($threadID != null && is_int($threadID)) {
				$this->threadID = intval($threadID);
				if (strlen($this->question) && sizeof($this->options)) {
					$addPollOptions = $mysql->prepare("INSERT INTO forums_pollOptions SET threadID = {$this->threadID}, `option` = :option");
					foreach ($this->getPollProperty('options') as $option) {
						$addPollOptions->bindValue(':option', $option);
						$addPollOptions->execute();
					}
				}
			} else {
				$options = preg_split('/\n/', $value);
				array_walk($options, function (&$value, $key) { $value = sanitizeString($value); });
				$loadedOptions = array();
				foreach ($this->oldOptions as $pollOptionID => $option) 
					$loadedOptions[] = $option;
				$addPollOption = $mysql->prepare("INSERT INTO forums_pollOptions SET threadID = {$this->threadID}, `option` = :option");
				foreach ($loadedOptions as $option) {
					if (in_array($option, $loadedOptions)) unset($loadedOptions[array_search($option, $loadedOptions)]);
					else {
						$addPollOption->bindValue(':option', $option);
						$addPollOption->execute();
					}
					if (sizeof($loadedOptions)) $mysql->query('DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.pollOptionID IN ('.implode(', ', array_keys($loadedOptions)).')');
				}
			}
			$addPoll = $mysql->prepare("INSERT INTO forums_polls (threadID, poll, optionsPerUser, allowRevoting) VALUES ({$this->threadID}, :poll, :optionsPerUser, :allowRevoting) ON DUPLICATE KEY UPDATE poll = :poll, optionsPerUser = :optionsPerUser, allowRevoting = :allowRevoting");
			$addPoll->bindValue(':poll', $this->question);
			$addPoll->bindValue(':optionsPerUser', $this->optionsPerUser);
			$addPoll->bindValue(':allowRevoting', $this->allowRevoting?1:0);
			$addPoll->execute();
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

		public function delete() {
			global $mysql;

			$mysql->query("DELETE FROM po, pv USING forums_pollOptions po LEFT JOIN forums_pollVotes pv ON po.pollOptionID = pv.pollOptionID WHERE po.threadID = {$this->threadID}");
			$mysql->query("DELETE FROM forums_polls WHERE threadID = {$this->threadID}");
		}
	}
?>