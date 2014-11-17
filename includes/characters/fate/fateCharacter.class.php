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
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$this->skills[] = array('name' => $skill['name'], 'rating' => intval($skill['rating']));
			}
		}

		public function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) $skillInfo = array('name' => '', 'rating' => 0);
?>
									<div class="skill clearfix">
										<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skillName placeholder" data-placeholder="Skill Name">
										<div class="rating"><select name="skills[<?=$key?>][rating]">
<?			for ($count = -2; $count <= 8; $count++) { ?>
											<option<?=$skillInfo['rating'] == $count?' selected="selected"':''?>><?=showSign($count)?></option>
<?			} ?>
										</select></div>
										<div class="remove"><a href="" class="sprite cross small"></a></div>
									</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skillInfo) {
				$this->skillEditFormat($key, array_merge(array('trait' => $trait), $skillInfo));
			} }
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
								<div class="skill clearfix"><?=$skill['name']?> (<span class="rating"><?=showSign($skill['name'])?></span>)</div>
									<div class="diceType">d<?=$skill['diceType']?></div>
								</div>
<?
			} }
		}

		public function setExtras($extras) {
			$this->extras = $extras;
		}

		public function getExtras() {
			return $this->extras;
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