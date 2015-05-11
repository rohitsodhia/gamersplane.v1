<?
	class primevalCharacter extends Character {
		const SYSTEM = 'primeval';

		protected $attributes = array(
			'awareness' => array('starting' => 0, 'current' => 0),
			'coordination' => array('starting' => 0, 'current' => 0),
			'ingenuity' => array('starting' => 0, 'current' => 0),
			'presence' => array('starting' => 0, 'current' => 0),
			'resolve' => array('starting' => 0, 'current' => 0),
			'strength' => array('starting' => 0, 'current' => 0)
		);
		protected $storyPoints = 0;
		protected $skills = array(
			'Athletics' => array('value' => 0, 'subskills' => array()),
			'Animal Handling' => array('value' => 0, 'subskills' => array()),
			'Convince' => array('value' => 0, 'subskills' => array()),
			'Craft' => array('value' => 0, 'subskills' => array()),
			'Fighting' => array('value' => 0, 'subskills' => array()),
			'Knowledge' => array('value' => 0, 'subskills' => array()),
			'Marksman' => array('value' => 0, 'subskills' => array()),
			'Medicine' => array('value' => 0, 'subskills' => array()),
			'Science' => array('value' => 0, 'subskills' => array()),
			'Subterfuge' => array('value' => 0, 'subskills' => array()),
			'Survival' => array('value' => 0, 'subskills' => array()),
			'Technology' => array('value' => 0, 'subskills' => array()),
			'Transport' => array('value' => 0, 'subskills' => array())
		);
		protected $talents = array();
		protected $equipment = '';

		public function getAttributeNames() {
			return array_keys($this->attributes);
		}

		public function setAttributes($attribute, $key, $value = '') {
			if (array_key_exists($attribute,$this->attributes) && in_array($key, array('starting', 'current'))) {
				$value = intval($value);
				if ($value >= 0) 
					$this->attributes[$attribute][$key] = $value;
			} else 
				return false;
		}
		
		public function getAttributes($attribute = null, $key = null) {
			if ($attribute == null) 
				return $this->attribute;
			elseif (array_key_exists($attribute, $this->attributes)) {
				if (in_array($key, array('starting', 'current'))) 
					return $this->attributes[$attribute][$key];
				elseif ($key == null) 
					return $this->attributes[$attribute];
				else 
					return false;
			}
			else 
				return false;
		}

		public function setStoryPoints($storyPoints) {
			$this->storyPoints = intval($storyPoints);
		}

		public function getStoryPoints() {
			return $this->storyPoints;
		}

		public static function skillEditFormat($key = 1, $subskillOf = 'replace', $skillInfo = null) {
			if ($skillInfo == null) 
				$skillInfo = array('name' => '', 'value' => '');
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
					foreach ($skillData['subskills'] as $subskill) 
						$this->skillEditFormat($key++, $skill, $subskill);
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
			$skill = array('value' => intval($skillData['value']));
			if (sizeof($skillData['subskills'])) { foreach ($skillData['subskills'] as $subskill) {
				if (strlen($subskill['name'])) {
					newItemized('skill', $subskill['name'], $this::SYSTEM);
					$subskill['name'] = sanitizeString($subskill['name']);
					$subskill['value'] = intval($subskill['value']);
					$skill['subskills'][] = $subskill;
				}
			} }
			$this->skills[$name] = $skill;
		}

		public static function talentEditFormat($key = 1, $talentInfo = null) {
			if ($talentInfo == null) 
				$talentInfo = array('name' => '', 'notes' => '');
?>
							<div class="talent tr clearfix">
								<input type="text" name="talents[<?=$key?>][name]" value="<?=$talentInfo['name']?>" class="name placeholder" data-placeholder="Talent">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="talents[<?=$key?>][notes]"><?=$talentInfo['notes']?></textarea>
							</div>
<?
		}

		public function showTalentsEdit() {
			if (sizeof($this->talents)) { foreach ($this->talents as $key => $talent) {
				$this->talentEditFormat($key + 1, $talent);
			} } else $this->talentEditFormat();
		}

		public function displayTalents() {
			if ($this->talents) { foreach ($this->talents as $talent) { ?>
					<div class="talent tr clearfix">
						<div class="name"><?=$talent['name']?></div>
<?	if (strlen($talent['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$talent['notes']?></div>
<?	} ?>
					</div>
<?
			} } else 
				echo "\t\t\t\t\t<p id=\"noTalents\">This character currently has no talents.</p>\n";
		}
		
		public function addTalent($talent) {
			if (strlen($talent['name'])) {
				newItemized('talent', $talent['name'], $this::SYSTEM);
				foreach ($talent as $key => $value) 
					$talent[$key] = sanitizeString($value);
				$this->talents[] = $talent;
			}
		}

		public function setEquipment($equipment) {
			$this->equipment = sanitizeString($equipment);
		}

		public function getEquipment($pr = false) {
			$equipment = $this->equipment;
			if ($pr) 
				$equipment = printReady($equipment);
			return $equipment;
		}

		public function save() {
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['attributes'] as $attribute => $values) {
					$this->setAttributes($attribute, 'starting', $values['starting']);
					$this->setAttributes($attribute, 'current', $values['current']);
				}
				$this->setStoryPoints($data['storyPoints']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) 
					foreach ($data['skills'] as $skillName => $skillInfo) 
						$this->addSkill($skillName, $skillInfo);

				$this->clearVar('talents');
				if (sizeof($data['talents'])) 
					foreach ($data['talents'] as $talentInfo) 
						$this->addTalent($talentInfo);

				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>