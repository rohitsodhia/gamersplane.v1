<?
	class spycraft2_character extends d20character {
		protected $ac = array('class' => 0, 'armor' => 0, 'dex' => 0, 'misc' => 0);
		protected $damage = array('vitality' => 0, 'wounds' => 0, 'subdual' => 'stress' => 0);
		protected $initiative = array('class' => 0, 'misc' => 0);
		protected $attackBonus = array('base' => 0, 'misc' => array('melee' => 0, 'ranged' => 0));
		protected $actionDice = array('total' => 0, 'type' => '');
		protected $checks = array('knowledge' => 0, 'request' => 0, 'gear' => 0);
		protected $weapons = array();
		protected $gold = 0;
		protected $abilities = array();
		protected $feats = array();
		protected $languages = array();
		
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