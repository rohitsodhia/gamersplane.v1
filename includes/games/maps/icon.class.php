<?
	class Icon {
		private $iconID;
		private $mapID;
		private $label;
		private $name;
		private $description;
		private $color;
		private $textColor = 'FFF';
		private $location;

		private $mysql;
		
		public function __construct($iconID = NULL) {
			global $mysql;
			$this->mysql = $mysql;
			$iconID = intval($iconID)?intval($iconID):NULL;
			if ($iconID) {
				$this->iconID = $iconID;
				$iconInfo = $this->mysql->query('SELECT mapID, label, name, description, color, location FROM maps_icons WHERE iconID = '.$iconID);
				$iconInfo = $iconInfo->fetch();
				$this->mapID = $iconInfo['mapID'];
				$this->label = $iconInfo['label'];
				$this->name = $iconInfo['name'];
				$this->description = $iconInfo['description'];
				$this->color = $iconInfo['color'];
				$this->location = $iconInfo['location'];
				$this->determineTextColor();
			}
			$result = $this->mysql->query('SELECT userID FROM users WHERE userID = 1');
		}

		public function __toString() {
			if ($this->iconID) return "<div id=\"icon_{$this->iconID}\" class=\"mapIcon\" title=\"{$this->name}\" style=\"background-color: #{$this->color}; color: #{$this->textColor};\">{$this->label}</div>\n";
			else return 'No icon';
		}

		public function updateAttr($attr, $value) {
			$this->$attr = $value;
			if ($attr == 'color') $this->determineTextColor();
		}

		public function getAttr($attr) {
			return $this->$attr;
		}

		public function saveIcon() {
			if (!isset($this->mapID) || !isset($this->label) || (strlen($this->label) != 1 && strlen($this->label) != 2) || !isset($this->name) || strlen($this->name) == 0 || !isset($this->color) || strlen($this->color) == 0) return FALSE;
			if (isset($this->iconID) && $this->iconID != 0) {
				$addIcon = $this->mysql->prepare("UPDATE maps_icons SET label = :label, name = :name, color = :color WHERE iconID = {$this->iconID}");
				$addIcon->execute(array(':label' => $this->label, ':name' => $this->name, ':color' => $this->color));
				$this->addHistory('edited');
				return $this->displayHistory(array('label' => $this->label, 'name' => $this->name, 'enactedBy' => intval($_SESSION['userID']), 'username' => $_SESSION['username'], 'action' => 'edited'));
			} else {
				$addIcon = $this->mysql->prepare("INSERT INTO maps_icons (mapID, label, name, color) VALUES ({$this->mapID}, :label, :name, :color)");
				$addIcon->execute(array(':label' => $this->label, ':name' => $this->name, ':color' => $this->color));
				$this->iconID = $this->mysql->lastInsertId();
				$this->addHistory('created');
			}
		}

		public function saveLocation($location) {
			$histInfo = array('label' => $this->label, 'name' => $this->name, 'enactedBy' => intval($_SESSION['userID']), 'enactedOn' => '', 'username' => $_SESSION['username'], 'action' => 'moved', 'origin' => $this->location, 'destination' => '');
			$location = preg_match('/^[0-9]{1,2}_[0-9]{1,2}$/', $_POST['location'])?$_POST['location']:'';
			$updateLocation = $this->mysql->prepare("UPDATE maps_icons SET location = :location WHERE iconID = {$this->iconID}");
			$updateLocation->execute(array(':location' => $location));
			$this->addHistory('moved', $this->location, $location);
			$historyID = $this->mysql->lastInsertId();
			$histInfo['enactedOn'] = $this->mysql->query("SELECT enactedOn FROM maps_iconHistory WHERE actionID = $historyID");
			$histInfo['enactedOn'] = $histInfo['enactedOn']->fetchColumn();
			$this->location = $location;
			$histInfo['destination'] = $location;

			return $this->displayHistory($histInfo);
		}

		private function determineTextColor() {
			$total = 0;
			for ($count = 0; $count < 6; $count += 2) {
				$total = hexdec(substr($this->color, $count, 2));
			}
			if ($total / 3 > 200) $this->textColor = '000';
			else $this->textColor = 'FFF';
		}

		public static function displayHistory($info) {
			if (!is_array($info)) {
				$info = intval($info);
				$info = $this->mysql->query("SELECT ih.iconID, i.label, i.name, i.mapID, ih.enactedBy, ih.enactedOn, u.username, ih.action, ih.origin, ih.destination FROM maps_iconHistory ih, maps_icons i, users u WHERE ih.iconID = i.iconID AND ih.enactedBy = u.userID AND ih.actionID = $info");
				$info = $info->fetch();
			}
			if ($info['action'] == 'moved') {
				$locParts = explode('_', $info['origin']);
				$info['origin'] = decToB26($locParts[0]).$locParts[1];
				$locParts = explode('_', $info['destination']);
				$info['destination'] = decToB26($locParts[0]).$locParts[1];

				$historyStr = "<a href=\"".SITEROOT."/ucp/{$info['enactedBy']}\">{$info['username']}</a> moved <strong>{$info['name']}</strong> ({$info['label']}) from ".(strlen($info['origin'])?strtoupper($info['origin']):'Box')." to ".(strlen($info['destination'])?strtoupper($info['destination']):'Box');
			} elseif ($info['action'] == 'created') $historyStr = "<a href=\"".SITEROOT."/ucp/{$info['enactedBy']}\">{$info['username']}</a> created <strong>{$info['name']}</strong> ({$info['label']})";
			elseif ($info['action'] == 'edited') $historyStr = "<a href=\"".SITEROOT."/ucp/{$info['enactedBy']}\">{$info['username']}</a> edited <strong>{$info['name']}</strong> ({$info['label']})";
			elseif ($info['action'] == 'deleted') $historyStr = "<a href=\"".SITEROOT."/ucp/{$info['enactedBy']}\">{$info['username']}</a> deleted <strong>{$info['name']}</strong> ({$info['label']})";
			$dateStr = date('m/d/y H:i:s', strtotime($info['enactedOn']));
			return <<<RTNSTR
<div class="historyItem">
	<p class="timestamp">$dateStr</p>
	<p class="historyStr">$historyStr</p>
</div>
RTNSTR;
		}

		private function addHistory($action, /*$enactedOn = 'NOW()', */$origin = NULL, $destination = NULL) {
			$userID = intval($_SESSION['userID']);
//			$enactedOn = $enactedOn == 'NOW()'?'NOW()':"'$enactedOn'"
			if (!isset($this->iconID) || intval($this->iconID) == 0) return FALSE;
			$addHistory = $this->mysql->prepare("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action, origin, destination) VALUES ({$this->iconID}, {$this->mapID}, $userID, NOW(), :action, :origin, :destination)");
			$addHistory->execute(array(':action' => $action, ':origin' => $origin, ':destination' => $destination));

			return $this->mysql->lastInsertId();
		}
	}
?>