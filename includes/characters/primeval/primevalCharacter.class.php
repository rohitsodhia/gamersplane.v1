<?
	class primevalCharacter extends Character {
		const SYSTEM = 'primeval';

		protected $attributes = [
			'awareness' => ['starting' => 0, 'current' => 0],
			'coordination' => ['starting' => 0, 'current' => 0],
			'ingenuity' => ['starting' => 0, 'current' => 0],
			'presence' => ['starting' => 0, 'current' => 0],
			'resolve' => ['starting' => 0, 'current' => 0],
			'strength' => ['starting' => 0, 'current' => 0]
		];
		protected $storyPoints = 0;
		protected $skills = [
			'Athletics' => ['value' => 0, 'subskills' => []],
			'Animal Handling' => ['value' => 0, 'subskills' => []],
			'Convince' => ['value' => 0, 'subskills' => []],
			'Craft' => ['value' => 0, 'subskills' => []],
			'Fighting' => ['value' => 0, 'subskills' => []],
			'Knowledge' => ['value' => 0, 'subskills' => []],
			'Marksman' => ['value' => 0, 'subskills' => []],
			'Medicine' => ['value' => 0, 'subskills' => []],
			'Science' => ['value' => 0, 'subskills' => []],
			'Subterfuge' => ['value' => 0, 'subskills' => []],
			'Survival' => ['value' => 0, 'subskills' => []],
			'Technology' => ['value' => 0, 'subskills' => []],
			'Transport' => ['value' => 0, 'subskills' => []]
		];
		protected $traits = [];
		protected $equipment = '';

		public function getAttributeNames() {
			return array_keys((array) $this->attributes);
		}

		public function setAttributes($attribute, $key, $value = '') {
			if (property_exists($this->attributes, $attribute) && in_array($key, ['starting', 'current'])) {
				$value = (int) $value;
				if ($value >= 0) {
					$this->attributes[$attribute][$key] = $value;
				}
			} else {
				return false;
			}
		}

		public function getAttributes($attribute = null, $key = null) {
			if ($attribute == null) {
				return $this->attribute;
			} elseif (property_exists($this->attributes, $attribute)) {
				if (in_array($key, ['starting', 'current'])) {
					return $this->attributes[$attribute][$key];
				} elseif ($key == null) {
					return $this->attributes[$attribute];
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		public function setStoryPoints($storyPoints) {
			$this->storyPoints = (int) $storyPoints;
		}

		public function getStoryPoints() {
			return $this->storyPoints;
		}

		public static function skillEditFormat($key = 1, $subskillOf = 'replace', $skillInfo = null) {
			if ($skillInfo == null) {
				$skillInfo = ['name' => '', 'value' => ''];
			}
?>
									<div class="subskill">
										<input type="text" name="skills[<?=$subskillOf?>][subskills][<?=$key?>][name]"  class="name placeholder" data-placeholder="Skill" value="<?=$skillInfo['name']?>">
										<input id="<?=$skillInfo['name']?>" type="text" name="skills[<?=$subskillOf?>][subskills][<?=$key?>][value]" value="<?=$skillInfo['value']?>">
									</div>
<?
		}

		public function showSkillsEdit() {
			foreach ($this->skills as $skill => $skillData) {
?>
							<div class="skillSet">
								<div class="skill">
									<label for="<?=$skill?>" class="textLabel"><?=$skill?></label>
									<input id="<?=$skill?>" type="text" name="skills[<?=$skill?>][value]" value="<?=$skillData['value']?>">
								</div>
								<a href="" class="addItem" data-type="<?=$skill?>">[ Add Subskill ]</a>
								<div class="subskills">
<?
				if (sizeof($skillData['subskills'])) {
					$key = 1;
					foreach ($skillData['subskills'] as $subskill) {
						$this->skillEditFormat($key++, $skill, $subskill);
					}
				}
?>
								</div>
							</div>
<?
			}
		}

		public function displaySkills() {
			foreach ($this->skills as $skill => $skillData) {
?>
					<div class="skillSet">
						<div class="skill">
							<div class="name"><?=$skill?></div>
							<div class="value"><?=$skillData['value']?></div>
						</div>
						<div class="subskills">
<?
				if (sizeof($skillData['subskills'])) {
					foreach ($skillData['subskills'] as $subskill) {
?>
							<div class="subskill">
								<div class="name"><?=$subskill['name']?></div>
								<div class="value"><?=$subskill['value']?></div>
							</div>
<?
					}
				}
?>
						</div>
					</div>
<?
			}
		}

		public function addSkill($name, $skillData) {
			$skill = ['value' => (int) $skillData['value']];
			if (sizeof($skillData['subskills'])) {
				foreach ($skillData['subskills'] as $subskill) {
					if (strlen($subskill['name'])) {
						newItemized('skill', $subskill['name'], $this::SYSTEM);
						$subskill['name'] = sanitizeString($subskill['name']);
						$subskill['value'] = (int) $subskill['value'];
						$skill['subskills'][] = $subskill;
					}
				}
			}
			$this->skills[$name] = $skill;
		}

		public static function traitEditFormat($key = 1, $type = 'replace', $traitInfo = null) {
			if ($traitInfo == null)
				$traitInfo = ['name' => '', 'notes' => ''];
?>
							<div class="trait tr clearfix">
								<input type="text" name="traits[<?=$type?>][<?=$key?>][name]" value="<?=$traitInfo['name']?>" class="name placeholder" data-placeholder="Trait">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="traits[<?=$type?>][<?=$key?>][notes]"><?=$traitInfo['notes']?></textarea>
							</div>
<?
		}

		public function showTraitsEdit($type) {
			if (sizeof($this->traits[$type])) {
				foreach ($this->traits[$type] as $key => $trait) {
					$this->traitEditFormat($key + 1, $type, $trait);
				}
			} else {
				$this->traitEditFormat(1, $type);
			}
		}

		public function displayTraits($type) {
			if ($this->traits[$type]) {
				foreach ($this->traits[$type] as $trait) {
?>
					<div class="trait tr clearfix">
						<div class="name"><?=$trait['name']?></div>
<?				if (strlen($trait['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$trait['notes']?></div>
<?				} ?>
					</div>
<?
				}
			} else {
				echo "\t\t\t\t\t<p id=\"noTraits\">This character has no {$type} traits.</p>\n";
			}
		}

		public function addTrait($type, $trait) {
			if (strlen($trait['name'])) {
				newItemized('trait', $trait['name'], $this::SYSTEM);
				foreach ($trait as $key => $value) {
					$trait[$key] = sanitizeString($value);
				}
				$this->traits[$type][] = $trait;
			}
		}

		public function setEquipment($equipment) {
			$this->equipment = sanitizeString($equipment);
		}

		public function getEquipment($pr = false) {
			$equipment = $this->equipment;
			if ($pr) {
				$equipment = printReady($equipment);
			}
			return $equipment;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				foreach ($data['attributes'] as $attribute => $values) {
					$this->setAttributes($attribute, 'starting', $values['starting']);
					$this->setAttributes($attribute, 'current', $values['current']);
				}
				$this->setStoryPoints($data['storyPoints']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) {
					foreach ($data['skills'] as $skillName => $skillInfo) {
						$this->addSkill($skillName, $skillInfo);
					}
				}

				$this->clearVar('traits');
				if (sizeof($data['traits'])) {
					foreach ($data['traits'] as $type => $traits) {
						foreach ($traits as $traitInfo) {
							$this->addTrait($type, $traitInfo);
						}
					}
				}

				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
