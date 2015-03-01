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
		}

		public function getPMID() {
			return $this->pmID;
		}

		public function setTitle($title) {
			$this->title = $title;
		}

		public function getTitle($pr = false) {
			if ($pr) return printReady($this->title);
			else return $this->title;
		}

		public function getMessage($pr = false) {
			if ($pr) return printReady($this->message);
			else return $this->message;
		}

		public function setSender($sender) {
			$this->sender = (object) $sender;
		}

		public function getSender($key = null) {
			if (is_object($this->sender) && property_exists($this->sender, $key)) return $this->sender->$key;
			else return $this->sender;
		}

		public function addRecipient($nRecipient) {
			foreach ($this->recipients as $recipient) 
				if ($recipient->userID == $recipient->userID) 
					return false;
			$this->recipients[] = (object) $nRecipient;
		}

		public function getRecipients() {
			return $this->recipients;
		}

		public function hasAccess($userID = null) {
			if ($userID == null) {
				global $currentUser;
				$userID = $currentUser->userID;
			}

			if ($this->sender->userID == $userID) return true;
			else { foreach ($this->recipients as $recipient) {
				if ($recipient->userID == $userID) return true;
			} }

			return false;
		}

		public function getDatestamp($format = null) {
			if (is_string($format)) return date($format, strtotime($this->datestamp));
			else return $this->datestamp;
		}

		public function getRead() {
			return $this->read;
		}

		public function setReplyTo($replyTo) {
			$replyTo = intval($replyTo) > 0?intval($replyTo):null;
			$this->replyTo = $replyTo;
		}

		public function getReplyTo() {
			return $this->replyTo;
		}
	}
?>