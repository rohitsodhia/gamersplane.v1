<?
	class Systems {
		private static $instance;
		protected $systems = array();

		private function __construct() {
			global $mongo;

			$systems = $mongo->systems->find(array(), array('name'))->sort(array('sortName' => 1));
			foreach ($systems as $system) 
				$this->systems[$system['_id']] = $system['name'];
		}

		public static function getInstance() {
			if (empty(self::$instance)) self::$instance = new Systems();
			return self::$instance;
		}

		public function getAllSystems($ignoreCustom = false) {
			$systems = $this->systems;
			if ($ignoreCustom) 
				unset($systems['custom']);
			return $systems;
		}

		public function getRandomSystems($num) {
			$randSystemSlugs = array();
			$randSystems = array();
			$systemSlugs = shuffle(array_keys($this->systems));
			for ($count = 0; $count < $num; $count++) 
				$randSystems[] = array_shift($systemSlugs);
			foreach ($this->systems as $slug => $name) 
				if (in_array($slug, $randSystemSlugs)) 
					$randSystems[$slug] = $name;
			return $randSystems;
		}

		public function verifySystem($slug) {
			return array_key_exists($slug, $this->systems)?true:false;
		}

		public function getFullName($slug, $debug = false) {
			if (array_key_exists($slug, $this->systems)) 
				return $this->systems[$slug];
			else 
				return null;
		}
	}
?>