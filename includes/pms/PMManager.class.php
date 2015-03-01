<?
	class PMManager {
		protected $pmID;
		protected $pm;
		protected $history = array();

		public function __construct($pmID) {
			global $currentUser, $mysql;

			$this->pmID = intval($pmID);
			$getPM = $mysql->prepare("SELECT pms.pmID, pms.senderID, pms.recipientIDs, pms.title, pms.message, pms.datestamp, pms.replyTo, c.`read` FROM pms INNER JOIN pms_inBox c ON pms.pmID = c.pmID AND c.userID = {$currentUser->userID} WHERE pms.pmID = :pm LIMIT 1");
			$getPM->execute(array(':pm' => $this->pmID));
			if ($getPM->rowCount() == 0) throw new Exception('No PM');
			$pm = $getPM->fetch();
			$userIDs = array_merge(array($pm['senderID']), explode(',', $pm['recipientIDs']));
			$users = $mysql->query("SELECT userID, username FROM users WHERE userID in (".implode(',', $userIDs).")")->fetchAll(PDO::FETCH_GROUP);
			array_walk($users, function (&$value, $key) { $value = array_merge(array('userID' => $key), $value[0]); });

			$pm['sender'] = (object) $users[$pm['senderID']];
			$pm['recipients'] = array();
			foreach (explode(',', $pm['recipientIDs']) as $recipientID) 
				$pm['recipients'][] = (object) $users[$recipientID];
			$this->pm = new PM($pmID, $pm);

			if ($this->pm->getReplyTo()) {
				$parentID = $this->pm->getReplyTo();
				$hUserIDs = array();
				for ($count = 0; $count < 10; $count++) {
					$getPM->execute(array(':pm' => $parentID));
					if ($getPM->rowCount() == 0) break;
					$pm = $getPM->fetch();
					$this->history[$pm['pmID']] = $pm;
					$hUserIDs = array_merge($hUserIDs, array($pm['senderID']), explode(',', $pm['recipientIDs']));
					$parentID = $pm['replyTo'];
				}
				$hUserIDs = array_unique($hUserIDs);
				foreach (array_keys($users) as $userID) {
					$key = array_search($userID, $hUserIDs);
					if ($key !== false) unset($hUserIDs[$key]);
				}

				if (sizeof($hUserIDs)) {
					$hUsers = $mysql->query("SELECT userID, username FROM users WHERE userID in (".implode(',', $hUserIDs).")")->fetchAll(PDO::FETCH_GROUP);
					array_walk($hUsers, function (&$value, $key) { $value = array_merge(array('userID' => $key), $value[0]); });
					foreach ($hUsers as $user) 
						$users[$user['userID']] = $user;
					foreach ($this->history as $pmID => $pm) {
						$pm['sender'] = (object) $users[$pm['senderID']];
						$pm['recipients'] = array();
						foreach (explode(',', $pm['recipientIDs']) as $recipientID) 
							$pm['recipients'][] = (object) $users[$recipientID];
						$this->history[$pmID] = new PM($pmID, $pm);
					}
				}
			}
		}

		public function __get($key) {
			if ($key == 'pm' || $key == 'history') return $this->$key;
		}

		public function getPMID() {
			return $this->pmID;
		}

		public function getUnread() {
			return $this->unread;
		}

		public function displayPMs($page) {
			$page = intval($page) > 0?intval($page):1;

			if (sizeof($this->pms) > ($page - 1) * PAGINATE_PER_PAGE) {
				for ($count = ($page - 1) * PAGINATE_PER_PAGE; $count < $page * PAGINATE_PER_PAGE; $count++) {
					if (!isset($this->pms[$count])) break;
					$pm = $this->pms[$count];
?>
			<div id="pm_<?=$pm->getPMID()?>" class="pm tr<?=$count == $page * PAGINATE_PER_PAGE || !isset($this->pms[$count + 1])?' lastTR':''?><?=$pm->getRead()?'':' new'?>">
				<div class="delCol"><a href="/pms/delete/<?=$pm->getPMID()?>/" class="deletePM sprite cross"></a></div>
				<div class="info">
					<div class="title"><a href="/pms/view/<?=$pm->getPMID()?>/"><?=(!$pm->getRead()?'<b>':'').$pm->getTitle(true).(!$pm->getRead()?'</b>':'')?></a></div>
					<div class="details">
<?					if ($this->box == 'inbox') { ?>
						from <a href="/user/<?=$pm->getSender('userID')?>/" class="username"><?=$pm->getSender('username')?></a> on <span class="convertTZ" data-parse-format="MMMM D, YYYY" data-display-format="MMMM D, YYYY h:mm a"><?=$pm->getDatestamp('F j, Y g:i a')?></span>
<?
					} else {
						$recipients = array();
						foreach ($pm->getRecipients() as $recipient) 
							$recipients[] = "<a href=\"/user/{$recipient->userID}/\" class=\"username\">{$recipient->username}</a>";
?>
						to <?=implode(', ', $recipients)?> on <span class="convertTZ" data-parse-format="MMMM D, YYYY" data-display-format="MMMM D, YYYY h:mm a"><?=$pm->getDatestamp('F j, Y g:i a')?></span>
<?					} ?>
					</div>
				</div>
			</div>
<?
				}
			} else {
?>
			<div id="noPMs">Doesn't seem like <?=$this->box == 'inbox'?'anyone has contacted you':'you have contacted anyone'?> yet...</div>
<?
			}
		}

		public function displayForum() {
			global $loggedIn, $currentUser;

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
			<div class="clearfix">
<?					if ($loggedIn && $childID == 2) { ?>
				<div class="pubGameToggle hbdMargined">
					<span>Show public games: </span>
					<a href="/forums/process/togglePubGames/" class="ofToggle disable<?=$currentUser->showPubGames?' on':''?>"></a>
				</div>
<?					} ?>
				<h2 class="wingDiv redWing">
					<div><?=$this->forums[$childID]->forumType == 'c'?$this->forums[$childID]->title:'Subforums'?></div>
					<div class="wing dlWing"></div>
					<div class="wing drWing"></div>
				</h2>
			</div>
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