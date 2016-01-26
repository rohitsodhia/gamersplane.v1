<?
	class ForumSearch {
		protected $search = '';
		protected $forumManager;
		protected $searchIn = null;
		protected $results = array();
		protected $resultsCount = 0;
		protected $page = 1;

		public function __construct($search, $searchIn = array()) {
			global $mysql, $currentUser;

			$this->search = $search;
			$forumManager = new ForumManager(0);
			$this->forumManager = $forumManager;
			if (is_array($searchIn) && sizeof($searchIn)) 
				$this->searchIn = $searchIn;
			else $this->searchIn = null;
		}

		public function getResultsCount() {
			return $this->resultsCount;
		}

		public function getPage() {
			return $this->page;
		}

		public function getPostsSince() {
			$checkPostsSince = $mysql->query("SELECT attemptStamp FROM loginRecords WHERE successful = 1 AND userID = {$currentUser->userID} AND attemptStamp < NOW() - INTERVAL 3 HOUR ORDER BY attemptStamp DESC LIMIT 1");
			if ($checkPostsSince->rowCount()) {
				$checkPostsSince = $checkPostsSince->fetchColumn();
				if (strtotime('-3 Days') > strtotime($checkPostsSince)) $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Days'));
			} else $checkPostsSince = date('Y-m-d H:i:s', strtotime('-3 Hour'));

			return $checkPostsSince;
		}

		public function findThreads($page = 1, $limit = PAGINATE_PER_PAGE) {
			global $mysql, $currentUser;

			if (intval($limit) <= 0) $limit = PAGINATE_PER_PAGE;
			$this->page = intval($page) > 0?intval($page):1;
			$start = ($this->page - 1) * PAGINATE_PER_PAGE;
			if ($this->search == 'latestPosts') {
				$this->resultsCount = $mysql->query("SELECT t.threadID FROM threads t INNER JOIN posts p ON t.lastPostID = p.postID WHERE t.forumID IN (".implode(', ', array_keys($this->forumManager->getAccessableForums())).") AND p.datePosted > NOW() - INTERVAL 1 WEEK")->rowCount();
				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND lp.datePosted > NOW() - INTERVAL 1 WEEK ORDER BY lp.datePosted DESC LIMIT {$start}, {$limit}")->fetchAll(PDO::FETCH_OBJ);
			} elseif ($this->search == 'homepage') {
				$forums = array();
				$forums = $this->forumManager->getAllChildren(0, true);

				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, IFNULL(rdt.lastRead, 0) lastRead, lp.postID lastPostID, lp.title, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $forums).") ORDER BY lp.datePosted DESC LIMIT 3")->fetchAll(PDO::FETCH_OBJ);
			}
		}

		public function displayResults() {
?>
		<div class="tableDiv threadTable">
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td threadInfo">Thread</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">
<?
			if (sizeof($this->results)) { foreach ($this->results as $result) {
				$forumIcon = $result->lastPostID > $this->forumManager->getForumProperty($result->forumID, 'markedRead') && $result->lastPostID > $result->lastRead?'new':'old';
?>
				<div class="tr">
					<div class="td icon"><div class="forumIcon<?=$forumIcon == 'new'?' newPosts':''?>" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread"></div></div>
					<div class="td threadInfo">
<?				if ($forumIcon == 'new') { ?>
						<a href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><img src="/images/forums/newPost.png" title="View new posts" alt="View new posts"></a>
<?				} ?>
						<div class="paginateDiv">
<?
				if ($result->postCount > PAGINATE_PER_PAGE) {
					$url = "/forums/thread/{$result->threadID}/";
					$numPages = ceil($result->postCount / PAGINATE_PER_PAGE);
					if ($numPages <= 3) { for ($count = 1; $count <= $numPages; $count++) {
?>
							<a href="<?=$url?>?page=<?=$count?>"><?=$count?></a>
<?					} } else { ?>
							<a href="<?=$url?>?page=1">1</a>
							<div>...</div>
<?						for ($count = ($numPages - 1); $count <= $numPages; $count++) { ?>
							<a href="<?=$url?>?page=<?=$count?>"><?=$count?></a>
<?
						}
					}
				}
?>
							<a href="/forums/thread/<?=$result->threadID?>/?view=lastPost#lastPost"><img src="/images/downArrow.png" title="Last post" alt="Last post"></a>
						</div>
						<a href="/forums/thread/<?=$result->threadID?>/"><?=$result->title?></a><br>
						<span class="threadAuthor">by <a href="/user/<?=$result->authorID?>/" class="username"><?=$result->username?></a> in <a href="/forums/<?=$result->forumID?>/"><?=$result->forum?></a> on <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($result->datePosted))?></span></span>
					</div>
					<div class="td numPosts"><?=$result->postCount?></div>
					<div class="td lastPost">
						<a href="/user/<?=$result->lp_authorID?>/" class="username"><?=$result->lp_username?></a><br><span class="convertTZ"><?=date('M j, Y g:i a', strtotime($result->lp_datePosted))?></span>
					</div>
				</div>
<?			} } else { ?>
				<div class="tr noThreads">No results</div>
<?			} ?>
			</div>
		</div>
<?
		}

		public function displayLatestHP() {
			$first = true;
			foreach ($this->results as $result) {
				if (!$first) echo "					<hr>\n";
				else $first = false;

				$newPosts = $result->lastPostID > $this->forumManager->getForumProperty($result->forumID, 'markedRead') && $result->lastPostID > $result->lastRead?true:false;

?>
					<div class="post">
						<div class="forumIcon<?=$newPosts?' newPosts':''?>"></div>
						<div class="title"><a href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><?=$result->title?></a></div>
						<div class="byLine">by <a href="/user/<?=$result->lp_authorID?>/" class="username"><?=$result->lp_username?></a>, <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($result->lp_datePosted))?></div>
						<div class="forum">in <a href="/forums/<?=$result->forumID?>/"><?=$result->forum?></a></div>
					</div>
<?
			}
		}
	}
?>