<?
	class hellfrostCharacter extends savageworldsCharacter {
		const SYSTEM = 'hellfrost';

		protected $spells = '';

		public function skillEditFormat($key = null, $skillInfo = NULL) {
			if ($key == null) $key = 1;
			if ($skillInfo == null) $skillInfo = array('name' => '', 'diceType' => '');
?>
							<div class="skill clearfix">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skillName placeholder" data-placeholder="Skill Name">
								<div class="diceSelect"><span>d</span> <select name="skills[<?=$key?>][diceType]" class="diceType">
<?			foreach (array(4, 6, 8, 10, 12) as $dCount) { ?>
									<option<?=$skillInfo['diceType'] == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?			} ?>
								</select></div>
								<a href="" class="sprite cross small remove"></a>
							</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill);
			} }
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
								<div class="skill clearfix">
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