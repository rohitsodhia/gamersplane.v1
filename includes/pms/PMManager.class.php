<?
	class PMManager {
		protected $pmID;
		protected $pm;
		protected $history = array();

		public function __construct($pmID = null) {
			global $currentUser, $mysql;

			if ($pmID !== null) {
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
			} else {
				$this->pm = new PM();
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
	}
?>