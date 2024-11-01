<?php
class ForumManager
{
	protected $currentForum;
	protected $forumsData = [];
	public $forums = [];
	protected $lastRead = [];
	protected $favouriteForumIds = [];
	protected $inGameForumIDs = [];

	const NO_CHILDREN = 1;
	const NO_NEWPOSTS = 2;
	const ADMIN_FORUMS = 4;

	const NON_FAVOURITE=1;
	const FAVOURITE=2;
	const FAV_AND_NON_FAV=3;

	public function __construct($forumID, $options = 0)
	{
		global $loggedIn, $currentUser;
		$mysql = DB::conn('mysql');

		if ($loggedIn) {
			$showPubGames = $currentUser->showPubGames;
			if ($showPubGames === null) {
				$showPubGames = 1;
				$currentUser->updateUsermeta('showPubGames', '1', true);
			}
		} else {
			$showPubGames = 1;
		}

		$this->currentForum = intval($forumID);
		if ($this->currentForum < 0) {
			header('Location: /forums/');
			exit;
		}

		$forumsR = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, cc.childCount, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f LEFT JOIN (SELECT parentID forumID, COUNT(forumID) childCount FROM forums GROUP BY (parentID)) cc ON cc.forumID = f.forumID INNER JOIN forums p ON p.forumID = {$this->currentForum} AND (".(bindec($options&$this::NO_CHILDREN) == 0?"f.heritage LIKE CONCAT(p.heritage, '%') OR ":'')."p.heritage LIKE CONCAT(f.heritage, '%')) LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID".($this->currentForum == 0 || $this->currentForum == 2?' WHERE f.heritage NOT LIKE CONCAT(LPAD(2, '.HERITAGE_PAD.', 0), "%") OR f.forumID IN (2, 10)':'')." ORDER BY LENGTH(f.heritage)");

		/*		$forumsToGet = [];
		$forumsToGetR = $mysql->query("SELECT parentID FROM forums_heritage WHERE childID = {$this->currentForum}");
		foreach ($forumsToGetR as $forumID) {
			$forumsToGet[] = $forumID;
		}
		if (bindec($options&$this::NO_CHILDREN) != 0) {
			if ($this->currentForum == 2) {
				$forumsToGet[] = 10;
			} else {
				$dontGetGames = '';
				if ($this->currentForum == 0) {
					$dontGetGames = " INNER JOIN forums_heritage g";
				}
			}
			$forumsToGetR = $mysql->query("SELECT h.childID FROM forums_heritage h WHERE h.parentID = {$this->currentForum}");
			foreach ($forumsToGetR as $forumID) {
				$forumsToGet[] = $forumID;
			}
		}
		$forumsToGet[] = $this->currentForum;
		$forumsR = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, cc.childCount, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f LEFT JOIN (SELECT parentID forumID, COUNT(forumID) childCount FROM forums GROUP BY (parentID)) cc ON cc.forumID = f.forumID LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID".($this->currentForum == 0 || $this->currentForum == 2?' WHERE f.heritage NOT LIKE CONCAT(LPAD(2, '.HERITAGE_PAD.', 0), "%") OR f.forumID IN (2, 10)':'')." ORDER BY LENGTH(f.heritage)"); */

		foreach ($forumsR as $forum) {
			$this->forumsData[$forum['forumID']] = $forum;
		}
		if ($loggedIn && in_array($forumID, [0, 2])) {
			$userGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f INNER JOIN games ON f.gameID = games.gameID INNER JOIN players ON games.gameID = players.gameID AND players.userID = {$currentUser->userID} AND players.approved = 1 LEFT JOIN (SELECT parentID forumID, COUNT(forumID) childCount FROM forums GROUP BY (parentID)) cc ON cc.forumID = f.forumID INNER JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%') LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID WHERE games.retired IS NULL ORDER BY LENGTH(f.heritage)");
			foreach ($userGameForums as $forum) {
				$this->forumsData[$forum['forumID']] = $forum;
				$this->inGameForumIDs[] = $forum['forumID'];
			}

			$userGameForums = $mysql->query("SELECT f.forumID, f.title, f.description, f.forumType, f.parentID, f.heritage, f.`order`, f.gameID, f.threadCount, t.numPosts postCount, t.lastPostID, u.userID, u.username, lp.datePosted FROM forums f INNER JOIN games ON f.gameID = games.gameID INNER JOIN games_favorites favorites ON games.gameID = favorites.gameID AND favorites.userID = {$currentUser->userID} LEFT JOIN (SELECT parentID forumID, COUNT(forumID) childCount FROM forums GROUP BY (parentID)) cc ON cc.forumID = f.forumID INNER JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%') LEFT JOIN (SELECT forumID, SUM(postCount) numPosts, MAX(lastPostID) lastPostID FROM threads GROUP BY forumID) t ON f.forumID = t.forumID LEFT JOIN posts lp ON t.lastPostID = lp.postID LEFT JOIN users u ON lp.authorID = u.userID ORDER BY LENGTH(f.heritage)");
			foreach ($userGameForums as $forum) {
				$this->forumsData[$forum['forumID']] = $forum;
				$this->favouriteForumIds[]=$forum['forumID'];
			}
		}

		$permissions = ForumPermissions::getPermissions($currentUser->userID, array_keys($this->forumsData), null, $this->forumsData);
		if($permissions){
			foreach ($permissions as $pForumID => $permission) {
				$this->forumsData[$pForumID]['permissions'] = $permission;
			}
			if (!($options & $this::NO_NEWPOSTS)) {
				$lastRead = $mysql->query("SELECT f.forumID, unread.markedRead, unread.numUnread newPosts FROM forums f LEFT JOIN (SELECT t.forumID, SUM(t.lastPostID > IFNULL(rdt.lastRead, 0) AND t.lastPostID > IFNULL(crdf.markedRead, 0)) numUnread, MAX(t.lastPostID) latestPost, crdf.markedRead FROM threads t LEFT JOIN forums_readData_threads rdt ON t.threadID = rdt.threadID AND rdt.userID = {$currentUser->userID} LEFT JOIN (SELECT f.forumID, MAX(rdf.markedRead) markedRead FROM forums f LEFT JOIN forums p ON f.heritage LIKE CONCAT(p.heritage, '%') LEFT JOIN forums_readData_forums rdf ON p.forumID = rdf.forumID AND rdf.userID = {$currentUser->userID} WHERE f.forumID IN (" . implode(',', array_keys($this->forumsData)) . ") GROUP BY f.forumID) crdf ON t.forumID = crdf.forumID GROUP BY t.forumID) unread ON f.forumID = unread.forumID WHERE f.forumID IN (" . implode(',', array_keys($this->forumsData)) . ")");
			} else {
				$lastRead = $mysql->query("SELECT f.forumID, rdf.markedRead FROM forums f LEFT JOIN forums_readData_forums rdf ON f.forumID = rdf.forumID AND rdf.userID = {$currentUser->userID} WHERE f.forumID IN (" . implode(',', array_keys($this->forumsData)) . ")");
			}
			$lastRead = $lastRead->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);
			array_walk($lastRead, function (&$value, $key) {
				$value = $value[0];
			});
			foreach ($this->forumsData as $forumID => $forumData) {
				foreach ($lastRead[$forumID] as $key => $value) {
					$this->forumsData[$forumID][$key] = $value;
				}
				$this->spawnForum($forumID);
			}
			foreach (array_keys($this->forumsData) as $forumID) {
				$this->forums[$forumID]->sortChildren();
			}
			if ($options & $this::ADMIN_FORUMS) {
				$this->pruneByPermissions(0, 'admin');
			} else {
				$this->pruneByPermissions();
			}
		}
	}

	protected function spawnForum($forumID)
	{
		if (isset($this->forums[$forumID])) {
			return null;
		}

		$this->forums[$forumID] = new Forum($forumID, $this->forumsData[$forumID]);
		if ($forumID == 0) {
			return null;
		}
		$parentID = $this->forums[$forumID]->parentID;
		if (!isset($this->forums[$parentID])) {
			$this->spawnForum($parentID);
		}
		$this->forums[$parentID]->setChild($forumID, $this->forums[$forumID]->order);
	}

	protected function pruneByPermissions($forumID = 0, $permission = 'read')
	{
		foreach ($this->forums[$forumID]->children as $childID) {
			$this->pruneByPermissions($childID, $permission);
		}
		if (sizeof($this->forums[$forumID]->children) == 0 && $this->forums[$forumID]->permissions[$permission] == false) {
			$parentID = $this->forums[$forumID]->parentID;
			unset($this->forums[$forumID]);
			if (isset($this->forums[$parentID])) {
				$this->forums[$parentID]->unsetChild($forumID);
			}
		}
	}

	public function getAccessableForums($validForums = null)
	{
		if ($validForums == null) {
			$validForums = [];
		}

		$forums = [];
		foreach ($this->forums as $forum) {
			if ($forum->getPermissions('read') && ((sizeof($validForums) && in_array($forum->getForumID(), $validForums)) || sizeof($validForums) == 0)) {
				$forums[] = $forum->getForumID();
			}
		}

		return $forums;
	}

	public function getAllChildren($forumID = 0, $read = false)
	{
		$forums = [$forumID];
		if (!isset($this->forums[$forumID])) {
			return [];
		}
		$forum = $this->forums[$forumID];
		foreach ($forum->getChildren() as $childID) {
			if (!in_array($childID, $forums) && (!$read || ($read && $this->forums[$childID]->getPermissions('read')))) {
				$forums[] = $childID;
			}
			$children = $this->getAllChildren($childID);
			$forums = array_merge($forums, $children);
		}

		return $forums;
	}

	public function getForumProperty($forumID, $property)
	{
		if (preg_match('/(\w+)\[(\w+)\]/', $property, $matches)) {
			return $this->forums[$forumID]->{$matches[1]}[$matches[2]];
		} elseif (preg_match('/(\w+)->(\w+)/', $property, $matches)) {
			return $this->forums[$forumID]->$matches[1]->$matches[2];
		} else {
			return $this->forums[$forumID]->$property;
		}
	}

	public function displayCheck($forumID = null)
	{
		if ($forumID == null) {
			$forumID = $this->currentForum;
		}

		if ((is_array($this->forums[$forumID]->children) && sizeof($this->forums[$forumID]->children)) || $this->forums[$forumID]->permissions['read']) {
			return true;
		} else {
			return false;
		}
	}

	public function hasForums($favFilter){
		$childForums=$this->forums[$this->currentForum]->children;

		if($favFilter==ForumManager::FAVOURITE){
			$childForums=array_filter($childForums, function($v,$k) {
				return in_array($v,$this->favouriteForumIds);
			}, ARRAY_FILTER_USE_BOTH);
		} else if($favFilter==ForumManager::NON_FAVOURITE){
			$childForums=array_filter($childForums, function($v,$k) {
				return !in_array($v,$this->favouriteForumIds);
			}, ARRAY_FILTER_USE_BOTH);
		}

		return sizeof($childForums)!=0;
	}

	private function sortGameForums(&$forums){
		usort($forums, function($a, $b) {
			if($a==10){  //games tavern
				return -1;
			}
			if($b==10){  //games tavern
				return 1;
			}
			$aFav=$this->isFavGame($a);
			$bFav=$this->isFavGame($b);
			if($aFav!=$bFav){
				return $aFav>$bFav?1:-1;
			}
			$atitle=trim(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding( $this->forums[$a]->title, "UTF-8" ) ) ));
			$btitle=trim(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', mb_convert_encoding( $this->forums[$b]->title, "UTF-8" ) ) ));
			return $atitle > $btitle ? 1 : ($atitle < $btitle ? -1 :0);
		});
	}

	public function displayForum($favFilter)
	{
		global $loggedIn, $currentUser;

		$childForums=$this->forums[$this->currentForum]->children;

		if($favFilter==ForumManager::FAVOURITE){
			$childForums=array_filter($childForums, function($v,$k) {
				return in_array($v,$this->favouriteForumIds);
			}, ARRAY_FILTER_USE_BOTH);
		} else if($favFilter==ForumManager::NON_FAVOURITE){
			$childForums=array_filter($childForums, function($v,$k) {
				return !in_array($v,$this->favouriteForumIds);
			}, ARRAY_FILTER_USE_BOTH);
		}

		if (sizeof($childForums) == 0) {
			return false;
		}

		if($favFilter==ForumManager::FAVOURITE || $favFilter==ForumManager::NON_FAVOURITE || $this->currentForum==2){
			$this->sortGameForums($childForums);
		}


		$tableOpen = false;
		$lastType = 'f';
		foreach ($childForums as $childID) {
			if ($tableOpen && ($lastType == 'c' || $this->forums[$childID]->forumType == 'c')) {
				$tableOpen = false;
				echo "\t\t\t</div>\n\t\t</div>\n";
			}
			if (!$tableOpen) {
				?>
<div class="tableDiv">
	<?if($this->forums[$childID]->forumType == 'c'){?>
		<div class="groupTopper groupTopperLeft"><h2 class="trapezoid redTrapezoid"><?$this->addForumIcon($childID)?><?=$this->forums[$childID]->title?></h2></div>
	<?}else{?>
		<div class="groupTopper"><h2 class="trapezoid redTrapezoid"> Subforums</h2></div>
	<?}?>
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
		if ($this->forums[$childID]->forumType == 'f') {
			$this->displayForumRow($childID);
		} elseif (is_array($this->forums[$childID]->children)) {
			$rolledUpChildren=$this->forums[$childID]->children;
			if($childID==2){ //games forums
				$this->sortGameForums($rolledUpChildren);
			}

			foreach ($rolledUpChildren as $cChildID) {
				$this->displayForumRow($cChildID);
			}
		}
		$lastType = $this->forums[$childID]->forumType;
	}
	echo "\t\t\t</div>\n\t\t</div>\n";
}

public function displayForumRow($forumID)
{
	$forum = $this->forums[$forumID];
	?>
        <div class="tr<?= $this->newPosts($forumID) ? '' : ' noPosts' ?><?= ' fid'.$forumID?><?= ($this->isFavGame($forumID)?' favGame':'')?>">
            <div class="td icon">
				<a href="/forums/<?= $forum->forumID ?>/"><div class="forumIcon<?= $this->newPosts($forumID) ? ' newPosts' : '' ?>" title="<?= $this->newPosts($forumID) ? 'New' : 'No new' ?> posts in forum" alt="<?= $this->newPosts($forumID) ? 'New' : 'No new' ?> posts in forum"></div></a>
            </div>
            <div class="td name">
                <a href="/forums/<?= $forum->forumID ?>/"><?= printReady($forum->title) ?></a>
                <?= ($forum->description != '') ? "\t\t\t\t\t\t<div class=\"description\">" . printReady($forum->description) . "</div>\n" : '' ?>
            </div>
            <div class="td numThreads"><?= $this->getTotalThreadCount($forumID) ?></div>
            <div class="td numPosts"><?= $this->getTotalPostCount($forumID) ?></div>
            <div class="td lastPost">
                <?
				$lastPost = $this->getLastPost($forumID);
				if ($lastPost) {
					echo "\t\t\t\t\t\t<a href=\"/user/{$lastPost->userID}/\" class=\"username\">{$lastPost->username}</a><br><span class=\"convertTZ\">" . date('M j, Y g:i a', strtotime($lastPost->datePosted)) . "</span>\n";
				} else {
					echo "\t\t\t\t\t\t</span>No Posts Yet!</span>\n";
				}
				?>
            </div>
        </div>
        <?
	}

	public function getTotalThreadCount($forumID)
	{
		$forum = $this->forums[$forumID];

		$total = 0;
		if (sizeof($forum->children)) {
			foreach ($forum->children as $cForumID) {
				$total += $this->getTotalThreadCount($cForumID);
			}
		}
		if ($forum->permissions['read']) {
			$total += $forum->threadCount;
		}

		return $total;
	}

	public function getTotalPostCount($forumID)
	{
		$forum = $this->forums[$forumID];

		$total = 0;
		if (sizeof($forum->children)) {
			foreach ($forum->children as $cForumID) {
				$total += $this->getTotalPostCount($cForumID);
			}
		}
		if ($forum->permissions['read']) {
			$total += $forum->postCount;
		}

		return $total;
	}

	public function maxRead($forumID)
	{
		$maxRead = 0;
		foreach ($this->forums[$forumID]->getHeritage() as $heritageID) {
			if ($this->forums[$heritageID]->getMarkedRead() > $maxRead) {
				$maxRead = $this->forums[$heritageID]->getMarkedRead();
			}
		}

		return $maxRead;
	}

	public function newPosts($forumID)
	{
		global $loggedIn;
		if (!$loggedIn) {
			return false;
		}

		$forum = $this->forums[$forumID];

		if (sizeof($forum->children)) {
			foreach ($forum->children as $childID) {
				if ($this->newPosts($childID)) {
					return true;
				}
			}
		}
		if ($forum->newPosts) {
			return true;
		} else {
			return false;
		}
	}

	public function getLastPost($forumID)
	{
		$forum = $this->forums[$forumID];

		$lastPost = new stdClass();
		$lastPost->postID = 0;
		if (sizeof($forum->children)) {
			foreach ($forum->children as $cForumID) {
				$cLastPost = $this->getLastPost($cForumID);
				if ($cLastPost && $cLastPost->postID > $lastPost->postID) {
					$lastPost = $cLastPost;
				}
			}
		}
		if ($forum->permissions['read'] && $forum->lastPost->postID > $lastPost->postID) {
			return $forum->lastPost;
		} elseif ($lastPost->postID != 0) {
			return $lastPost;
		} else {
			return null;
		}
	}

	public function displayBreadcrumbs()
	{
		?>
        <div id="breadcrumbs">
            <?
			$this->displayForumBreadcrumbs();
			?>
        </div>
        <?
	}

	public function displayForumBreadcrumbs()
	{
		if ($this->currentForum != 0) {
			$heritage = $this->forums[$this->currentForum]->heritage;
			$fCounter = 0;
			foreach ($heritage as $hForumID) {
				echo "\t\t\t\t\t<a href=\"/forums/{$hForumID}\">" . printReady($this->forums[$hForumID]->title) . "</a>" . ($fCounter != sizeof($heritage) - 1 ? ' > ' : '') . "\n";
				$fCounter++;
			}
		}
	}

	public function getThreads($page = 1)
	{
		$this->forums[$this->currentForum]->getThreads($page);
	}

	public function displayThreads()
	{
		$mysql = DB::conn('mysql');
		$forum = $this->forums[$this->currentForum];
		if (!$forum->permissions['read']) {
			return false;
		}

		?>
        <div class="tableDiv threadTable">
            <? if ($forum->permissions['createThread']) { ?>
            <div class="hbdMargined"><a href="/forums/newThread/<?= $forum->forumID ?>/" class="fancyButton">New Thread</a></div>
            <?
		} ?>
            <div class="tr headerTR headerbar hbDark">
                <div class="td icon">&nbsp;</div>
                <div class="td threadInfo">Thread</div>
                <div class="td numPosts"># of Posts</div>
                <div class="td lastPost">Last Post</div>
            </div>
            <div class="sudoTable threadList">
                <?
				if (sizeof($forum->threads)) {
					foreach ($forum->threads as $thread) {
						$maxRead = $this->maxRead($forum->getForumID());
						?>
                <div class="tr">
                    <div class="td icon">
						<a href="/forums/thread/<?= $thread->threadID ?>/?view=newPost#newPost"><div class="forumIcon<?= $thread->getStates('sticky') ? ' sticky' : '' ?><?= $thread->getStates('locked') ? ' locked' : '' ?><?= $thread->getStates('publicPosting') ? ' publicPosting' : '' ?><?= $thread->newPosts($maxRead) ? ' newPosts' : '' ?>" title="<?= $thread->newPosts($maxRead) ? 'New' : 'No new' ?> posts in thread" alt="<?= $thread->newPosts($maxRead) ? 'New' : 'No new' ?> posts in thread"></div></a>
                    </div>
                    <div class="td threadInfo">
                        <? if ($thread->newPosts($maxRead)) { ?>
                        <a class="threadInfoNew" href="/forums/thread/<?= $thread->threadID ?>/?view=newPost#newPost"><img src="/images/forums/newPost.png" title="View new posts" alt="View new posts"></a>
                        <?
					} else {?><span class="threadInfoNew"></span><?} ?>
                        <div class="paginateDiv">
                            <?
							if ($thread->postCount > PAGINATE_PER_PAGE) {
								$url = '/forums/thread/' . $thread->threadID . '/';
								$numPages = ceil($thread->postCount / PAGINATE_PER_PAGE);
								if ($numPages <= 4) {
									for ($count = 1; $count <= $numPages; $count++) {
										?>
                            <a href="<?= $url ?>?page=<?= $count ?>"><?= $count ?></a>
                            <?
						}
					} else {
						?>
                            <a href="<?= $url ?>?page=1">1</a>
                            <div>...</div>
                            <? for ($count = ($numPages - 1); $count <= $numPages; $count++) { ?>
                            <a href="<?= $url ?>?page=<?= $count ?>"><?= $count ?></a>
                            <?
						}
					}
				}
				$nonMobBadge = '';
				$mobBadge = '';
				if ($forum->forumID == 10) {
					$getGameInfo = $mysql->query("SELECT status, `system`, customSystem FROM games WHERE recruitmentThreadId = {$thread->threadID} LIMIT 1");
					if ($getGameInfo->rowCount()) {
						$gameInfo = $getGameInfo->fetch();
						$systems = Systems::getInstance();
						$nonMobBadge = "<span class=\"mob-hide badge badge-game" . ($gameInfo['status'] == 'open' ? "Open" : "Closed") . '">' . ($gameInfo["customType"] ? $gameInfo["customType"] : $systems->getFullName($gameInfo['system'])) . "</span>";
						$mobBadge = "<span class=\"non-mob-hide badge badge-game" . ($gameInfo['status'] == 'open' ? "Open" : "Closed") . '">' . ($gameInfo["customType"] ? $gameInfo["customType"] : $systems->getFullName($gameInfo['system'])) . "</span>";
					}
				}
			?>
                            <a href="/forums/thread/<?= $thread->threadID ?>/?view=lastPost#lastPost"><img src="/images/downArrow.png" title="Last post" alt="Last post"></a>
                        </div>
                        <a href="/forums/thread/<?= $thread->threadID ?>/" class="threadTitle"><?= $thread->title ?></a>
                        <span class="threadAuthor"><?=$nonMobBadge?>
							by <a href="/user/<?= $thread->authorID ?>/" class="username"><?= $thread->authorUsername ?></a> on <span class="convertTZ"><?= date('M j, Y g:i a', strtotime($thread->datePosted)) ?></span>
						<span>
                    </div>
                    <div class="td numPosts"><?= $thread->postCount ?></div>
                    <div class="td lastPost">
						<?=$mobBadge?><a href="/user/<?= $thread->lastPost->authorID ?>" class="username"><?= $thread->lastPost->username ?></a>
						<span class="convertTZ"><?= date('M j, Y g:i a', strtotime($thread->lastPost->datePosted)) ?></span>
                    </div>
                </div>
                <?
			}
		} else {
			echo "\t\t\t\t<div class=\"tr noThreads\">No threads yet</div>\n";
		}
		echo "			</div>
		</div>\n";
	}

	public function getAdminForums($forumID = 0, $currentForum = 0)
	{
		if (!isset($this->forums[$forumID])) {
			return null;
		}

		$forum = $this->forums[$forumID];
		$details = [
			'forumID' => (int)$forumID,
			'title' => $forum->getTitle(true),
			'admin' => true,
			'children' => []
		];
		if (!$forum->getPermissions('admin')) {
			$details['admin'] = false;
		}
		if (sizeof($forum->getChildren())) {
			foreach ($forum->getChildren() as $childID) {
				if ($child = $this->getAdminForums($childID, $currentForum)) {
					$details['children'][] = $child;
				}
			}
		} elseif (!$details['admin']) {
			return null;
		}

		return $details;
	}

	public function addForumIcon($forumID=null){
		$forumID=$forumID?$forumID:$this->currentForum;
		echo "<i class='ra forum-icon forum-root-".$this->forums[$forumID]->rootHeritage()." forum-id-".$forumID."'></i> ";
	}

	public function isFavGame($forumID){
		return in_array($forumID, $this->favouriteForumIds) && !in_array($forumID, $this->inGameForumIDs);
	}
}
?>
