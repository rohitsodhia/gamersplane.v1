<?
	class ForumSearch {
		protected $search = '';
		protected $forumManager;
		protected $searchIn = null;
		protected $results = array();
		protected $resultsCount = 0;
		protected $page = 1;
		protected $searchText = '';
		protected $gameID = 0;

		public function __construct($search, $searchIn = array(), $useForumManager = null) {
			global $mysql, $currentUser;

			$this->search = $search;
			$forumManager = $useForumManager ? $useForumManager: new ForumManager(0);
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

		public function searchText($text,$gameID){
			$this->searchText = $text;
			$this->gameID = ((int)$gameID);
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
				$this->resultsCount = $mysql->query("SELECT t.threadID FROM threads t INNER JOIN posts p ON t.lastPostID = p.postID WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND p.datePosted > NOW() - INTERVAL 1 WEEK")->rowCount();
				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND lp.datePosted > NOW() - INTERVAL 1 WEEK ORDER BY lp.datePosted DESC LIMIT {$start}, {$limit}")->fetchAll(PDO::FETCH_OBJ);
			} elseif ($this->search == 'latestGamePosts') {
				$this->resultsCount = $mysql->query("SELECT t.threadID FROM threads t INNER JOIN posts p ON t.lastPostID = p.postID INNER JOIN forums f ON t.forumID = f.forumID WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND p.datePosted > NOW() - INTERVAL 1 WEEK AND f.gameID IS NOT NULL")->rowCount();
				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND lp.datePosted > NOW() - INTERVAL 1 WEEK AND f.gameID IS NOT NULL ORDER BY lp.datePosted DESC LIMIT {$start}, {$limit}")->fetchAll(PDO::FETCH_OBJ);
			} elseif ($this->search == 'latestPublicPosts') {
				$this->resultsCount = $mysql->query("SELECT t.threadID FROM threads t INNER JOIN posts p ON t.lastPostID = p.postID INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN forums_permissions_general as fpg ON f.forumID = fpg.forumID WHERE fpg.read=1 AND p.datePosted > NOW() - INTERVAL 1 WEEK AND f.gameID IS NOT NULL")->rowCount();
				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID INNER JOIN forums_permissions_general as fpg ON f.forumID = fpg.forumID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE fpg.read=1 AND lp.datePosted > NOW() - INTERVAL 1 WEEK AND f.gameID IS NOT NULL ORDER BY lp.datePosted DESC LIMIT {$start}, {$limit}")->fetchAll(PDO::FETCH_OBJ);
			} elseif ($this->search == 'homepage') {
				$this->resultsCount = $mysql->query("SELECT t.threadID FROM threads t INNER JOIN posts p ON t.lastPostID = p.postID WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND p.datePosted > NOW() - INTERVAL 1 WEEK ")->rowCount();
				$this->results = $mysql->query("SELECT t.threadID, t.forumID, f.title forum, t.locked, t.sticky, fp.postID firstPostID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, lp.postID lastPostID, lp.authorID lp_authorID, lpa.username lp_username, lp.datePosted lp_datePosted FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.firstPostID = fp.postID INNER JOIN users fpa ON fp.authorID = fpa.userID INNER JOIN posts lp ON t.lastPostID = lp.postID INNER JOIN users lpa ON lp.authorID = lpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND lp.datePosted > NOW() - INTERVAL 1 WEEK AND f.gameID IS NULL ORDER BY lp.datePosted DESC LIMIT {$start}, {$limit}")->fetchAll(PDO::FETCH_OBJ);
			} elseif ($this->search == 'text') {
				$rowCountStmt=$mysql->prepare("SELECT COUNT(*) FROM threads t INNER JOIN posts p ON t.threadID = p.threadID INNER JOIN forums ON t.forumID = forums.forumID WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND MATCH (p.messageFullText) AGAINST (? IN BOOLEAN MODE)".($this->gameID ? " AND forums.gameID={$this->gameID}" : ""));
				$rowCountStmt->execute([$this->searchText]);
				$this->resultsCount = $rowCountStmt->fetchColumn();
				$resultsStmt = $mysql->prepare("SELECT t.threadID, t.forumID, f.title forum, f.heritage, t.locked, t.sticky, fp.postID, fp.title, fp.authorID, fpa.username, fp.datePosted, IFNULL(rdt.lastRead, 0) lastRead, t.postCount, fp.postID lastPostID, fp.authorID lp_authorID, fpa.username lp_username, fp.datePosted lp_datePosted,fp.messageFullText FROM threads t INNER JOIN forums f ON t.forumID = f.forumID INNER JOIN posts fp ON t.threadID = fp.threadID INNER JOIN users fpa ON fp.authorID = fpa.userID LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} WHERE t.forumID IN (".implode(', ', $this->forumManager->getAccessableForums()).") AND MATCH (messageFullText) AGAINST (? IN BOOLEAN MODE)".($this->gameID ? " AND f.gameID = {$this->gameID} " : "")." ORDER BY fp.datePosted DESC LIMIT {$start}, {$limit}");
				$resultsStmt->execute([$this->searchText]);
				$this->results = $resultsStmt->fetchAll(PDO::FETCH_OBJ);
			}
		}

		function getTextSnippet($text, $maxChars) {
			$words = preg_split('/[\s]+/', $text, null, PREG_SPLIT_DELIM_CAPTURE);
			$wordCount = count($words);
			$ret = '';
			$length=0;
			for ($i=0; $i < $wordCount; $i++) {
				$length += (strlen($words[$i]) + 1);
				if ($length > $maxChars) {
					$ret.='...';
					break;
				}
				else{
					$ret.=$words[$i];
					$ret.=' ';
				}
			}
			return trim($ret);
		}

		public function displayFullTextResults(){
			?>
			<ul class="ft_search">
			<?
			if (sizeof($this->results)) { foreach ($this->results as $result) {
				$forumIcon = $result->lastPostID > $this->forumManager->getForumProperty($result->forumID, 'markedRead') && $result->lastPostID > $result->lastRead?'new':'old';
				$postTitle=$result->title;
				if(substr($postTitle,0,4)=='Re: '){
					$postTitle=substr($postTitle,4);
				}
				$heritageArray=explode('-', $result->heritage);
				$heritageRoot=ltrim($heritageArray[0], '0');
				?>
				<li>
					<h3><a href="/forums/thread/<?=$result->threadID?>/?p=<?=$result->postID?>#p<?=$result->postID?>"><?=$postTitle?></a></h3>
					<div class="ft_post_info"><a href="/forums/<?=$result->forumID?>/" class="ft_forum"><i class="ra forum-icon forum-root-<?=$heritageRoot?> forum-id-<?=$result->forumID?>"></i> <?=$result->forum?></a> <span class="ft_poster"><a href="/user/<?=$result->lp_authorID?>/" class="username"><?=$result->lp_username?></a><span class="convertTZ"><?=date('M j, Y g:i a', strtotime($result->lp_datePosted))?></span></span></div>
					<p class="ft_snippet"><?=$this->getTextSnippet($result->messageFullText,200)?></p>
				</li>
				<?
			}}
			?>
			<ul class="ft_search">
			<?
		}

		public function displayResults() {
			if($this->search == 'text'){
				return $this->displayFullTextResults();
			}
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
					<div class="td icon"><a href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><div class="forumIcon<?=$forumIcon == 'new'?' newPosts':''?>" title="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread" alt="<?=$forumIcon == 'new'?'New':'No new'?> posts in thread"></div></a></div>
					<div class="td threadInfo">
<?				if ($forumIcon == 'new') { ?>
						<a class="threadInfoNew" href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><img src="/images/forums/newPost.png" title="View new posts" alt="View new posts"></a>
<?				} else {?><span class="threadInfoNew"></span><?} ?>
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
						<a class="threadTitle" href="/forums/thread/<?=$result->threadID?>/"><?=$result->title?></a><br>
						<span class="threadAuthor">by <a href="/user/<?=$result->authorID?>/" class="username"><?=$result->username?></a> in <a href="/forums/<?=$result->forumID?>/"><?=$result->forum?></a> on <span class="convertTZshort"><?=date('M j, Y g:i a', strtotime($result->datePosted))?></span></span>
					</div>
					<div class="td numPosts"><?=$result->postCount?></div>
					<div class="td lastPost">
					<a href="/forums/<?=$result->forumID?>/" class="forumDisplay"><?=$result->forum?></a><a href="/user/<?=$result->lp_authorID?>/" class="username"><?=$result->lp_username?></a><br><span class="convertTZshort"><?=date('M j, Y g:i a', strtotime($result->lp_datePosted))?></span>
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

				ForumSearch::displayLatestPostResultHP($result,$newPosts);
			}
		}

		public static function displayLatestPostResultHP($result,$newPosts) {
?>
					<div class="post">
						<a href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><div class="forumIcon<?=$newPosts?' newPosts':''?>"></div></a>
						<div class="title"><a href="/forums/thread/<?=$result->threadID?>/?view=newPost#newPost"><?=$result->title?></a></div>
						<div class="byLine">by <a href="/user/<?=$result->lp_authorID?>/" class="username"><?=$result->lp_username?></a>, <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($result->lp_datePosted))?></div>
						<div class="forum">in <a href="/forums/<?=$result->forumID?>/"><?=$result->forum?></a></div>
					</div>
<?
		}


		public function displayLatestHPWidget($header,$footer,$sectionclass,$headerbarclass=''){
			if($this->getResultsCount()>0){
				echo '<h3 class="headerbar '.$headerbarclass.'">';
				echo $header;
				echo '</h3><div class="widgetBody widget-'.$sectionclass.'">';
				$this->displayLatestHP();
				echo '<div class="latestPostsLink">';
				echo $footer;
				echo '</div></div>';

				return true;
			}
			return false;
		}

		public function displayHeader(){
			global $mysql;
			if ($this->search == 'latestGamePosts') {
				echo '<h1 class="headerbar"><i class="ra ra-d6"></i> Latest Game Posts</h1>';
			} elseif ($this->search == 'latestPublicPosts') {
				echo '<h1 class="headerbar"><i class="ra ra-horn-call"></i> Lastest Public Game Posts</h1>';
			} elseif ($this->search == 'text') {
				if($this->gameID){
					$gameTitle = $mysql->query("SELECT title FROM forums WHERE gameID = {$this->gameID} AND parentID=2")->fetchColumn();
					echo '<span class="searchTitle"><i class="ra ra-telescope"></i> '.$gameTitle.'</span>';
				} else {
					echo '<span class="searchTitle"><i class="ra ra-telescope"></i> Search</span>';
				}
			} else {
				echo '<h1 class="headerbar"><i class="ra ra-speech-bubble"></i> Latest Posts</h1>';
			}
		}

		public function displayPagination(){
			if($this->search == 'text'){
				ForumView::displayPagination($this->getResultsCount(), $this->getPage(), array('search' => $this->search, 'q' => $this->searchText, 'gameID' => $this->gameID));
			} else {
				ForumView::displayPagination($this->getResultsCount(), $this->getPage(), array('search' => $this->search));
			}
		}
	}
?>