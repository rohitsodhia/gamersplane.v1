<?
	class ForumPoll {
		protected $threadID;
		protected $question;
		protected $optionsPerUser;
		protected $pollLength;
		protected $allowRevoting;
		protected $options;

		public function __construct($threadID) {
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
				$options = $options->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_OBJ);
				array_walk($options, function (&$value, $key) { $value = $value[0]; });
				$this->options = $options;
			} else throw new Exception('No poll');
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function getVotesCast() {
			$cast = array();
			foreach ($this->options as $option) 
				if ($option->voted) $cast[] = $option->pollOptionID;
			return $cast;
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