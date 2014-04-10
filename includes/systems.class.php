<?
	class Systems {
		private static $instance;
		protected $shortNames = array();
		protected $fullNames = array();
		protected $classes = array();

		private function __construct() {
			global $mysql;

			$systems = $mysql->query('SELECT systemID, shortName, fullName, class FROM systems WHERE enabled = 1 ORDER BY fullName');
			foreach ($systems as $system) {
				$this->shortNames[$system['systemID']] = $system['shortName'];
				$this->fullNames[$system['systemID']] = $system['fullName'];
				$this->classes[$system['systemID']] = $system['class'];
			}
		}

		public static function getInstance() {
			if (empty(self::$instance)) self::$instance = new Systems();
			return self::$instance;
		}

		public function getAllSystems($ignoreCustom = FALSE) {
			$systems = array();
			foreach ($this->shortNames as $systemID => $shortName) $systems[$systemID] = array('shortName' => $shortName, 'fullName' => $this->fullNames[$systemID]);
			if ($ignoreCustom) unset($systems[1]);
			return $systems;
		}

		public function getRandomSystems($num) {
			$systems = $this->getAllSystems();
			$systemIDs = array_keys($systems);
			$randSystemIDs = array();
			for ($count = 0; $count < $num; $count++) {
				$newID = $systemIDs[mt_rand(0, count($systemIDs) - 1)];
				if (!in_array($newID, $randSystemIDs)) $randSystemIDs[] = $newID;
				else $count -= 1;
			}
			$randSystems = array();
			foreach (array_keys($this->shortNames) as $systemID) {
				if (in_array($systemID, $randSystemIDs)) $randSystems[$systemID] = $systems[$systemID];
			}
			return $randSystems;
		}

		public function getSystemInfo($systemID) {
			if (array_search($systemID, array_keys($this->shortNames))) return array('systemID' => $systemID, 'shortName' => $this->shortNames[$systemID], 'fullName' => $this->fullNames[$systemID]);
		}

		public function getSystemID($shortName) {
			return array_search($shortName, $this->shortNames);
		}

		public function getShortName($systemID) {
			return isset($this->shortNames[$systemID])?$this->shortNames[$systemID]:FALSE;
		}

		public function getFullName($shortName) {
			$systemID = $this->getSystemID($shortName);
			if ($systemID) return $this->fullNames[$systemID];
			else return FALSE;
		}

		public function getClass($systemID) {
			return isset($this->classes[$systemID])?$this->classes[$systemID]:FALSE;
		}
	}
?>