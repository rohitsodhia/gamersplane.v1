<?
	class spycraft2_character {
		private $userID ;
		private $charID;
		private $name;
		private $classes;
		private $stats = array('str' => 0, 'dex' => 0, 'con' => 0, 'int' => 0, 'wis' => 0, 'cha' => 0);
		private $ac = array('class' => 0, 'armor' => 0, 'dex' => 0, 'misc' => 0);
		private $damage = array('vitality' => 0, 'wounds' => 0, 'subdual' => 'stress' => 0);
		private $saves = array ('fort' => array('base' => 0, 'misc' => 0),
								'ref' => array('base' => 0, 'misc' => 0),
								'will' => array('base' => 0, 'misc' => 0));
		private $initiative = array('class' => 0, 'misc' => 0);
		private $attackBonus = array('base' => 0, 'misc' => array('melee' => 0, 'ranged' => 0));
		private $actionDice = array('total' => 0, 'type' => '');
		private $checks = array('knowledge' => 0, 'request' => 0, 'gear' => 0);
		private $skills = array();
		private $weapons = array();
		private $items = array();
		private $experience = 0;
		private $gold = 0;
		private $abilities = array();
		private $feats = array();
		private $languages = array();
		
		public function __construct($userID) {
			$this->userID = $userID;
		}
		
		public function setCharID($charID) {
			$this->charID = $charID;
			
			return TRUE;
		}
		
		public function setName($name) {
			$this->name = $name;
			
			return TRUE;
		}
		
		public function addClass($class, $level) {
			array_push($this->classes, $class);
			array_push($this->levels, $level);
			
			return TRUE;
		}
		
		public function getClass($which = -1) {
			if ($which == -1) {
				$allClasses = array();
				for ($count = 0; $count < sizeof($this->classes); $count++) {
					$allClasses[$this->classes[$count]] = $this->levels[$count];
				}
				
				return $allClasses;
			} else {
				return array($this->classes[$which], $this->levels[$which]);
			}
		}
		
		public function changeLevel($class, $level) {
			$key = array_search($class, $this->classes);
			$this->levels[$key] = $level;
		}
		
		public function setAlignment($alignment) {
			$this->alignment = $alignment;
		}
		
		public function getAlignment($full = FALSE) {
			if ($full) {
				$sAlignment = $this->alignment;
				if ($sAlignment = "TN") { $fAlignment = "True Neutral"; }
				else {
					if ($sAlignment[0] == "L") { $fAlignment = "Lawful "; }
					elseif ($sAlignment[0] == "N") { $fAlignment = "Neutral "; }
					elseif ($sAlignment[0] == "C") { $fAlignment = "Chaotic "; }
					
					if ($sAlignment[1] == "G") { $fAlignment .= "Good"; }
					elseif ($sAlignment[1] == "N") { $fAlignment .= "Neutral"; }
					elseif ($sAlignment[1] == "E") { $fAlignment .= "Evil"; }
				}
				
				return $fAlignment;
			} else { return $this->alignment; }
		}
		
		public function setStats() {
			if (func_num_args() == 6) {
				for ($count = 0; $count < 6; $count++) {
					$this->stats["Str"] = func_get_arg(0);
					$this->stats["Dex"] = func_get_arg(1);
					$this->stats["Con"] = func_get_arg(2);
					$this->stats["Int"] = func_get_arg(3);
					$this->stats["Wis"] = func_get_arg(4);
					$this->stats["Cha"] = func_get_arg(5);
				}
				
				return TRUE;
			} elseif (is_array(func_get_arg(0))) {
				$statsUpdate = func_get_arg(0);
				foreach ($statsUpdate as $key => $value) { $this->stats[ucfirst(strtolower($key))] = $value; }
				
				return TRUE;
			}
		}
		
		public function getStats($stat = "") {
			if ($stat == "") { return $this->stats; }
			else { return $this->stats[ucfirst(strtolower($stat))]; }
		}
		
		public function setHP($hp, $modify = FALSE) {
			if ($modify) { $this->HP += $hp; }
			else { $this->HP = $hp; }
			
			return TRUE;
		}
		
		public function getHP() {
			return $this->HP;
		}
	}
?>