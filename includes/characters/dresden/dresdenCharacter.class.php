<?
	class dresdenCharacter extends fateCharacter {
		const SYSTEM = 'dresden';

		protected $template = '';
		protected $fatePoints = ['current' => 3, 'refresh' => 0, 'adjustedRefresh' => 0];
		protected $powerLevel = '';
		protected $phases = [];
		protected $skillCap = 0;
		protected $skillPoints = ['spent' => 0, 'available' => 0];
		protected $maxStress = 8;
		protected $stresses = [
			'physical' => [1 => 0, 0],
			'mental' => [1 => 0, 0],
			'social' => [1 => 0, 0]
		];

		public function setTemplate($template) {
			$this->template = sanitizeString($template);
		}

		public function getTemplate() {
			return $this->template;
		}

		public function setPowerLevel($powerLevel) {
			$this->powerLevel = sanitizeString($powerLevel);
		}

		public function getPowerLevel() {
			return $this->powerLevel;
		}

		public function setPhase($phase, $key, $value) {
			$phase = intval($phase);
			if ($phase >= 1 && $phase <= 5 && in_array($key, ['aspect', 'events'])) {
				$this->phases[$phase][$key] = $value;
			}
		}

		public function getPhase($phase = null, $key = null) {
			if ($phase == null) {
				return $this->phases;
			} else {
				$phase = (int) $phase;
				if ($phase < 1 || $phase > 5) {
					return false;
				}

				if ($key == null) {
					return $this->phases[$phase];
				} elseif (in_array($key, ['aspect', 'events'])) {
					return $this->phases[$phase][$key];
				} else {
					return false;
				}
			}
		}

		public function setSkillCap($skillCap) {
			$this->skillCap = (int) $skillCap;
		}

		public function getSkillCap() {
			return $this->skillCap;
		}

		public function setSkillPoints($key, $value) {
			if (array_key_exists($key, $this->skillPoints)) {
				$this->skillPoints[$key] = (int) $value;
			} else
				return false;
		}

		public function getSkillPoints($key = null) {
			if ($key == null) {
				return $this->skillPoints;
			} elseif (array_key_exists($key, $this->skillPoints)) {
				return $this->skillPoints[$key];
			} else {
				return false;
			}
		}

		public static function stuntEditFormat($key = 1, $stuntInfo = null) {
			if ($stuntInfo == null) {
				$stuntInfo = ['name' => '', 'cost' => 0, 'notes' => ''];
			}
?>
									<div class="stunt tr clearfix">
										<input type="text" name="stunts[<?=$key?>][cost]" value="<?=$stuntInfo['cost']?>" class="cost">
										<input type="text" name="stunts[<?=$key?>][name]" value="<?=$stuntInfo['name']?>" class="name placeholder" data-placeholder="Stunt Name">
										<a href="" class="notesLink">Notes</a>
										<a href="" class="remove sprite cross"></a>
										<textarea name="stunts[<?=$key?>][notes]"><?=$stuntInfo['notes']?></textarea>
									</div>
<?
		}

		public function displayStunts() {
			if ($this->stunts) {
				foreach ($this->stunts as $stunt) {
?>
					<div class="stunt tr clearfix">
						<span class="cost"><?=$stunt['cost']?></span>
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
				$stunt['cost'] = intval($stunt['cost']);
				$this->stunts[] = $stunt;
			}
		}

		public function save($bypass = false) {
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create']) && !$bypass) {
				$this->setName($data['name']);
				$this->setTemplate($data['template']);
				$this->setPowerLevel($data['powerLevel']);
				$this->setFatePoints('current', $data['fatePoints']['current']);
				$this->setFatePoints('refresh', $data['fatePoints']['refresh']);
				$this->setFatePoints('adjustedRefresh', $data['fatePoints']['adjustedRefresh']);
				$this->setHighConcept($data['highConcept']);
				$this->setTrouble($data['trouble']);

				for ($count = 1; $count <= 5; $count++) {
					$this->setPhase($count, 'aspect', $data['phases'][$count]['aspect']);
					$this->setPhase($count, 'events', $data['phases'][$count]['events']);
				}

				$this->clearVar('aspects');
				if (sizeof($data['aspects'])) {
					foreach ($data['aspects'] as $aspect) $this->addAspect($aspect);
				}

				$this->clearVar('stunts');
				if (sizeof($data['stunts'])) {
					foreach ($data['stunts'] as $stuntInfo) {
						$this->addStunt($stuntInfo);
					}
				}

				$this->setSkillCap($data['skillCap']);
				$this->setSkillPoints('spent', $data['skillPoints']['spent']);
				$this->setSkillPoints('available', $data['skillPoints']['available']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) {
					foreach ($data['skills'] as $skill) {
						$this->addSkill($skill);
					}
				}

				foreach ($this->stresses as $type => $stress) {
					$this->setStressBoxes($type, $data['stressCap'][$type]);
					for ($count = 0; $count <= $data['stressCap'][$type]; $count++)
						if (isset($data['stresses'][$type][$count])) {
							$this->setStress($type, $count, 1);
						}
				}

				$this->setConsequences($data['consequences']);

				$this->setNotes($data['notes']);
			}

			parent::save($bypass);
		}
	}
?>
