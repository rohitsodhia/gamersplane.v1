<?
	class thestrangeCharacter extends Character {
		const SYSTEM = 'thestrange';

		protected $descriptor = '';
		protected $type = '';
		protected $focus = '';
		protected $tier = 0;
		protected $effort = 0;
		protected $xp = 0;
		protected $stats = array(
							'might' => array(
								'pool' => array(
									'current' => 0,
									'max' => 0),
								'edge' => 0),
							'speed' => array(
								'pool' => array(
									'current' => 0,
									'max' => 0),
								'edge' => 0),
							'intellect' => array(
								'pool' => array(
									'current' => 0,
									'max' => 0),
								'edge' => 0));
		protected $damage = array('impaired' => 0, 'debilitated' => 0);
		protected $recovery = 0;
		protected $recoveryTimes = array('action' => false, 'ten_min' => false, 'hour' => false, 'ten_hours' => false);
		protected $armor = 0;
		protected $attacks = null;
		protected $skills = null;
		protected $specialAbilities = null;
		protected $cyphers = null;
		protected $possessions = '';

		public function setDescriptor($descriptor) {
			$this->descriptor = sanitizeString($descriptor);
		}

		public function getDescriptor() {
			return $this->descriptor;
		}

		public function setType($type) {
			$this->type = sanitizeString($type);
		}

		public function getType() {
			return $this->type;
		}

		public function setFocus($focus) {
			$this->focus = sanitizeString($focus);
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
			$cKey = &$this->stats[$stat];
			foreach ($key as $iKey) {
				if (!isset($cKey[$iKey]))
					return false;
				$cKey = &$cKey[$iKey];
			}
			$cKey = sanitizeString($value);
		}

		public function getStats($stat = null, $key = null) {
			if ($stat == null) return $this->stats;
			elseif ($key != null) {
				$key = explode('.', $key);
				$cKey = &$this->stats[$stat];
				foreach ($key as $iKey) {
					if (!isset($cKey[$iKey])) return false;
					$cKey = &$cKey[$iKey];
				}
				return $cKey;
			}
		}

		public function setDamage($key, $value = null) {
			if (array_key_exists($key, $this->damage))
				$this->damage[$key] = intval($value);
			else
				return false;
		}

		public function getDamage($key = null) {
			if ($key == null)
				return $this->damage;
			elseif (array_key_exists($key, $this->damage))
				return $this->damage[$key];
			else
				return false;
		}

		public function setRecovery($recovery) {
			$this->recovery = intval($recovery);
		}

		public function getRecovery() {
			return $this->recovery;
		}

		public function setRecoveryTimes($key, $value = null) {
			if (array_key_exists($key, $this->recoveryTimes))
				$this->recoveryTimes[$key] = intval($value);
			else
				return false;
		}

		public function getRecoveryTimes($key = null) {
			if ($key == null)
				return $this->recoveryTimes;
			elseif (array_key_exists($key, $this->recoveryTimes))
				return $this->recoveryTimes[$key];
			else
				return false;
		}

		public function setArmor($armor) {
			$this->armor = intval($armor);
		}

		public function getArmor() {
			return $this->armor;
		}

		public function addAttack($attack) {
			if (strlen($attack['name']))
				foreach ($attack as $key => $value)
					$attack[$key] = sanitizeString($value);
				$this->attacks[] = $attack;
		}

		public function showAttacksEdit($min) {
			$attackNum = 0;
			if (!is_array($this->attacks))
				$this->attacks = (array) $this->attacks;
			foreach ($this->attacks as $attackInfo)
				$this->attackEditFormat($attackNum++, $attackInfo);
			if ($attackNum < $min)
				while ($attackNum < $min)
					$this->attackEditFormat($attackNum++);
		}

		public function attackEditFormat($attackNum, $attackInfo = array()) {
			if (!is_array($attackInfo) || sizeof($attackInfo) == 0) $attackInfo = array();
?>
						<div class="attack tr">
							<input type="text" name="attacks[<?=$attackNum?>][name]" value="<?=$attackInfo['name']?>" class="name medText">
							<input type="text" name="attacks[<?=$attackNum?>][mod]" value="<?=$attackInfo['mod']?>" class="mod shortNum lrBuffer">
							<input type="text" name="attacks[<?=$attackNum?>][dmg]" value="<?=$attackInfo['dmg']?>" class="dmg shortNum">
							<a href="" class="attack_remove sprite cross lrBuffer"></a>
						</div>
<?
		}

		public function displayAttacks() {
			if (sizeof($this->attacks)) { foreach ($this->attacks as $attack) {
?>
							<div class="attack tr">
								<div class="name medText"><?=$attack['name']?></div>
								<div class="mod shortNum lrBuffer alignCenter"><?=$attack['mod']?></div>
								<div class="dmg shortNum alignCenter"><?=$attack['dmg']?></div>
							</div>
<?
			} } else echo "\t\t\t\t\t\t<p id=\"noAttacks\">This character currently has no attacks.</p>\n";
		}

		public function addSkill($skill) {
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				foreach ($skill as $key => $value)
					$skill[$key] = sanitizeString($value);
				$this->skills[] = $skill;
			}
		}

		public static function skillEditFormat($key = null, $skillInfo = null) {
			if ($key == null) $key = 1;
			if ($skillInfo == null) $skillInfo = array('name' => '', 'prof' => '');
?>
							<div class="skill clearfix">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name width5 alignLeft placeholder" data-placeholder="Skill Name">
								<div class="skill_prof alignCenter shortNum lrBuffer"><div><?=$skillInfo['prof'] != ''?$skillInfo['prof']:'&nbsp;'?></div><input type="hidden" name="skills[<?=$key?>][prof]" value="<?=$skillInfo['prof']?>"></div>
								<a href="" class="skill_remove sprite cross lrBuffer"></a>
							</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill);
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
					<div class="skill tr clearfix">
						<div class="skill_name width5"><?=$skill['name']?></div>
						<div class="skill_prof alignCenter shortNum lrBuffer"><div><?=$skill['prof']?></div></div>
					</div>
<?
			} } else echo "\t\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addSpecialAbility($specialAbility) {
			if (strlen($specialAbility['name'])) {
				newItemized('specialAbility', $specialAbility['name'], $this::SYSTEM);
				foreach ($specialAbility as $key => $value)
					$specialAbility[$key] = sanitizeString($value);
				$this->specialAbilities[] = $specialAbility;
			}
		}

		public static function specialAbilityEditFormat($key = 1, $specialAbilityInfo = null) {
			if ($specialAbilityInfo == null) $specialAbilityInfo = array('name' => '', 'notes' => '');
?>
							<div class="specialAbility clearfix">
								<input type="text" name="specialAbilities[<?=$key?>][name]" value="<?=$specialAbilityInfo['name']?>" class="specialAbility_name placeholder" data-placeholder="Ability Name">
								<a href="" class="specialAbility_notesLink">Notes</a>
								<a href="" class="specialAbility_remove sprite cross"></a>
								<textarea name="specialAbilities[<?=$key?>][notes]"><?=$specialAbilityInfo['notes']?></textarea>
							</div>
<?
		}

		public function showSpecialAbilitiesEdit() {
			if (sizeof($this->specialAbilities)) { foreach ($this->specialAbilities as $key => $specialAbility) {
				$this->specialAbilityEditFormat($key + 1, $specialAbility);
			} } else $this->specialAbilityEditFormat();
		}

		public function displaySpecialAbilities() {
			if ($this->specialAbilities) { foreach ($this->specialAbilities as $specialAbility) { ?>
					<div class="specialAbility tr clearfix">
						<span class="specialAbility_name"><?=$specialAbility['name']?></span>
<?	if (strlen($specialAbility['notes'])) { ?>
						<a href="" class="specialAbility_notesLink">Notes</a>
						<div class="notes"><?=$specialAbility['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSpecialAbilities\">This character currently has no special abilities.</p>\n";
		}

		public function addCypher($cypher) {
			if (strlen($cypher['name'])) {
				newItemized('cypher', $cypher['name'], $this::SYSTEM);
				foreach ($cypher as $key => $value)
					$cypher[$key] = sanitizeString($value);
				$this->cyphers[] = $cypher;
			}
		}

		public static function cypherEditFormat($key = 1, $cypherInfo = null) {
			if ($cypherInfo == null) $cypherInfo = array('name' => '', 'notes' => '');
?>
							<div class="cypher clearfix">
								<input type="text" name="cyphers[<?=$key?>][name]" value="<?=$cypherInfo['name']?>" class="cypher_name placeholder" data-placeholder="Cypher Name">
								<a href="" class="cypher_notesLink">Notes</a>
								<a href="" class="cypher_remove sprite cross"></a>
								<textarea name="cyphers[<?=$key?>][notes]"><?=$cypherInfo['notes']?></textarea>
							</div>
<?
		}

		public function showCyphersEdit() {
			if (sizeof($this->cyphers)) { foreach ($this->cyphers as $key => $cypher) {
				$this->cypherEditFormat($key + 1, $cypher);
			} } else $this->cypherEditFormat();
		}

		public function displayCyphers() {
			if ($this->cyphers) { foreach ($this->cyphers as $cypher) { ?>
					<div class="cypher tr clearfix">
						<span class="cypher_name"><?=$cypher['name']?></span>
<?	if (strlen($cypher['notes'])) { ?>
						<a href="" class="cypher_notesLink">Notes</a>
						<div class="notes"><?=$cypher['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noCyphers\">This character currently has no cyphers.</p>\n";
		}

		public function setPossessions($possessions) {
			$this->possessions = sanitizeString($possessions);
		}

		public function getPossessions() {
			return $this->possessions;
		}

		public function save($bypass = false) {
			global $mysql;
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create']) && !$bypass) {
				$this->setName($data['name']);
				$this->setDescriptor($data['descriptor']);
				$this->setType($data['type']);
				$this->setFocus($data['focus']);
				$this->setTier($data['tier']);
				$this->setEffort($data['effort']);
				$this->setXP($data['xp']);
				foreach ($data['stats'] as $stat => $values) {
					$this->setStat($stat, 'pool.current', $values['pool']['current']);
					$this->setStat($stat, 'pool.max', $values['pool']['max']);
					$this->setStat($stat, 'edge', $values['edge']);
				}
				$this->setDamage('impaired', isset($data['damage']['impaired']));
				$this->setDamage('debilitated', isset($data['damage']['debilitated']));
				$this->setRecovery($data['recovery']);
				foreach (array('action', 'ten_min', 'hour', 'ten_hours') as $slug) $this->setRecoveryTimes($slug, isset($data['recoveryTimes'][$slug]));
				$this->setArmor($data['armor']);

				$this->clearVar('attacks');
				if ($data['attacks'] && sizeof($data['attacks']))
					foreach ($data['attacks'] as $attack) $this->addAttack($attack);

				$this->clearVar('skills');
				if ($data['skills'] && sizeof($data['skills']))
					foreach ($data['skills'] as $skillInfo) $this->addSkill($skillInfo);

				$this->clearVar('specialAbilities');
				if ($data['specialAbilities'] && sizeof($data['specialAbilities']))
					foreach ($data['specialAbilities'] as $specialAbilityInfo) $this->addSpecialAbility($specialAbilityInfo);

				$this->clearVar('cyphers');
				if ($data['cyphers'] && sizeof($data['cyphers']))
					foreach ($data['cyphers'] as $cypherInfo) $this->addCypher($cypherInfo);

				$this->setPossessions($data['possessions']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
