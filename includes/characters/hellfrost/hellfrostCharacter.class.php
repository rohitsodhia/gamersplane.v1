<?
	class hellfrostCharacter extends savageworldsCharacter {
		const SYSTEM = 'hellfrost';

		protected $spells = '';

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			$skillInfo = array('skillID' => $skillID, 'name' => $name);
			try {
				$addSkill = $mysql->query("INSERT INTO ".$this::SYSTEM."_skills (characterID, skillID, diceType) VALUES ({$this->characterID}, $skillID, 4)");
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
										<a href="" class="sprite cross small remove"></a>
									</div>
<?
		}

		public function showSkillsEdit() {
			if ($this->skills == null) {
				$this->skills = array();

				global $mysql;
				$system = $this::SYSTEM;
				$skills = $mysql->query("SELECT s.skillID, sl.name, s.diceType FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
				foreach ($skills as $skill) $this->skills[] = $skill;
			}

			if (sizeof($this->skills)) { foreach ($this->skills as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} }
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".$this::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills() {
			if ($this->skills == null) {
				$this->skills = array();

				global $mysql;
				$system = $this::SYSTEM;
				$skills = $mysql->query("SELECT s.skillID, sl.name, s.diceType FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
				foreach ($skills as $skill) $this->skills[] = $skill;
			}

			if ($this->skills) { foreach ($this->skills as $skill) {
?>
								<div id="skill_<?=$skill['skillID']?>" class="skill clearfix">
									<div class="skillName"><?=$skill['name']?></div>
									<div class="diceType">d<?=$skill['diceType']?></div>
								</div>
<?
			} }
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
			$system = $this::SYSTEM;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['traits'] as $trait => $value) $this->setTrait($trait, $value);
				foreach ($data['derivedTraits'] as $trait => $value) $this->setDerivedTrait($trait, $value);
				$updateSkill = $mysql->prepare("UPDATE {$system}_skills SET diceType = :diceType WHERE characterID = {$this->characterID} AND skillID = :skillID");
				foreach ($data['skills'] as $skillID => $skillInfo) {
					$updateSkill->bindValue(':diceType', $skillInfo['diceType']);
					$updateSkill->bindValue(':skillID', $skillID);
					$updateSkill->execute();
				}
				$this->setEdgesHindrances($data['edge_hind']);
				$this->setWounds($data['wounds']);
				$this->setFatigue($data['fatigue']);
				$this->setInjuries($data['injuries']);
				$this->setWeapons($data['weapons']);
				$this->setSpells($data['spells']);
				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save(true);
		}
	}
?>