<?
	class Icon {
		public $iconID;
		public $mapID;
		public $label;
		public $name;
		public $description;
		public $color;
		public $location;

		private $mysql;
		
		public function __construct($iconID = NULL) {
			global $mysql;
			$this->mysql = $mysql;
			$result = $this->mysql->query('SELECT userID FROM users WHERE userID = 1');
		}

		public function __toString() {
			if ($this->iconID) return "<div id=\"icon_{$this->iconID}\" class=\"mapIcon {$this->color}Icon\" title=\"{$this->name}\">{$this->label}</div>";
			else return 'No icon';
		}

		public function updateAttr($attr, $value) {
			$this->$attr = $value;
		}

		public function getAttr($attr) {
			return $this->$attr;
		}

		public function saveIcon() {
			if (!isset($this->mapID) || !isset($this->label) || (strlen($this->label) != 1 && strlen($this->label) != 2) || !isset($this->name) || strlen($this->name) == 0 || !isset($this->color) || strlen($this->color) == 0) return FALSE;
			if (isset($this->iconID) && $this->iconID != 0) {
			} else {
				$addIcon = $this->mysql->prepare("INSERT INTO maps_icons (mapID, label, name, color) VALUES ({$this->mapID}, :label, :name, :color)");
				$addIcon->execute(array(':label' => $this->label, ':name' => $this->name, ':color' => $this->color));
				$this->iconID = $this->mysql->lastInsertId();
				$this->addHistory('created');
			}
		}

		private function addHistory($action) {
			$userID = intval($_SESSION['userID']);
			if (!isset($this->iconID) || intval($this->iconID) == 0) return FALSE;
			$addHistory = $this->mysql->prepare("INSERT INTO maps_iconHistory (iconID, mapID, enactedBy, enactedOn, action) VALUES ({$this->iconID}, {$this->mapID}, $userID, NOW(), :action)");
			$addHistory->execute(array(':action' => $action));
		}
	}
?>