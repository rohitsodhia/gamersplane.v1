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

			$this->forumManager = new ForumManager($this->thread->forumID, ForumManager::NO_CHILDREN|ForumManager::NO_NEWPOSTS);
		}

		public function __get($key) {
			if (property_exists($this, $key)) return $this->$key;
		}

		public function getThreadProperty($forumID, $property) {
			if (preg_match('/(\w+)\[(\w+)\]/', $property, $matches)) return $this->thread[$forumID]->{$matches[1]}[$matches[2]];
			elseif (preg_match('/(\w+)->(\w+)/', $property, $matches)) return $this->thread[$forumID]->$matches[1]->$matches[2];
			else return $this->thread[$forumID]->$property;
		}

		public function getPosts($page = 1) {
			$this->thread->getPosts($page);
		}

		public function getPermissions($permission = null) {
			return $this->forumManager->getForumProperty($this->thread->forumID, 'permissions'.($permission != null?"[{$permission}]":''));
		}

		public function getForumProperty($key) {
			return $this->forumManager->getForumProperty($this->thread->forumID, $key);
		}
	}
?>