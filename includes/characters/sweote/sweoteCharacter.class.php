<?
	class sweoteCharacter extends Character {
		const SYSTEM = 'sweote';

		protected $species = '';
		protected $career = '';
		protected $specialization = '';
		protected $xp = array('total' => 0, 'spent' => 0);
		protected $stats = array('bra' => 0, 'agi' => 0, 'int' => 0, 'cun' => 0, 'wil' => 0, 'pre' => 0);
		protected $defenses = array('melee' => 0, 'ranged' => 0, 'soak' => 0);
		protected $hp = array('maxStrain' => 0, 'currentStrain' => 0, 'maxWounds' => 0, 'currentWounds' => 0);
		protected $weapons = array();
		protected $motivations = '';
		protected $obligations = '';

		public function setSpecies($species) {
			$this->species = sanitizeString($species);
		}

		public function getSpecies() {
			return $this->species;
		}

		public function setCareer($career) {
			$this->career = sanitizeString($career);
		}

		public function getCareer() {
			return $this->career;
		}

		public function setSpecialization($specialization) {
			$this->specialization = sanitizeString($specialization);
		}

		public function getSpecialization() {
			return $this->specialization;
		}

		public function setXP($type, $value = '') {
			if (in_array($type, array_keys($this->xp))) {
				$value = intval($value);
				if ($value >= 0) 
					$this->xp[$type] = $value;
			} else return false;
		}
		
		public function getXP($type = null) {
			if ($type == null) 
				return $this->xp;
			elseif (in_array($type, array_keys($this->xp))) 
				return $this->xp[$type];
			else 
				return false;
		}

		public function setStat($stat, $value = '') {
			if (in_array($stat, array_keys($this->stats))) {
				$value = intval($value);
				if ($value > 0) 
					$this->stats[$stat] = $value;
			} else 
				return false;
		}
		
		public function getStat($stat = null) {
			if ($stat == null) 
				return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) 
				return $this->stats[$stat];
			else 
				return false;
		}

		public function setDefense($defense, $value = '') {
			if (in_array($defense, array_keys($this->defenses))) {
				$value = intval($value);
				if ($value >= 0) 
					$this->defenses[$defense] = $value;
			} else 
				return false;
		}
		
		public function getDefense($defense = null) {
			if ($defense == null) 
				return $this->defenses;
			elseif (in_array($defense, array_keys($this->defenses))) 
				return $this->defenses[$defense];
			else 
				return false;
		}

		public function setHP($type, $value = '') {
			if (in_array($type, array_keys($this->hp))) {
				$value = intval($value);
				if ($value > 0) 
					$this->hp[$type] = $value;
			} else 
				return false;
		}
		
		public function getHP($type = null) {
			if ($type == null) 
				return $this->hp;
			elseif (in_array($type, array_keys($this->hp))) 
				return $this->hp[$type];
			else 
				return false;
		}

		static public function skillEditFormat($key = 1, $skillInfo = null) {
?>
							<div id="skill_<?=$key?>" class="skill tr clearfix">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name medText">
								<span class="skill_stat"><select name="skills[<?=$key?>][stat]">
<?	foreach (sweote_consts::getStatNames() as $short => $stat) { ?>
									<option value="<?=$short?>"<?=$skillInfo['stat'] == $short?' selected="selected"':''?>><?=$stat?></option>
<?	} ?>
								</select></span>
								<input type="text" name="skills[<?=$key?>][rank]" value="<?=$skillInfo['rank']?>" class="skill_rank shortNum lrBuffer">
								<span class="skill_career shortNum lrBuffer alignCenter"><input type="checkbox" name="skills[<?=$key?>][career]"<?=$skillInfo['career']?' checked="checked"':''?>></span>
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
						<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
							<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
							<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
							<span class="skill_rank alignCenter shortNum lrBuffer"><?=$skill['rank']?></span>
							<span class="skill_career alignCenter shortNum lrBuffer"><?=$skill['career']?'<div class="sprite check"></div>':''?></span>
						</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addSkill($skill) {
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$skill['name'] = sanitizeString($skill['name']);
				$skill['stat'] = sanitizeString($skill['stat']);
				$skill['rank'] = intval($skill['rank']);
				$skill['career'] = $skill['career']?true:false;
				$this->skills[] = $skill;
			}
		}

		public static function talentEditFormat($key = 1, $talentInfo = null) {
			if ($talentInfo == null) $talentInfo = array('name' => '', 'notes' => '');
?>
							<div class="talent">
								<input type="text" name="talents[<?=$key?>][name]" value="<?=$talentInfo['name']?>" class="talent_name placeholder" data-placeholder="Talent Name">
								<a href="" class="talent_notesLink">Notes</a>
								<a href="" class="talent_remove sprite cross"></a>
								<textarea name="talents[<?=$key?>][notes]"><?=$talentInfo['notes']?></textarea>
							</div>
<?
		}

		public function showTalentsEdit() {
			if (sizeof($this->talents)) { foreach ($this->talents as $key => $talent) {
				$this->talentEditFormat($key + 1, $talent);
			} } else $this->talentEditFormat();
		}

		public function displayTalents() {
			if ($this->talents) { foreach ($this->talents as $talent) { ?>
					<div class="talent tr clearfix">
						<span class="talent_name"><?=$talent['name']?></span>
<?	if (strlen($talent['notes'])) { ?>
						<a href="" class="talent_notesLink">Notes</a>
						<div class="talent_notes"><?=$talent['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noTalents\">This character currently has no talents.</p>\n";
		}
		
		public function addTalent($talent) {
			if (strlen($talent['name'])) {
				newItemized('talent', $talent['name'], $this::SYSTEM);
				foreach ($talent as $key => $value) 
					$talent[$key] = sanitizeString($value);
				$this->talents[] = $talent;
			}
		}
		
		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['skill']) && strlen($weapon['damage'])) {
				foreach ($weapon as $key => $value) 
					$weapon[$key] = sanitizeString($value);
				$this->weapons[] = $weapon;
			}
		}

		public function showWeaponsEdit($min) {
			$weaponNum = 0;
			if (!is_array($this->weapons)) 
				$this->weapons = (array) $this->weapons;
			foreach ($this->weapons as $weaponInfo) 
				$this->weaponEditFormat($weaponNum++, $weaponInfo);
			if ($weaponNum < $min) 
				while ($weaponNum < $min) 
					$this->weaponEditFormat($weaponNum++);
		}

		public function weaponEditFormat($weaponNum, $weaponInfo = array()) {
			if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) 
				$weaponInfo = array();
?>
							<div class="weapon">
								<div class="tr labelTR">
									<label class="medText lrBuffer borderBox shiftRight">Name</label>
									<label class="weapons_skill lrBuffer borderBox shiftRight">Skill</label>
								</div>
								<div class="tr weapon_firstRow">
									<input type="text" name="weapons[<?=$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
									<input type="text" name="weapons[<?=$weaponNum?>][skill]" value="<?=$weaponInfo['skill']?>" class="weapons_skill lrBuffer">
								</div>
								<div class="tr labelTR weapon_secondRow">
									<label class="shortText alignCenter lrBuffer">Damage</label>
									<label class="shortText alignCenter lrBuffer">Range</label>
									<label class="shortText alignCenter lrBuffer">Critical</label>
								</div>
								<div class="tr weapon_secondRow">
									<input type="text" name="weapons[<?=$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
									<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortText lrBuffer">
									<input type="text" name="weapons[<?=$weaponNum?>][critical]" value="<?=$weaponInfo['critical']?>" class="weapon_crit shortText lrBuffer">
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
								<label class="weapons_skill alignCenter lrBuffer">Skill</label>
							</div>
							<div class="tr">
								<span class="weapon_name medText lrBuffer"><?=$weapon['name']?></span>
								<span class="weapons_skill lrBuffer alignCenter"><?=$weapon['skill']?></span>
							</div>
							<div class="tr labelTR weapon_secondRow">
								<label class="shortText alignCenter lrBuffer">Damage</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Critical</label>
							</div>
							<div class="tr weapon_secondRow">
								<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weapon['damage']?></span>
								<span class="weapon_range shortText lrBuffer alignCenter"><?=$weapon['range']?></span>
								<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['critical']?></span>
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

		public function setItems($items) {
			$this->items = sanitizeString($items);
		}

		public function getItems($pr = false) {
			$items = $this->items;
			if ($pr) 
				$items = printReady($items);
			return $items;
		}

		public function setMotivations($motivations) {
			$this->motivations = sanitizeString($motivations);
		}

		public function getMotivations($pr = false) {
			$motivations = $this->motivations;
			if ($pr) 
				$motivations = printReady($motivations);
			return $motivations;
		}

		public function setObligations($obligations) {
			$this->obligations = sanitizeString($obligations);
		}

		public function getObligations($pr = false) {
			$obligations = $this->obligations;
			if ($pr) 
				$obligations = printReady($obligations);
			return $obligations;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				$this->setSpecies($data['species']);
				$this->setCareer($data['career']);
				$this->setSpecialization($data['specialization']);
				$this->setXP('total', $data['xp']['total']);
				$this->setXP('spent', $data['xp']['spent']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['defenses'] as $type => $value) $this->setDefense($type, $value);
				foreach ($data['hp'] as $type => $value) $this->setHP($type, $value);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillInfo) {
					$this->addSkill($skillInfo);
				} }

				$this->clearVar('talents');
				if (sizeof($data['talents'])) { foreach ($data['talents'] as $talentInfo) {
					$this->addTalent($talentInfo);
				} }

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

				$this->setItems($data['items']);
				$this->setMotivations($data['motivations']);
				$this->setObligations($data['obligations']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>