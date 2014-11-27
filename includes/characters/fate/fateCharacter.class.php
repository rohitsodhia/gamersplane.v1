<?
	class fateCharacter extends Character {
		const SYSTEM = 'fate';

		protected $fatePoints = array('current' => 3, 'refresh' => 0);
		protected $highConcept = '';
		protected $trouble = '';
		protected $aspects = array();
		protected $skills = array();
		protected $extras = '';
		protected $stunts = array();
		protected $stress = array('physical' => array('total' => 2, 'current' => 0), 'mental' => array('total' => 2, 'current' => 0));
		protected $consequences;

		public function setFatePoints($key, $value) {
			if (array_key_exists($key, $this->fatePoints)) $this->fatePoints[$key] = intval($value);
			else return false;
		}

		public function getFatePoints($key = null) {
			if ($key == null) return $this->fatePoints;
			elseif (array_key_exists($key, $this->fatePoints)) return $this->fatePoints[$key];
			else return false;
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
			if (strlen($aspect)) $this->aspects[] = $aspect;
		}

		public function aspectEditFormat($key = 1, $aspect = null) {
?>
								<div class="aspect item tr clearfix">
									<input type="text" name="aspects[<?=$key?>]" value="<?=$aspect?>" class="aspectName placeholder width5 alignLeft" data-placeholder="Aspect Name">
									<a href="" class="remove sprite cross"></a>
								</div>
<?
		}

		public function showAspectsEdit() {
			if (sizeof($this->aspects)) { foreach ($this->aspects as $key => $aspect) {
				$this->aspectEditFormat($key, $aspect);
			} } else $this->aspectEditFormat();
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
									<div class="skill tr clearfix">
										<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="name placeholder width4" data-placeholder="Skill Name">
										<span class="rating"><select name="skills[<?=$key?>][rating]">
<?			for ($count = -2; $count <= 8; $count++) { ?>
											<option<?=$skillInfo['rating'] == $count?' selected="selected"':''?>><?=showSign($count)?></option>
<?			} ?>
										</select></span>
										<a href="" class="remove sprite cross"></a>
									</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skillInfo) {
				$this->skillEditFormat($key, $skillInfo);
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
								<div class="skill clearfix"><?=$skill['name']?> (<span class="rating"><?=showSign($skill['rating'])?></span>)</div>
<?
			} }
		}

		public function setExtras($extras) {
			$this->extras = $extras;
		}

		public function getExtras() {
			return $this->extras;
		}

		public static function stuntEditFormat($key = 1, $stuntInfo = null) {
			if ($stuntInfo == null) $stuntInfo = array('name' => '', 'notes' => '');
?>
									<div class="stunt tr clearfix">
										<input type="text" name="stunts[<?=$key?>][name]" value="<?=$stuntInfo['name']?>" class="name placeholder" data-placeholder="Stunt Name">
										<a href="" class="notesLink">Notes</a>
										<a href="" class="remove sprite cross"></a>
										<textarea name="stunts[<?=$key?>][notes]"><?=$stuntInfo['notes']?></textarea>
									</div>
<?
		}

		public function showStuntsEdit() {
			if (sizeof($this->stunts)) { foreach ($this->stunts as $key => $stunt) {
				$this->stuntEditFormat($key + 1, $stunt);
			} } else $this->stuntEditFormat();
		}

		public function displayStunts() {
			if ($this->stunts) { foreach ($this->stunts as $stunt) { ?>
					<div class="stunt tr clearfix">
						<span class="name"><?=$stunt['name']?></span>
<?	if (strlen($stunt['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$stunt['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noStunts\">This character currently has no stunts/abilities.</p>\n";
		}
		
		public function addStunt($stunt) {
			if (strlen($stunt['name'])) {
				newItemized('stunt', $stunt['name'], $this::SYSTEM);
				$this->stunts[] = $stunt;
			}
		}

		public function setInjuries($injuries) {
			$this->injuries = $injuries;
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setStress($type, $key, $value) {
			if (in_array($type, array('physical', 'mental')) && (($key == 'total' && intval($value) >= 2) || ($key == 'current' && intval($value) >= 0)))
				$this->stress[$type][$key] = intval($value);
			else return false;
		}

		public function getStress($type = null, $key = null) {
			if ($type == null) return $this->stress;
			elseif (in_array($type, array('physical', 'mental'))) {
				if ($key == null) return $this->stress[$type];
				elseif (in_array($key, array('total', 'current'))) return $this->stress[$type][$key];
			} else return false;
		}

		public function setConsequences($consequences) {
			$this->consequences = $consequences;
		}

		public function getConsequences($level = null) {
			return $this->consequences;
		}

		public function save($bypass = false) {
			global $mysql;
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create']) && !$bypass) {
				$this->setName($data['name']);
				$this->setFatePoints('current', $data['fatePoints']['current']);
				$this->setFatePoints('refresh', $data['fatePoints']['refresh']);

				$this->setHighConcept($data['highConcept']);
				$this->setTrouble($data['trouble']);

				$this->clearVar('aspects');
				if (sizeof($data['aspects'])) {
					foreach ($data['aspects'] as $aspect) $this->addAspect($aspect);
				}

				$this->clearVar('skills');
				if (sizeof($data['skills'])) {
					foreach ($data['skills'] as $skill) $this->addSkill($skill);
				}

				$this->setExtras($data['extras']);
				$this->clearVar('stunts');
				if (sizeof($data['stunts'])) { foreach ($data['stunts'] as $stuntInfo) {
					$this->addStunt($stuntInfo);
				} }

				$this->setStress('physical', 'total', $data['stress']['physical']['total']);
				$this->setStress('physical', 'current', $data['stress']['physical']['current']);
				$this->setStress('mental', 'total', $data['stress']['mental']['total']);
				$this->setStress('mental', 'current', $data['stress']['mental']['current']);

				$this->setConsequences($data['consequences']);

				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>