<?
	class ForumManager {
		protected $currentForum;
		protected $forumsData = array();
		public $forums = array();
		protected $lastRead = array();

		const NO_CHILDREN = 1;
		const NO_NEWPOSTS = 2;
		const ADMIN_FORUMS = 4;

		public function __construct($forumID, $options = 0) {
			global $mysql, $currentUser;

			$this->currentForum = intval($forumID);
			$forumsR = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f INNER JOIN forums p ON p.forumID = {$this->currentForum} AND (".(bindec($options&$this::NO_CHILDREN) == 0?"f.heritage LIKE CONCAT(p.heritage, '%') OR ":'')."p.heritage LIKE CONCAT(f.heritage, '%')) LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID".($this->currentForum == 0 || $this->currentForum == 2?' WHERE f.heritage NOT LIKE CONCAT(LPAD(2, '.HERITAGE_PAD.', 0), "%") OR f.forumID IN (2, 10)':'')." ORDER BY LENGTH(f.heritage)");
			foreach ($forumsR as $forum) $this->forumsData[$forum['forumID']] = $forum;
			if (($this->currentForum == 0 || $this->currentForum == 2) && bindec($options&$this::NO_CHILDREN) == 0) {
				$publicGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f INNER JOIN games g ON f.gameID = g.gameID AND g.public = 1 LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID");
				foreach ($publicGameForums as $forum) $this->forumsData[$forum['forumID']] = $forum;
				$userGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f INNER JOIN players p ON f.gameID = p.gameID AND p.userID = {$currentUser->userID} LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID");
				foreach ($userGameForums as $forum) $this->forumsData[$forum['forumID']] = $forum;
			}
			$permissions = ForumPermissions::getPermissions($currentUser->userID, array_keys($this->forumsData), null, $this->forumsData);
			foreach ($permissions as $pForumID => $permission)
				$this->forumsData[$pForumID]['permissions'] = $permission;
			if (!($options&$this::NO_NEWPOSTS)) 
				$lastRead = $mysql->query("SELECT f.forumID, rdf.markedRead, IF((unread.numUnread > 0 AND unread.lastUnreadPost > rdf.markedRead) OR (unread.numUnread = 0 AND unread.latestReadPost > rdf.markedRead), 1, 0) newPosts FROM forums f LEFT JOIN forums_readData_forums rdf ON f.forumID = rdf.forumID AND rdf.userID = {$currentUser->userID} LEFT JOIN (SELECT t.forumID, SUM(t.lastPostID > IFNULL(rdt.lastRead, 0)) numUnread, MAX(rdt.lastRead) latestReadPost, MAX(IF(t.lastPostID > IFNULL(rdt.lastRead, 0), t.lastPostID, 0)) lastUnreadPost FROM threads t LEFT JOIN forums_readData_threads rdt ON rdt.userID = {$currentUser->userID} AND t.threadID = rdt.threadID GROUP BY t.forumID) unread ON f.forumID = unread.forumID WHERE f.forumID IN (".implode(',', array_keys($this->forumsData)).")");
			else 
				$lastRead = $mysql->query("SELECT f.forumID, rdf.markedRead FROM forums f LEFT JOIN forums_readData_forums rdf ON f.forumID = rdf.forumID AND rdf.userID = {$currentUser->userID} WHERE f.forumID IN (".implode(',', array_keys($this->forumsData)).")");
			$lastRead = $lastRead->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
			array_walk($lastRead, function (&$value, $key) { $value = $value[0]; });
			foreach ($this->forumsData as $forumID => $forumData) {
				foreach ($lastRead[$forumID] as $key => $value) 
					$this->forumsData[$forumID][$key] = $value;
				$this->spawnForum($forumID);
			}
			foreach (array_keys($this->forumsData) as $forumID) 
				$this->forums[$forumID]->sortChildren();
			if ($options&$this::ADMIN_FORUMS) 
				$this->pruneByPermissions(0, 'admin');
			else 
				$this->pruneByPermissions();
		}

		protected function spawnForum($forumID) {
			if (isset($this->forums[$forumID])) return null;

			$this->forums[$forumID] = new Forum($forumID, $this->forumsData[$forumID]);
			if ($forumID == 0) return null;
			$parentID = $this->forums[$forumID]->parentID;
			if (!isset($this->forums[$parentID])) $this->spawnForum($parentID);
			$this->forums[$parentID]->setChild($forumID, $this->forums[$forumID]->order);
		}

		protected function pruneByPermissions($forumID = 0, $permission = 'read') {
			foreach ($this->forums[$forumID]->children as $childID) 
				$this->pruneByPermissions($childID, $permission);
			if (sizeof($this->forums[$forumID]->children) == 0 && $this->forums[$forumID]->permissions[$permission] == 0) unset($this->forums[$forumID]);
		}

		public function getAccessableForums($validForums = null) {
			if ($validForums == null) $validForums = array();

			$forums = array();
			foreach ($this->forums as $forum) {
				if ($forum->getPermissions('read') && ((sizeof($validForums) && in_array($forum->getForumID(), $validForums)) || sizeof($validForums) == 0)) 
					$forums[] = $forum->getForumID();
			}
			return $forums;
		}

		public function getAllChildren($forumID = 0) {
			$forums = array($forumID);
			$forum = $this->forums[$forumID];
			foreach ($forum->getChildren() as $childID) {
				if (!in_array($childID, $forums)) $forums[] = $childID;
				$this->getAllChildren($childID);
			}
			return $forums;
		}

		public function getForumProperty($forumID, $property) {
			if (preg_match('/(\w+)\[(\w+)\]/', $property, $matches)) return $this->forums[$forumID]->{$matches[1]}[$matches[2]];
			elseif (preg_match('/(\w+)->(\w+)/', $property, $matches)) return $this->forums[$forumID]->$matches[1]->$matches[2];
			else return $this->forums[$forumID]->$property;
		}

		public function displayCheck($forumID = null) {
			if ($forumID == null) $forumID = $this->currentForum;

			if (sizeof($this->forums[$forumID]->children) || $this->forums[$forumID]->permissions['read']) return true;
			else return false;
		}

		public function displayForum() {
			if (sizeof($this->forums[$this->currentForum]->children) == 0) return false;

			$tableOpen = false;
			$lastType = 'f';
			foreach ($this->forums[$this->currentForum]->children as $childID) {
				if ($tableOpen && ($lastType == 'c' || $this->forums[$childID]->forumType == 'c')) {
					$tableOpen = false;
					echo "\t\t\t</div>\n\t\t</div>\n";
				}
				if (!$tableOpen) {
?>
		<div class="tableDiv">
			<div class="clearfix"><h2 class="wingDiv redWing">
				<div><?=$this->forums[$childID]->forumType == 'c'?$this->forums[$childID]->title:'Subforums'?></div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</h2></div>
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td name">Forum</div>
				<div class="td numThreads"># of Threads</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable forumList hbdMargined">
<?
					$tableOpen = true;
				}
				if ($this->forums[$childID]->forumType == 'f') $this->displayForumRow($childID);
				elseif (is_array($this->forums[$childID]->children))
					foreach ($this->forums[$childID]->children as $cChildID) 
						$this->displayForumRow($cChildID);
				$lastType = $this->forums[$childID]->forumType;
			}
			echo "\t\t\t</div>\n\t\t</div>\n";
		}

		public function displayForumRow($forumID) {
			$forum = $this->forums[$forumID];
?>
				<div class="tr<?=$this->newPosts($forumID)?'':' noPosts'?>">
					<div class="td icon"><div class="forumIcon<?=$this->newPosts($forumID)?' newPosts':''?>" title="<?=$this->newPosts($forumID)?'New':'No new'?> posts in forum" alt="<?=$this->newPosts($forumID)?'New':'No new'?> posts in forum"></div></div>
					<div class="td name">
						<a href="/forums/<?=$forum->forumID?>/"><?=printReady($forum->title)?></a>
<?=($forum->description != '')?"\t\t\t\t\t\t<div class=\"description\">".printReady($forum->description)."</div>\n":''?>
					</div>
					<div class="td numThreads"><?=$this->getTotalThreadCount($forumID)?></div>
					<div class="td numPosts"><?=$this->getTotalPostCount($forumID)?></div>
					<div class="td lastPost">
<?
			$lastPost = $this->getLastPost($forumID);
			if ($lastPost) echo "\t\t\t\t\t\t<a href=\"/user/{$lastPost->userID}/\" class=\"username\">{$lastPost->username}</a><br><span class=\"convertTZ\">".date('M j, Y g:i a', strtotime($lastPost->datePosted))."</span>\n";
			else echo "\t\t\t\t\t\t</span>No Posts Yet!</span>\n";
?>
					</div>
				</div>
<?
		}

		public function getTotalThreadCount($forumID) {
			$forum = $this->forums[$forumID];

			$total = 0;
			if (sizeof($forum->children)) 
				foreach ($forum->children as $cForumID) 
					$total += $this->getTotalThreadCount($cForumID);
			if ($forum->permissions['read']) $total += $forum->threadCount;
			return $total;
		}

		public function getTotalPostCount($forumID) {
			$forum = $this->forums[$forumID];

			$total = 0;
			if (sizeof($forum->children)) {
				foreach ($forum->children as $cForumID) 
					$total += $this->getTotalPostCount($cForumID);
			}
			if ($forum->permissions['read']) $total += $forum->postCount;
			return $total;
		}

		public function maxRead($forumID) {
			$maxRead = 0;
			foreach ($this->forums[$forumID]->getHeritage() as $heritageID) {
				if ($this->forums[$heritageID]->getMarkedRead() > $maxRead) 
					$maxRead = $this->forums[$heritageID]->getMarkedRead();
			}

			return $maxRead;
		}

		public function newPosts($forumID) {
			global $loggedIn;
			if (!$loggedIn) return false;
			
			$forum = $this->forums[$forumID];

			if (sizeof($forum->children)) { foreach ($forum->children as $childID) {
				if ($this->newPosts($childID)) return true;
			} }
			if ($forum->newPosts) return true;
			else return false;
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

		public function displayBreadcrumbs() {
?>
				<div id="breadcrumbs">
<?
			if ($this->currentForum != 0) {
				$heritage = $this->forums[$this->currentForum]->heritage;
				$fCounter = 0;
				foreach ($heritage as $hForumID) {
					echo "\t\t\t\t\t<a href=\"/forums/{$hForumID}\">".printReady($this->forums[$hForumID]->title)."</a>".($fCounter != sizeof($heritage) - 1?' > ':'')."\n";
					$fCounter++;
				}
			} else echo "\t\t\t\t\t&nbsp;\n";
?>
				</div>
<?
		}

		public function getThreads($page = 1) {
			$this->forums[$this->currentForum]->getThreads($page);
		}

		public function displayThreads() {
			$forum = $this->forums[$this->currentForum];
			if (!$forum->permissions['read']) return false;

?>
		<div class="tableDiv threadTable">
<?			if ($forum->permissions['createThread']) { ?>
			<div id="newThread" class="clearfix"><a href="/forums/newThread/<?=$forum->forumID?>/" class="fancyButton">New Thread</a></div>
<? 			} ?>
			<div class="tr headerTR headerbar hbDark">
				<div class="td icon">&nbsp;</div>
				<div class="td threadInfo">Thread</div>
				<div class="td numPosts"># of Posts</div>
				<div class="td lastPost">Last Post</div>
			</div>
			<div class="sudoTable threadList hbdMargined">
<?
			if (sizeof($forum->threads)) { foreach ($forum->threads as $thread) {
				$maxRead = $this->maxRead($forum->getForumID());
?>
				<div class="tr">
					<div class="td icon"><div class="forumIcon<?=$thread->getStates('sticky')?' sticky':''?><?=$thread->getStates('locked')?' locked':''?><?=$thread->newPosts($maxRead)?' newPosts':''?>" title="<?=$thread->newPosts($maxRead)?'New':'No new'?> posts in thread" alt="<?=$thread->newPosts($maxRead)?'New':'No new'?> posts in thread"></div></div>
					<div class="td threadInfo">
<?				if ($thread->newPosts($maxRead)) { ?>
						<a href="/forums/thread/<?=$thread->threadID?>/?view=newPost#newPost"><img src="/images/forums/newPost.png" title="View new posts" alt="View new posts"></a>
<?
				}
				if ($thread->numPosts > PAGINATE_PER_PAGE) {
?>
						<div class="paginateDiv">
<?
					$url = '/forums/thread/'.$thread->threadID.'/';
					$numPages = ceil($thread->numPosts / PAGINATE_PER_PAGE);
					if ($numPages <= 4) for ($count = 1; $count <= $numPages; $count++) echo "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
					else {
						echo "\t\t\t\t\t\t\t<a href=\"$url?page=1\">1</a>\n";
						echo "\t\t\t\t\t\t\t<div>...</div>\n";
						for ($count = ($numPages - 2); $count <= $numPages; $count++) echo "\t\t\t\t\t\t\t<a href=\"$url?page=$count\">$count</a>\n";
					}
					echo "\t\t\t\t\t\t</div>\n";
				}
?>
						<a href="/forums/thread/<?=$thread->threadID?>/"><?=$thread->title?></a><br>
						<span class="threadAuthor">by <a href="/ucp/<?=$thread->authorID?>/" class="username"><?=$thread->authorUsername?></a> on <span class="convertTZ"><?=date('M j, Y g:i a', strtotime($thread->datePosted))?></span></span>
					</div>
					<div class="td numPosts"><?=$thread->postCount?></div>
					<div class="td lastPost">
						<a href="/ucp/<?=$thread->lastPost->authorID?>" class="username"><?=$thread->lastPost->username?></a><br><span class="convertTZ"><?=date('M j, Y g:i a', strtotime($thread->lastPost->datePosted))?></span>
					</div>
				</div>
<?
			} } else echo "\t\t\t\t<div class=\"tr noThreads\">No threads yet</div>\n";
		echo "			</div>
		</div>\n";
		}

		public function displayAdminSidelist($forumID = 0, $currentForum = 0) {
			if (!isset($this->forums[$forumID])) return null;

			$forum = $this->forums[$forumID];
			$classes = array();
			if ($forum->getPermissions('admin')) 
				$classes[] = 'adminLink';
			if ($forumID == $currentForum) 
				$classes[] = 'currentForum';
			echo '<li'.(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">\n";
			if ($forum->getPermissions('admin')) 
				echo "<a href=\"/forums/acp/{$forumID}/\">{$forum->getTitle(true)}</a>\n";
			else
				echo "<div>{$forum->getTitle(true)}</div>\n";

			if (sizeof($forum->getChildren())) {
				echo "<ul>\n";
				foreach ($forum->getChildren() as $childID)
					$this->displayAdminSidelist($childID, $currentForum);
				echo "</ul>\n";
			}
		}
	}
?>