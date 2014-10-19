<?
	class numeneraCharacter extends Character {
		const SYSTEM = 'numenera';

		protected $descriptor = '';
		protected $type = '';
		protected $focus = '';
		protected $tier = 0;
		protected $effort = 0;
		protected $xp = 0;
		protected $stats = array(
							'might' => array(
								'pool' => array(
									'total' => 0, 
									'used' => 0), 
								'edge' => 0), 
							'speed' => array(
								'pool' => array(
									'total' => 0, 
									'used' => 0), 
								'edge' => 0), 
							'intellect' => array(
								'pool' => array(
									'total' => 0, 
									'used' => 0), 
								'edge' => 0));
		protected $damage = array('impaired' => 0, 'debilitated' => 0);
		protected $recovery = 0;
		protected $recoveryTimes = array('action' => false, 'ten_min' => false, 'hour' => false, 'ten_hours' => false);
		protected $armor = 0;
		protected $attacks = null;
		protected $skills = null;
		protected $specialAbilities = null;
		protected $cypers = null;
		protected $possessions = '';

		public function setDescriptor($descriptor) {
			$this->descriptor = $descriptor;
		}

		public function getDescriptor() {
			return $this->descriptor;
		}

		public function setType($type) {
			$this->type = $type;
		}

		public function getType() {
			return $this->type;
		}

		public function setFocus($focus) {
			$this->focus = $focus;
		}

		public function getFocus() {
			return $this->focus;
		}

		public function setTier($tier) {
			$this->tier = intval($tier);
		}

		public function getTier() {
			return $this->tier;
		}

		public function setEffort($effort) {
			$this->effort = intval($effort);
		}

		public function getEffort() {
			return $this->effort;
		}

		public function setXP($xp) {
			$this->xp = intval($xp);
		}

		public function getXP() {
			return $this->xp;
		}

		public function setStat($stat, $key, $value = null) {
			$key = explode('.', $key);
			if (!strlen($key)) return false;
			$cKey = &$this->stats[$stat];
			foreach ($key as $iKey) {
				if (!isset($cKey[$iKey])) return false;
				$cKey = &$cKey[$iKey];
			}
			$cKey = $value;
		}
		
		public function getStats($stat = null, $key = null) {
			if ($stat == null) return $this->stats;
			elseif ($key != null) {
				$cKey = &$this->stats[$stat];
				foreach ($key as $iKey) {
					if (!isset($cKey[$iKey])) return false;
					$cKey = &$cKey[$iKey];
				}
				return $cKey;
			}
		}

		public function setDamage($key, $value = null) {
			if (array_key_exists($key, $this->damage)) $this->damage[$key] = intval($value);
			else return false;
		}
		
		public function getDamage($key = null) {
			if ($key == null) return $this->damage;
			elseif (array_key_exists($key, $this->damage)) return $this->damage[$key];
			else return false;
		}

		public function setRecovery($recovery) {
			$this->recovery = intval($recovery);
		}

		public function getRecovery() {
			return $this->recovery;
		}

		public function setRecoveryTimes($key, $value = null) {
			if (array_key_exists($key, $this->recoveryTimes)) $this->recoveryTimes[$key] = intval($value);
			else return false;
		}
		
		public function getRecoveryTimes($key = null) {
			if ($key == null) return $this->recoveryTimes;
			elseif (array_key_exists($key, $this->recoveryTimes)) return $this->recoveryTimes[$key];
			else return false;
		}

		public function setArmor($armor) {
			$this->armor = intval($armor);
		}

		public function getArmor() {
			return $this->armor;
		}

		public function addSkill($skill) {
			if (array_key_exists($skill['trait'], savageworlds_consts::getTraits()) && strlen($skill['name']) && in_array($skill['diceType'], array(4, 6, 8, 10, 12))) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$this->skills[$skill['trait']][] = array('name' => $skill['name'], 'diceType' => $skill['diceType']);
			}
		}

		public function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) $skillInfo = array('trait' => 'trait', 'name' => '', 'diceType' => 4);
?>
									<div class="skill clearfix">
										<input type="text" name="skills[<?=$skillInfo['trait']?>][<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skillName placeholder" data-placeholder="Skill Name">
										<div class="diceSelect"><span>d</span> <select name="skills[<?=$skillInfo['trait']?>][<?=$key?>][diceType]" class="diceType">
<?			foreach (array(4, 6, 8, 10, 12) as $dCount) { ?>
											<option<?=$skillInfo['diceType'] == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?			} ?>
										</select></div>
										<div class="remove"><a href="" class="sprite cross small"></a></div>
									</div>
<?
		}

		public function showSkillsEdit($trait) {
			if (sizeof($this->skills[$trait])) { foreach ($this->skills[$trait] as $key => $skillInfo) {
				$this->skillEditFormat($trait, array_merge(array('trait' => $trait), $skillInfo));
			} }
		}

		public function displaySkills($trait) {
			if ($this->skills[$trait]) { foreach ($this->skills[$trait] as $skill) {
?>
								<div id="skill_<?=$skill['skillID']?>" class="skill clearfix">
									<div class="skillName"><?=$skill['name']?></div>
									<input type="hidden" name="skills[<?=$skill['skillID']?>][trait]" value="<?=$skill['trait']?>">
									<div class="diceType">d<?=$skill['diceType']?></div>
								</div>
<?
			} }
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = $edgesHindrances;
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setWounds($wounds) {
			$this->wounds = intval($wounds);
		}

		public function getWounds() {
			return $this->wounds;
		}

		public function setFatigue($fatigue) {
			$this->fatigue = intval($fatigue);
		}

		public function getFatigue() {
			return $this->fatigue;
		}

		public function setInjuries($injuries) {
			$this->injuries = $injuries;
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setWeapons($weapons) {
			$this->weapons = $weapons;
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setEquipment($equipment) {
			$this->equipment = $equipment;
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
				foreach ($data['traits'] as $trait => $value) $this->setTrait($trait, $value);
				foreach ($data['derivedTraits'] as $trait => $value) $this->setDerivedTrait($trait, $value);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) { foreach ($data['skills'] as $trait => $skillInfos) {
					foreach ($skillInfos as $skillInfo) $this->addSkill(array_merge(array('trait' => $trait), $skillInfo));
				} }

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