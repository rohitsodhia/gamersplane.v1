<?
	class ForumSearch {
		protected $forums = array();
		protected $results = array();

		public function __construct($search) {
			global $mysql, $currentUser;

			$forumManager = new ForumManager(0);
			$this->forums = $forumManager->forums;
		}

		public function getPostsSince() {
			$checkPostsSince = $mysql->query("SELECT attemptStamp FROM loginRecords WHERE successful = 1 AND userID = {$currentUser->userID} AND attemptStamp < NOW() - INTERVAL 3 HOUR ORDER BY attemptStamp DESC LIMIT 1");
			if ($checkPostsSince->rowCount()) {
				$checkPostsSince = $checkPostsSince->fetchColumn();
				if (strtotime('-3 Days') > strtotime($checkPostsSince)) $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Days'));
			} else $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Hour'));

			return $checkPostsSince;
		}

		public function findThreads($page = 0) {
			global $mysql, $currentUser;

			$threads = $mysql->query("SELECT t.threadID, t.forumID, t.locked, t.sticky, p.title FROM threads t INNER JOIN posts p ON t.firstPostID = p.postID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', array_keys($this->forums)).")");
		}
	}
?>