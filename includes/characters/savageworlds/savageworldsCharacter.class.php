<?
	class savageworldsCharacter extends Character {
		const SYSTEM = 'savageworlds';

		protected $traits = ['agi' => 4, 'sma' => 4, 'spi' => 4, 'str' => 4, 'vig' => 4];
		protected $skills = null;
		protected $derivedTraits = ['pace' => 0, 'charisma' => 0, 'parry' => 0, 'toughness' => 0];
		protected $edgesHindrances = '';
		protected $wounds = 0;
		protected $fatigue = 0;
		protected $injuries = '';
		protected $weapons = '';
		protected $equipment = '';
//		protected $advances = '';

		public function setTrait($trait, $value = null) {
			if (array_key_exists($trait, $this->traits)) {
				$this->traits[$trait] = (int) $value;
			} else {
				return false;
			}
		}

		public function getTraits($trait = null) {
			if ($trait == null) {
				return $this->traits;
			} elseif (array_key_exists($trait, $this->traits)) {
				return $this->traits[$trait];
			} else {
				return false;
			}
		}

		public function setDerivedTrait($trait, $value = null) {
			if (array_key_exists($trait, $this->derivedTraits) && ($trait == 'parry' || $trait == 'toughness')) {
				$this->derivedTraits[$trait] = sanitizeString($value);
			} elseif (array_key_exists($trait, $this->derivedTraits)) {
				$this->derivedTraits[$trait] = (int) $value;
			} else {
				return false;
			}
		}

		public function getDerivedTraits($trait = null) {
			if ($trait == null) {
				return $this->derivedTraits;
			} elseif (array_key_exists($trait, $this->derivedTraits)) {
				return $this->derivedTraits[$trait];
			} else {
				return false;
			}
		}

		public function addSkill($skill) {
			if (
				array_key_exists($skill['trait'], savageworlds_consts::getTraits()) &&
				strlen($skill['name']) &&
				in_array($skill['diceType'], [4, 6, 8, 10, 12])
			) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$this->skills[$skill['trait']][] = ['name' => sanitizeString($skill['name']), 'diceType' => (int) $skill['diceType']];
			}
		}

		public function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) {
				$skillInfo = ['trait' => 'trait', 'name' => '', 'diceType' => 4];
			}
?>
									<div class="skill clearfix">
										<input type="text" name="skills[<?=$skillInfo['trait']?>][<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skillName placeholder" data-placeholder="Skill Name">
										<div class="diceSelect"><span>d</span> <select name="skills[<?=$skillInfo['trait']?>][<?=$key?>][diceType]" class="diceType">
<?			foreach ([4, 6, 8, 10, 12] as $dCount) { ?>
											<option<?=$skillInfo['diceType'] == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?			} ?>
										</select></div>
										<div class="remove"><a href="" class="sprite cross small"></a></div>
									</div>
<?
		}

		public function showSkillsEdit($trait) {
			if (sizeof($this->skills[$trait])) {
				foreach ($this->skills[$trait] as $key => $skillInfo) {
					$this->skillEditFormat($key, array_merge(['trait' => $trait], $skillInfo));
				}
			}
		}

		public function displaySkills($trait) {
			if ($this->skills[$trait]) {
				foreach ($this->skills[$trait] as $skill) {
?>
								<div id="skill_<?=$skill['skillID']?>" class="skill clearfix">
									<div class="skillName"><?=$skill['name']?></div>
									<input type="hidden" name="skills[<?=$skill['skillID']?>][trait]" value="<?=$skill['trait']?>">
									<div class="diceType">d<?=$skill['diceType']?></div>
								</div>
<?
				}
			}
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = sanitizeString($edgesHindrances);
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setWounds($wounds) {
			$this->wounds = (int) $wounds;
		}

		public function getWounds() {
			return $this->wounds;
		}

		public function setFatigue($fatigue) {
			$this->fatigue = (int) $fatigue;
		}

		public function getFatigue() {
			return $this->fatigue;
		}

		public function setInjuries($injuries) {
			$this->injuries = sanitizeString($injuries);
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setWeapons($weapons) {
			$this->weapons = sanitizeString($weapons);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setEquipment($equipment) {
			$this->equipment = sanitizeString($equipment);
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function save($bypass = false) {
			global $mysql;
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create']) && !$bypass) {
				$this->setName($data['name']);
				foreach ($data['traits'] as $trait => $value) {
					$this->setTrait($trait, $value);
				}
				foreach ($data['derivedTraits'] as $trait => $value) {
					$this->setDerivedTrait($trait, $value);
				}

				$this->clearVar('skills');
				if (sizeof($data['skills'])) {
					foreach ($data['skills'] as $trait => $skillInfos) {
						foreach ($skillInfos as $skillInfo) {
							$this->addSkill(array_merge(['trait' => $trait], $skillInfo));
						}
					}
				}

				$this->setEdgesHindrances($data['edge_hind']);
				$this->setWounds($data['wounds']);
				$this->setFatigue($data['fatigue']);
				$this->setInjuries($data['injuries']);
				$this->setWeapons($data['weapons']);
				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
