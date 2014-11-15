<?
	class savageworldsCharacter extends Character {
		const SYSTEM = 'fate';

		protected $fatePoints = 3;
		protected $refresh = 0;
		protected $highConcept = '';
		protected $trouble = '';
		protected $aspects = array();
		protected $skills = array();
		protected $extras = '';
		protected $stunts = array();
		protected $stress = array('physical' = array('total' => 2, 'current' => 0), 'mental' = array('total' => 2, 'current' => 0));
		protected $consequences = array();

		public function setFatePoints($fatePoints) {
			$this->fatePoints = intval($fatePoints);
		}

		public function getFatePoints() {
			return $this->fatePoints;
		}

		public function setRefresh($refresh) {
			$this->refresh = intval($refresh);
		}

		public function getRefresh() {
			return $this->refresh;
		}

		public function setHighConcept($highConcept) {
			$this->highConcept = $highConcept;
		}

		public function getHighConcept() {
			return $this->highConcept;
		}

		public function setTrouble($trouble) {
			$this->trouble = $trouble;
		}

		public function getTrouble() {
			return $this->trouble;
		}

		public function addAspect($aspect) {
			if (strlen($aspect) $this->aspects[] = $aspect;
		}

		public function aspectEditFormat($key = 1, $aspect = null) {
?>
									<div class="aspect clearfix">
										<input type="text" name="aspects[<?=$key?>]" value="<?=$aspect?>" class="aspectName placeholder" data-placeholder="Aspect Name">
										<div class="remove"><a href="" class="sprite cross small"></a></div>
									</div>
<?
		}

		public function showAspectsEdit() {
			if (sizeof($this->aspects)) { foreach ($this->aspects as $key => $aspect) {
				$this->aspectEditFormat($key, $aspect);
			} }
		}

		public function displayAspects() {
			if ($this->aspects) { foreach ($this->aspects as $aspect) {
?>
								<div class="aspect"><?=$aspect?></div>
<?
			} }
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