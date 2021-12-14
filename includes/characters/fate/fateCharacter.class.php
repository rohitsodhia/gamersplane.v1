<?
	class fateCharacter extends Character {
		const SYSTEM = 'fate';

		protected $fatePoints = ['current' => 3, 'refresh' => 0];
		protected $highConcept = '';
		protected $trouble = '';
		protected $aspects = [];
		protected $skills = [];
		protected $extras = '';
		protected $stunts = [];
		protected $maxStress = 4;
		protected $stresses = [
			'physical' => [1 => 0, 0],
			'mental' => [1 => 0, 0]
		];
		protected $consequences;

		public function setFatePoints($key, $value) {
			if (array_key_exists($key, $this->fatePoints)) {
				$this->fatePoints[$key] = (int) $value;
			} else {
				return false;
			}
		}

		public function getFatePoints($key = null) {
			if ($key == null) {
				return $this->fatePoints;
			} elseif (array_key_exists($key, $this->fatePoints)) {
				return $this->fatePoints[$key];
			} else {
				return false;
			}
		}

		public function setHighConcept($highConcept) {
			$this->highConcept = sanitizeString($highConcept);
		}

		public function getHighConcept() {
			return $this->highConcept;
		}

		public function setTrouble($trouble) {
			$this->trouble = sanitizeString($trouble);
		}

		public function getTrouble() {
			return $this->trouble;
		}

		public function addAspect($aspect) {
			if (strlen($aspect)) {
				$this->aspects[] = sanitizeString($aspect);
			}
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
			if (sizeof($this->aspects)) {
				foreach ($this->aspects as $key => $aspect) {
					$this->aspectEditFormat($key, $aspect);
				}
			} else {
				$this->aspectEditFormat();
			}
		}

		public function displayAspects() {
			if ($this->aspects) {
				foreach ($this->aspects as $aspect) {
?>
								<div class="aspect"><?=$aspect?></div>
<?
				}
			}
		}

		public function addSkill($skill) {
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$this->skills[] = ['name' => sanitizeString($skill['name']), 'rating' => (int) $skill['rating']];
			}
		}

		public function skillEditFormat($key = 0, $skillInfo = null) {
			if ($skillInfo == null) {
				$skillInfo = ['name' => '', 'rating' => 0];
			}
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
			if (sizeof($this->skills)) {
				foreach ($this->skills as $key => $skillInfo) {
					$this->skillEditFormat($key, $skillInfo);
				}
			} else {
				$this->skillEditFormat();
			}
		}

		public function displaySkills() {
			if ($this->skills) {
				foreach ($this->skills as $skill) {
?>
								<div class="skill clearfix"><?=$skill['name']?> (<span class="rating"><?=showSign($skill['rating'])?></span>)</div>
<?
				}
			}
		}

		public function setExtras($extras) {
			$this->extras = sanitizeString($extras);
		}

		public function getExtras() {
			return $this->extras;
		}

		public static function stuntEditFormat($key = 0, $stuntInfo = null) {
			if ($stuntInfo == null) {
				$stuntInfo = ['name' => '', 'notes' => ''];
			}
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
			if (sizeof($this->stunts)) {
				foreach ($this->stunts as $key => $stunt) {
					$this->stuntEditFormat($key + 1, $stunt);
				}
			} else {
				$this->stuntEditFormat();
			}
		}

		public function displayStunts() {
			if ($this->stunts) {
				foreach ($this->stunts as $stunt) {
?>
					<div class="stunt tr clearfix">
						<span class="name"><?=$stunt['name']?></span>
<?				if (strlen($stunt['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$stunt['notes']?></div>
<?				} ?>
					</div>
<?
				}
			} else {
				echo "\t\t\t\t\t<p id=\"noStunts\">This character currently has no stunts/abilities.</p>\n";
			}
		}

		public function addStunt($stunt) {
			if (strlen($stunt['name'])) {
				newItemized('stunt', $stunt['name'], $this::SYSTEM);
				$stunt['name'] = sanitizeString($stunt['name']);
				$stunt['notes'] = sanitizeString($stunt['notes']);
				$this->stunts[] = $stunt;
			}
		}

		public function setInjuries($injuries) {
			$this->injuries = sanitizeString($injuries);
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setStressBoxes($type, $numBoxes = 2) {
			$numBoxes = (int) $numBoxes;
			if (array_key_exists($type, $this->stresses) && $numBoxes > 2 && $numBoxes <= $this->maxStress) {
				$this->stresses[$type] = [];
				for ($count = 1; $count <= $numBoxes; $count++) {
					$this->stresses[$type][$count] = 0;
				}
			}
		}

		public function setStress($type, $key, $value = 0) {
			if (array_key_exists($type, $this->stresses) && array_key_exists($key, $this->stresses[$type]) && (in_array((int) $value, [0, 1]))) {
				$this->stresses[$type][$key] = (int) $value;
			} else {
				return false;
			}
		}

		public function getStress($type = null, $key = null) {
			if ($type == null) {
				return $this->stresses;
			} elseif (array_key_exists($type, $this->stresses)) {
				if ($key == null) {
					return $this->stresses[$type];
				} elseif (array_key_exists($key, $key, $this->stresses[$type])) {
					return $this->stresses[$type][$key];
				}
			} else {
				return false;
			}
		}

		public function setConsequences($consequences) {
			$this->consequences = sanitizeString($consequences);
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
				if ($data['aspects'] && sizeof($data['aspects'])) {
					foreach ($data['aspects'] as $aspect) $this->addAspect($aspect);
				}

				$this->clearVar('skills');
				if ($data['skills'] && sizeof($data['skills'])) {
					foreach ($data['skills'] as $skill) $this->addSkill($skill);
				}

				$this->setExtras($data['extras']);
				$this->clearVar('stunts');
				if ($data['stunts'] && sizeof($data['stunts'])) {
					foreach ($data['stunts'] as $stuntInfo) {
						$this->addStunt($stuntInfo);
					}
				}

				foreach ($this->stresses as $type => $stress) {
					$this->setStressBoxes($type, $data['stressCap'][$type]);
					for ($count = 0; $count <= $data['stressCap'][$type]; $count++) {
						if (isset($data['stresses'][$type][$count])) {
							$this->setStress($type, $count, 1);
						}
					}
				}

				$this->setConsequences($data['consequences']);

				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
