<?
	class hellfrostCharacter extends Character {
		const SYSTEM = 'hellfrost';

		protected $traits = array('agi' => 4, 'sma' => 4, 'spi' => 4, 'str' => 4, 'vig' => 4
		);
		protected $skills = null;
		protected $derivedTraits = array('pace' => 0, 'parry' => 0, 'charisma' => 0, 'toughness' => 0);
		protected $edgesHindrances = '';
		protected $wounds = 0;
		protected $fatigue = 0;
		protected $injuries = '';
		protected $weapons = '';
		protected $equipment = '';
//		protected $advances = '';

		protected $linkedTables = array('skills');

		public function __construct($characterID, $userID = NULL) {
			parent::__construct($characterID, $userID);

			$this->mongoIgnore['save'][] = 'skills';
		}

		public function setTrait($trait, $value = null) {
			if (array_key_exists($trait, $this->traits)) $this->traits[$trait] = intval($value);
			else return FALSE;
		}
		
		public function getTraits($trait = NULL) {
			if ($trait == NULL) return $this->traits;
			elseif (array_key_exists($trait, $this->traits)) return $this->traits[$trait];
			else return FALSE;
		}

		public function setDerivedTrait($trait, $value = null) {
			if (array_key_exists($trait, $this->derivedTraits)) $this->derivedTraits[$trait] = intval($value);
			else return FALSE;
		}
		
		public function getDerivedTraits($trait = NULL) {
			if ($trait == NULL) return $this->derivedTraits;
			elseif (array_key_exists($trait, $this->derivedTraits)) return $this->derivedTraits[$trait];
			else return FALSE;
		}

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			$skillInfo = array('skillID' => $skillID, 'name' => $name, 'trait' => $post['trait']);
			if (!array_key_exists($skillInfo['trait'], $this->traits)) return false;
			try {
				$addSkill = $mysql->query("INSERT INTO ".$this::SYSTEM."_skills (characterID, skillID, diceType, trait) VALUES ({$this->characterID}, $skillID, 4, '{$skillInfo['trait']}')");
				if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo, 'trait');
			} catch (Exception $e) { var_dump($e); }
		}

		public function updateSkill($skillID, $skillInfo) {
			global $mysql;

			$updateSkill = $mysql->prepare("UPDATE ".$this::SYSTEM."_skills SET diceType = :diceType WHERE characterID = :characterID AND skillID = :skillID");
			$updateSkill->bindValue(':diceType', intval($skillInfo['diceType']));
			$updateSkill->bindValue(':characterID', $this->characterID);
			$updateSkill->bindValue(':skillID', $skillID);
			$updateSkill->execute();
		}

		public function skillEditFormat($skillInfo = NULL) {
?>
									<div id="skill_<?=$skillInfo['skillID']?>" class="skill clearfix">
										<div class="skillName"><?=$skillInfo['name']?></div>
										<input type="hidden" name="skills[<?=$skillInfo['skillID']?>][trait]" value="<?=$skillInfo['trait']?>">
										<div class="diceSelect"><span>d</span> <select name="skills[<?=$skillInfo['skillID']?>][diceType]" class="diceType">
<?			foreach (array(4, 6, 8, 10, 12) as $dCount) { ?>
											<option<?=$skillInfo['diceType'] == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?			} ?>
										</select></div>
										<div class="remove"><a href="" class="sprite cross small"></a></div>
									</div>
<?
		}

		public function showSkillsEdit($trait) {
			if ($this->skills == null) {
				$this->skills = array();

				global $mysql;
				$system = $this::SYSTEM;
				$skills = $mysql->query("SELECT s.skillID, sl.name, s.trait, s.diceType FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
				foreach ($skills as $skill) $this->skills[$skill['trait']][] = $skill;
			}

			if (sizeof($this->skills[$trait])) { foreach ($this->skills[$trait] as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} }
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".$this::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills($trait) {
			if ($this->skills == null) {
				$this->skills = array();

				global $mysql;
				$system = $this::SYSTEM;
				$skills = $mysql->query("SELECT s.skillID, sl.name, s.trait, s.diceType FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
				foreach ($skills as $skill) $this->skills[$skill['trait']][] = $skill;
			}

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

		public function save() {
			global $mysql;
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['traits'] as $trait => $value) $this->setTrait($trait, $value);
				foreach ($data['derivedTraits'] as $trait => $value) $this->setDerivedTrait($trait, $value);
				$updateSkill = $mysql->prepare("UPDATE {$system}_skills SET diceType = :diceType WHERE characterID = {$this->characterID} AND skillID = :skillID AND trait = :trait");
				foreach ($data['skills'] as $skillID => $skillInfo) {
					$updateSkill->bindValue(':diceType', $skillInfo['diceType']);
					$updateSkill->bindValue(':skillID', $skillID);
					$updateSkill->bindValue(':trait', $skillInfo['trait']);
					$updateSkill->execute();
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