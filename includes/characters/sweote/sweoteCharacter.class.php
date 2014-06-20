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
			$this->species = $species;
		}

		public function getSpecies() {
			return $this->species;
		}

		public function setCareer($career) {
			$this->career = $career;
		}

		public function getCareer() {
			return $this->career;
		}

		public function setSpecialization($specialization) {
			$this->specialization = $specialization;
		}

		public function getSpecialization() {
			return $this->specialization;
		}

		public function setXP($type, $value = '') {
			if (in_array($type, array_keys($this->xp))) {
				$value = intval($value);
				if ($value >= 0) $this->xp[$type] = $value;
			} else return FALSE;
		}
		
		public function getXP($type = NULL) {
			if ($type == NULL) return $this->xp;
			elseif (in_array($type, array_keys($this->xp))) return $this->xp[$type];
			else return FALSE;
		}

		public function setStat($stat, $value = '') {
			if (in_array($stat, array_keys($this->stats))) {
				$value = intval($value);
				if ($value > 0) $this->stats[$stat] = $value;
			} else return FALSE;
		}
		
		public function getStat($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (in_array($stat, array_keys($this->stats))) return $this->stats[$stat];
			else return FALSE;
		}

		public function setDefense($defense, $value = '') {
			if (in_array($defense, array_keys($this->defenses))) {
				$value = intval($value);
				if ($value >= 0) $this->defenses[$defense] = $value;
			} else return FALSE;
		}
		
		public function getDefense($defense = NULL) {
			if ($defense == NULL) return $this->defenses;
			elseif (in_array($defense, array_keys($this->defenses))) return $this->defenses[$defense];
			else return FALSE;
		}

		public function setHP($type, $value = '') {
			if (in_array($type, array_keys($this->hp))) {
				$value = intval($value);
				if ($value > 0) $this->hp[$type] = $value;
			} else return FALSE;
		}
		
		public function getHP($type = NULL) {
			if ($type == NULL) return $this->hp;
			elseif (in_array($type, array_keys($this->hp))) return $this->hp[$type];
			else return FALSE;
		}

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			if (array_key_exists($post['stat'], $this->stats)) $stat = sanitizeString($_POST['stat']);
			else exit;
			$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'rank' => 0);
			$addSkill = $mysql->query("INSERT INTO sweote_skills (characterID, skillID, stat) VALUES ($characterID, $skillID, '$stat')");
			if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo);
		}

		public function updateSkill($skillID, $skillInfo) {
			$updateSkill = $mysql->prepare("UPDATE sweote_skills SET rank = :rank, career = :career WHERE characterID = $characterID AND skillID = :skillID");
			$updateSkill->bindValue(':rank', intval($skillInfo['rank']));
			$updateSkill->bindValue(':career', isset($skillInfo['career'])?1:0);
			$updateSkill->bindValue(':skillID', $skillID);
			$updateSkill->execute();
		}

		public function skillEditFormat($skillInfo = NULL, $statBonus = NULL) {
?>
							<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
								<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
								<span class="skill_stat textLabel lrBuffer alignCenter"><?=sweote_consts::getStatNames($skillInfo['stat'])?></span>
								<input type="text" name="skills[<?=$skillInfo['skillID']?>][rank]" value="<?=$skillInfo['rank']?>" class="skill_rank shortNum lrBuffer">
								<span class="skill_career shortNum lrBuffer alignCenter"><input type="checkbox" name="skills[<?=$skillInfo['skillID']?>][career]" value="<?=$skillInfo['career']?>"></span>
								<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
							</div>
<?
		}

		public function showSkillsEdit() {
			global $mysql;

			$system = $this::SYSTEM;
			$skills = $mysql->query('SELECT ss.skillID, sl.name, ss.stat, ss.rank, ss.career FROM sweote_skills ss INNER JOIN skillsList sl USING (skillID) WHERE ss.characterID = '.$this->characterID.' ORDER BY sl.name');
			if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?
			}
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".$this::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills() {
			global $mysql;
			$skills = $mysql->query('SELECT s.skillID, sl.name, s.stat, s.rank, s.career FROM '.$this::SYSTEM.'_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = '.$this->characterID.' ORDER BY sl.name');
			if ($skills->rowCount()) { foreach ($skills as $skill) {
?>
						<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
							<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
							<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
							<span class="skill_rank alignCenter shortNum lrBuffer"><?=$skill['rank']?></span>
							<span class="skill_career alignCenter shortNum lrBuffer"><?=$skill['career']?'<img src="/images/check.png">':''?></span>
						</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addTalent($name) {
			global $mysql;

			$talent = $mysql->prepare('SELECT talentID FROM sweote_talentsList WHERE searchName = :searchName');
			$talent->execute(array(':searchName' => sanitizeString($name, 'search_format')));
			if ($talent->rowCount()) $talentID = $talent->fetchColumn();
			else {
				$addNewTalent = $mysql->prepare('INSERT INTO sweote_talentsList (name, searchName, userDefined) VALUES (:name, :searchName, :userID)');
				$addNewTalent->execute(array(':name' => $name, ':searchName' => sanitizeString($name, 'search_format'), ':userID' => $userID));
				$talentID = $mysql->lastInsertId();
			}
			$talentInfo = array('talentID' => $talentID, 'name' => $name);
			$addTalent = $mysql->query("INSERT INTO sweote_talents (characterID, talentID) VALUES ($characterID, $talentID)");
			if ($addTalent->rowCount()) $this->talentEditFormat($talentInfo);
		}

		public function talentEditFormat($talentInfo) {
?>
						<div id="talent_<?=$talentInfo['talentID']?>" class="talent clearfix">
							<span class="talent_name textLabel"><?=mb_convert_case($talentInfo['name'], MB_CASE_TITLE)?></span>
							<a href="/characters/<?=$this::SYSTEM?>/<?=$this->characterID?>/editTalentNotes/<?=$talentInfo['talentID']?>" id="talentNotesLink_<?=$talentInfo['talentID']?>" class="talent_notesLink">Notes</a>
							<input type="image" name="talentRemove_<?=$talentInfo['talentID']?>" src="/images/cross.png" value="<?=$talentInfo['talentID']?>" class="talent_remove lrBuffer">
						</div>
<?
		}

		public function showTalentsEdit() {
			global $mysql;

			$talents = $mysql->query("SELECT tl.talentID, tl.name FROM sweote_talents t INNER JOIN sweote_talentsList tl USING (talentID) WHERE t.characterID = {$this->characterID} ORDER BY tl.name");
			if ($talents->rowCount()) { foreach ($talents as $talentInfo) {
				$this->talentEditFormat($talentInfo);
			} } else { ?>
					<p id="noTalents">This character currently has no talents.</p>
<?
			}
		}

		public function removeTalent($talentID) {
			global $mysql;

			$removeTalent = $mysql->query("DELETE FROM sweote_talents WHERE characterID = {$this->characterID} AND talentID = $talentID");
			if ($removeTalent->rowCount()) echo 1;
			else echo 0;
		}

		public function displayTalents() {
			global $mysql;

			$talents = $mysql->query("SELECT t.talentID, tl.name, t.notes FROM sweote_talents t INNER JOIN sweote_talentsList tl USING (talentID) WHERE t.characterID = {$this->characterID} ORDER BY tl.name");
			if ($talents->rowCount()) { foreach ($talents as $talent) { ?>
					<div id="talent_<?=$talent['talentID']?>" class="talent tr clearfix">
						<span class="talent_name"><?=mb_convert_case($talent['name'], MB_CASE_TITLE)?></span>
						<a href="/characters/<?=$this::SYSTEM?>/<?=$this->characterID?>/talentNotes/<?=$talent['talentID']?>" class="talent_notesLink">Notes</a>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noTalents\">This character currently has no talents.</p>\n";
		}
		
		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['skill']) && strlen($weapon['damage'])) $this->weapons[] = $weapon;
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
			$this->items = $items;
		}

		public function getItems() {
			return $this->items;
		}

		public function setMotivations($motivations) {
			$this->motivations = $motivations;
		}

		public function getMotivations() {
			return $this->motivations;
		}

		public function setObligations($obligations) {
			$this->obligations = $obligations;
		}

		public function getObligations() {
			return $this->obligations;
		}

		public function save() {
			$data = $_POST;

			$this->setName($data['name']);
			$this->setSpecies($data['species']);
			$this->setCareer($data['career']);
			$this->setSpecialization($data['specialization']);
			$this->setXP('total', $data['xp']['total']);
			$this->setXP('spent', $data['xp']['spent']);

			foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
			foreach ($data['defenses'] as $type => $value) $this->setDefense($type, $value);
			foreach ($data['hp'] as $type => $value) $this->setHP($type, $value);

			if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillID => $skillInfo) {
				$this->updateSkill($skillID, $skillInfo);
			} }

			$this->clearVar('weapons');
			foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

			$this->setItems($data['items']);
			$this->setMotivations($data['motivations']);
			$this->setObligations($data['obligations']);
			$this->setNotes($data['notes']);

			parent::save();
		}
	}
?>