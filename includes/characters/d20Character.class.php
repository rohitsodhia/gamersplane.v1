<?
	abstract class d20Character extends Character {
		protected $classes = [];
		protected $experience = 0;
		protected $stats = ['str' => 10, 'dex' => 10, 'con' => 10, 'int' => 10, 'wis' => 10, 'cha' => 10];
		protected $ac = [];
		protected $hp = [];
		protected $speed = 0;
		protected $saves = [
			'fort' => ['base' => 0, 'stat' => 'con', 'misc' => 0],
			'ref' => ['base' => 0, 'stat' => 'dex', 'misc' => 0],
			'will' => ['base' => 0, 'stat' => 'wis', 'misc' => 0]
		];
		protected $initiative = ['stat' => 'dex', 'misc' => 0];
		protected $attackBonus = [
			'base' => 0,
			'stat' => ['melee' => 'str', 'ranged' => 'dex'],
			'misc' => ['melee' => 0, 'ranged' => 0]
		];
		protected $skills = [];
		protected $feats = [];
		protected $items = '';

		public function setClass($class, $level = 1) {
			$this->classes[sanitizeString($class)] = (int) $level;
		}

		public function setClasses($classes) {
			if (is_array($classes)) {
				$this->classes = [];
				foreach ($classes as $class => $level) {
					$this->setClass($class, $level);
				}
			}
		}

		public function removeClass($class) {
			if (isset($this->classes[$class])) {
				unset($this->classes[$class]);
			}
		}

		public function getClasses($class = null) {
			if ($class == null) {
				return $this->classes;
			} elseif (in_array($class, array_keys($this->classes))) {
				return $this->classes[$class];
			} else {
				return false;
			}
		}

		public function displayClasses() {
			array_walk($this->classes, function ($value, $key) {
				echo $key.' - '.$value.'<br>';
			});
		}

		public function getLevel() {
			return array_sum((array) $this->classes);
		}

		public function setStat($stat, $value = 10) {
			if (array_key_exists($stat, $this->stats)) {
				$value = (int) $value;
				if ($value > 0) {
					$this->stats[$stat] = $value;
				}
			} else {
				return false;
			}
		}

		public function getStat($stat = null) {
			if ($stat == null) {
				return $this->stats;
			} elseif (array_key_exists($stat, $this->stats)) {
				return $this->stats[$stat];
			} else {
				return false;
			}
		}

		public function getStatMod($stat, $showSign = true) {
			if (array_key_exists($stat, $this->stats)) {
				$mod = floor(($this->stats[$stat] - 10) / 2);
				if ($showSign) {
					$mod = showSign($mod);
				}
				return $mod;
			} else {
				return false;
			}
		}

		public function setAC($key, $value) {
			if (array_key_exists($key, $this->ac)) {
				$this->ac[$key] = (int) $value;
			} else {
				return false;
			}
		}

		public function getAC($key = null) {
			$ac = (array) $this->ac;
			if ($key == null) {
				return array_merge(array('total' => array_sum($ac) + 10), $ac);
			} elseif (array_key_exists($key, $ac)) {
				return $ac[$key];
			} elseif ($key == 'total') {
				return array_sum($ac) + 10;
			} else {
				return false;
			}
		}

		public function setHP($key, $value) {
			$hp = (array) $this->hp;
			if (array_key_exists($key, $hp)) {
				$hp[$key] = (int) $value;
			} else {
				return false;
			}
		}

		public function getHP($key = null) {
			$hp = (array) $this->hp;
			if (array_key_exists($key, $hp)) {
				return $hp[$key];
			} elseif ($key == null) {
				return $hp;
			} else {
				return false;
			}
		}

		public function setSpeed($value) {
			$value = (int) $value;
			if ($value >= 0) {
				$this->speed = $value;
			} else {
				return false;
			}
		}

		public function getSpeed() {
			return $this->speed;
		}

		public function setSave($save, $key, $value) {
			if (array_key_exists($save, $this->saves) && array_key_exists($key, $this->saves[$save])) {
				if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) {
					$this->saves[$save][$key] = $value;
				} elseif ($key != 'stat') {
					$this->saves[$save][$key] = (int) $value;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getSave($save = null, $key = null) {
			if (array_key_exists($save, $this->saves)) {
				if ($key == null) {
					return $this->saves[$save];
				} elseif (array_key_exists($key, $this->saves[$save])) {
					return $this->saves[$save][$key];
				} elseif ($key == 'total') {
					$total = 0;
					foreach ($this->saves[$save] as $value) {
						if (is_numeric($value)) {
							$total += $value;
						}
					}
					$total += $this->getStatMod($this->saves[$save]['stat'], false);
					return $total;
				} else {
					return false;
				}
			} elseif ($save == null) {
				return $this->saves;
			} else {
				return false;
			}
		}

		public function setInitiative($key, $value) {
			if (array_key_exists($key, $this->initiative)) {
				if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) {
					$this->initiative[$key] = $value;
				} elseif ($key != 'stat') {
					$this->initiative[$key] = (int) $value;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getInitiative($key = null) {
			if ($key == null) {
				return $this->initiative;
			} elseif (array_key_exists($key, $this->initiative)) {
				return $this->initiative[$key];
			} elseif ($key == 'total') {
				$total = 0;
				foreach ($this->initiative as $value) {
					if (is_numeric($value)) {
						$total += $value;
					}
				}
				$total += $this->getStatMod($this->initiative['stat'], false);
				return $total;
			} else {
				return false;
			}
		}

		public function addSkill($skill) {
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				foreach ($skill as $key => $value) {
					$skill[$key] = sanitizeString($value);
				}
				$this->skills[] = $skill;
			}
		}

		public static function featEditFormat($key = 1, $featInfo = null) {
			if ($featInfo == null) {
				$featInfo = ['name' => '', 'notes' => ''];
			}
?>
							<div class="feat item clearfix">
								<input type="text" name="feats[<?=$key?>][name]" value="<?=$featInfo['name']?>" class="feat_name placeholder" data-placeholder="Feat">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="feats[<?=$key?>][notes]"><?=$featInfo['notes']?></textarea>
							</div>
<?
		}

		public function showFeatsEdit() {
			if (sizeof($this->feats)) {
				foreach ($this->feats as $key => $feat) {
					$this->featEditFormat($key + 1, $feat);
				}
			} else {
				$this->featEditFormat();
			}
		}

		public function displayFeats() {
			if ($this->feats) {
				foreach ($this->feats as $feat) {
?>
					<div class="feat tr clearfix">
						<span class="feat_name"><?=$feat['name']?></span>
<?	if (strlen($feat['notes'])) { ?>
						<a href="" class="feat_notesLink">Notes</a>
						<div class="notes"><?=printReady($feat['notes'])?></div>
<?	} ?>
					</div>
<?
				}
			} else {
				echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
			}
		}

		public function addFeat($feat) {
			if (strlen($feat['name'])) {
				newItemized('feat', $feat['name'], $this::SYSTEM);
				foreach ($feat as $key => $value) {
					$feat[$key] = sanitizeString($value);
				}
				$this->feats[] = $feat;
			}
		}

		public function setAttackBonus($key, $value, $type = null) {
			if (property_exists($this->attackBonus, $key)) {
				if (is_object($this->attackBonus[$key]) && property_exists($this->attackBonus[$key], $type)) {
					if ($key == 'stat' && $value != null && d20Character_consts::getStatNames($value)) {
						$this->attackBonus[$key][$type] = $value;
					} elseif ($key != 'stat') {
						$this->attackBonus[$key][$type] = (int) $value;
					} else {
						return false;
					}
				} elseif (!is_object($this->attackBonus[$key])) {
					$this->attackBonus[$key] = (int) $value;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function getAttackBonus($key = null, $type = null) {
			if ($key == null) {
				return $this->attackBonus;
			} elseif ($key == 'total' && $type != null) {
				$total = 0;
				foreach ($this->attackBonus as $value) {
					if (is_object($value) && is_numeric($value->$type)) {
						$total += $value[$type];
					} elseif (is_numeric($value[$type])) {
						$total += $value;
					}
				}
				$total += $this->getStatMod($this->attackBonus['stat'][$type], false);
				return $total;
			} elseif (property_exists($this->attackBonus, $key)) {
				if (is_object($this->attackBonus->$key) && array_key_exists($type, $this->attackBonus->$key)) {
					return $this->attackBonus[$key][$type];
				} elseif (!is_object($this->attackBonus[$key])) {
					return $this->attackBonus[$key];
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function setItems($value) {
			$this->items = sanitizeString($value);
		}

		public function getItems() {
			return $this->items;
		}

		public function setExperience($value) {
			$this->experience = sanitizeString($value);
		}

		public function getExperience() {
			return $this->experience;
		}
	}
?>
