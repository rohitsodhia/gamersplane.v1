<?
	class PMBoxManager {
		protected $box;
		protected $pms = array();
		protected $unread = 0;

		public function __construct($box) {
			global $currentUser, $mysql;

			if ($box == 'inbox' || $box == 'outbox') 
				$this->box = $box;
			else 
				return false;

			$pms = $mysql->query("SELECT pms.pmID, pms.senderID, pms.recipientIDs, pms.title, pms.datestamp, c.`read` FROM pms INNER JOIN pms_inBox c ON pms.pmID = c.pmID AND c.userID = {$currentUser->userID} WHERE pms.senderID ".($this->box == 'inbox'?'!':'')."= {$currentUser->userID} ORDER BY datestamp DESC")->fetchAll(PDO::FETCH_GROUP);
			array_walk($pms, function (&$value, $key) { $value = array_merge(array('pmID' => $key), $value[0]); });
			$userIDs = array();
			foreach ($pms as $pmID => $pm) {
				$userIDs[] = $pm['senderID'];
				$pms[$pmID]['recipientIDs'] = explode(',', $pm['recipientIDs']);
				$userIDs = array_merge($userIDs, explode(',', $pm['recipientIDs']));
				if ($pm['read']) $this->unread++;
			}
			$userIDs = array_unique($userIDs);
			$users = $mysql->query("SELECT userID, username FROM users WHERE userID in (".implode(',', $userIDs).")")->fetchAll(PDO::FETCH_GROUP);
			array_walk($users, function (&$value, $key) { $value = array_merge(array('userID' => $key), $value[0]); });

			foreach ($pms as $pmID => $pm) {
				$pm['sender'] = (object) $users[$pm['senderID']];
				$pm['recipients'] = array();
				foreach ($pm['recipientIDs'] as $recipientID) 
					$pm['recipients'][] = (object) $users[$recipientID];
				$this->pms[] = new PM($pmID, $pm);
			}
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
	}
?>