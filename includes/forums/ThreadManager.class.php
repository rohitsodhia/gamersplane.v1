<?
	class ThreadManager {
		protected $threadID;
		protected $thread;
		protected $forumManager;

		public function __construct($threadID) {
			global $mysql, $currentUser;

			$this->threadID = intval($threadID);
			$thread = $mysql->query("SELECT t.threadID, t.forumID, t.locked, t.sticky, fp.title, fp.authorID, tAuthor.username authorUsername, fp.datePosted, lp.postID lp_postID, lp.authorID lp_authorID, lAuthor.username lp_username, lp.datePosted lp_datePosted, t.postCount, IFNULL(rd.lastRead, 0) lastRead FROM threads t INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users tAuthor ON fp.authorID = tAuthor.userID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users lAuthor ON lp.authorID = lAuthor.userID LEFT JOIN forums_readData_threads rd ON t.threadID = rd.threadID AND rd.userID = {$currentUser->userID} WHERE t.threadID = {$this->threadID} LIMIT 1");
			$this->thread = $thread->fetch();
			$this->thread = new Thread($this->thread);

			$this->forumManager = new ForumManager($this->thread->forumID);
		}

		public function getPermissions($permission = null) {
			return $this->forumManager->getForumProperty($this->thread->forumID, 'permissions'.($permission != null?"[{$permission}]":''));
		}

		public function getLastPost($forumID) {
			$forum = $this->forums[$forumID];

			$lastPost = new stdClass();
			$lastPost->postID = 0;
			if (sizeof($forum->children)) {
				foreach ($forum->children as $cForumID) {
					$cLastPost = $this->getLastPost($cForumID);
					if ($cLastPost && $cLastPost->postID > $lastPost->postID) 
						$lastPost = $cLastPost; 
				}
			}
			if ($forum->permissions['read'] && $forum->lastPost->postID > $lastPost->postID) return $forum->lastPost;
			elseif ($lastPost->postID != 0) return $lastPost;
			else return null;
		}
	}
?>