<?
	class dnd5Character extends d20Character {
		const SYSTEM = 'dnd5';

		protected $race = '';
		protected $background = '';
		protected $alignment = 'tn';
		protected $inspiration = 0;
		protected $profBonus = 0;
		protected $saveProf = array('str'=> false, 'dex'=> false, 'con'=> false, 'int'=> false, 'wis'=> false, 'cha'=> false);
		protected $ac = 0;
		protected $initiative = 0;
		protected $speed = 0;
		protected $hp = array('total' => 0, 'current' => 0, 'temp' => 0);
		protected $deathSaves = array('success' => 0, 'failure' => 0);
		protected $languages = '';
		protected $spellDC = 0;
		protected $spellAB = 0;

		protected $linkedTables = array('feats', 'skills');

		public function setRace($value) {
			$this->race = $value;
		}

		public function getRace() {
			return $this->race;
		}

		public function setBackground($value) {
			$this->background = $value;
		}

		public function getBackground() {
			return $this->background;
		}

		public function setAlignment($value) {
			if (dnd5_consts::getAlignments($value) && $value != NULL) $this->alignment = $value;
		}

		public function getAlignment() {
			return dnd5_consts::getAlignments($this->alignment);
		}

		public function setInspiration($value) {
			$this->inspiration = intval($value);
		}

		public function getInspiration() {
			return $this->inspiration;
		}

		public function setProfBonus($value) {
			$this->profBonus = intval($value);
		}

		public function getProfBonus() {
			return $this->profBonus;
		}
		
		public function setSaveProf($save, $value) {
			if (d20Character_consts::getStatNames($save)) $this->saveProf[$save] = $value;
			else return false;
		}

		public function getSaveProf($save = null) {
			if (d20Character_consts::getStatNames($save)) return $this->saveProf[$save];
			elseif ($save == null) return $this->saveProf;
			else return false;
		}

		public function setAC($value) {
			$this->ac = intval($value);
		}

		public function getAC($key = null) {
			return $this->ac;
		}

		public function setInitiative($value) {
			$this->initiative = intval($value);
		}

		public function getInitiative($key = null) {
			return $this->ac;
		}

		public function setSpeed($value) {
			$this->speed = intval($value);
		}

		public function getSpeed($key = null) {
			return $this->speed;
		}

		static public function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) $skillInfo = array('name' => '', 'proficient' => false);
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill clearfix">
							<span class="shortNum alignCenter skill_prof"><input type="checkbox" name="skill[<?=$key?>][proficient]"></span>
							<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name medText placeholder dontAdd" data-placeholder="Skill Name">
							<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) echo "							<option value=\"$short\"".($skillInfo['stat'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
?>
							</select></span>
						</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill, $this->getStatMod($skill['stat'], false));
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) echo "						<option value=\"$short\"".($skillInfo['stat'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
?>
						</select></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['ab']) && strlen($weapon['damage'])) $this->weapons[] = $weapon;
		}

		public function showWeaponsEdit($min) {
			$weaponNum = 0;
			if (!is_array($this->weapons)) $this->weapons = (array) $this->weapons;
			foreach ($this->weapons as $weaponInfo) $this->weaponEditFormat($weaponNum++, $weaponInfo);
			if ($weaponNum < $min) while ($weaponNum < $min) $this->weaponEditFormat($weaponNum++);
		}

		public function weaponEditFormat($weaponNum, $weaponInfo = array()) {
			if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) $weaponInfo = array();
?>
						<div class="weapon">
							<div class="tr labelTR">
								<label class="medText lrBuffer shiftRight borderBox">Name</label>
								<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
								<label class="shortText alignCenter lrBuffer">Damage</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][ab]" value="<?=$weaponInfo['ab']?>" class="weapons_ab shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][notes]" value="<?=$weaponInfo['notes']?>" class="weapon_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
		}

		public function displayWeapons() {
			foreach ($this->weapons as $weapon) {
?>
					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
							<label class="shortText alignCenter lrBuffer">Damage</label>
						</div>
						<div class="tr">
							<span class="weapon_name medText lrBuffer"><?=$weapon['name']?></span>
							<span class="weapons_ab shortText lrBuffer alignCenter"><?=$weapon['ab']?></span>
							<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weapon['damage']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="weapon_notes lrBuffer"><?=$weapon['notes']?></span>
						</div>
					</div>
<?
			}
		}

		public function addArmor($armor) {
			if (strlen($armor['name']) && strlen($armor['ac'])) $this->armor[] = $armor;
		}


		public function setItems($items) {
			$this->items = $items;
		}

		public function getItems() {
			return $this->items;
		}

		public function setSpells($spells) {
			$this->spells = $spells;
		}

		public function getSpells() {
			return $this->spells;
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				$this->setSize($data['size']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);
				$this->setAlignment($data['alignment']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) $this->setSave($save, $sub, $value);
				}
				$this->setHP('total', $data['hp']['total']);
				$this->setDamageReduction($data['damageReduction']);
				foreach ($data['ac'] as $key => $value) $this->setAC($key, $value);
				$this->setInitiative('stat', $data['initiative']['stat']);
				$this->setInitiative('misc', $data['initiative']['misc']);
				$this->setAttackBonus('base', $data['attackBonus']['base']);
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['melee'], 'melee');
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['ranged'], 'ranged');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['melee'], 'melee');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['ranged'], 'ranged');

				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillID => $skillInfo) {
					$this->updateSkill($skillID, $skillInfo);
				} }

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

				$this->clearVar('armor');
				foreach ($data['armor'] as $armor) $this->addArmor($armor);

				$this->setItems($data['items']);
				$this->setSpells($data['spells']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>