<?
	class ForumManager {
		public $currentForum;
		protected $forumsData = array();

		public function __construct($forumID) {
			global $mysql, $currentUser;

			$forumID = intval($forumID);
			$forumsR = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN forums p ON p.forumID = {$forumID} AND f.heritage LIKE CONCAT(p.heritage, '%')".($forumID == 0 || $forumID == 2?' WHERE f.heritage NOT LIKE LPAD(2, '.HERITAGE_PAD.', 0) OR f.forumID IN (2, 10)':'')." ORDER BY LENGTH(f.heritage)");
			foreach ($forumsR as $forum) $this->forumsData[$forum['forumID']] = $forum;
			if ($forumID == 0 || $forumID == 2) {
				$publicGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN games g ON f.gameID = g.gameID AND g.public = 1");
				foreach ($publicGameForums as $forum) $this->forumsData[$forum['forumID']] = $forum;
				$userGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN players p ON f.gameID = p.gameID AND p.userID = {$currentUser->userID}");
				foreach ($userGameForums as $forum) $this->forumsData[$forum['forumID']] = $forum;
			}
			$permissions = ForumPermissions::getPermissions($currentUser->userID, array_keys($this->forumsData), array('read', 'moderate', 'createThread'), $this->forumsData);
			foreach ($permissions as $pForumID => $permission)
				$this->forumsData[$pForumID]['permissions'] = $permission;
			$this->currentForum = new Forum($forumID, $this->forumsData);
		}
	}
?>