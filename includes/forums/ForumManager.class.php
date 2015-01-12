<?
	class ForumManager {
		protected $currentForum;

		public function __construct($forumID) {
			global $mysql, $currentUser;

			$forumID = intval($forumID);
			$forumInfos = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN forums p ON p.forumID = {$loadData} AND f.heritage LIKE CONCAT(p.heritage, '%')".($loadData == 0 || $loadData == 2?' WHERE heritage NOT LIKE LPAD(2, '.HERITAGE_PAD.', 0) OR forumID = 10':'')." ORDER BY LENGTH(f.heritage)");
			$forumInfos = $forumInfos->fetchAll();
			if ($loadData == 0 || $loadData == 2) {
				$publicGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN games g ON f.gameID = g.gameID AND g.public = 1");
				$userGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount FROM forums f INNER JOIN players p ON f.gameID = p.gameID AND p.userID = {$currentUser->userID}");
				$forumInfos = array_merge($forumInfos, $publicGameForums->fetchAll(), $userGameForums->fetchAll());
			}

			$this->currentForum = new Forum($forumID);
		}
	}
?>