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
		protected $traits = array();
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

		public static function traitEditFormat($key = 1, $type = 'replace', $traitInfo = null) {
			if ($traitInfo == null) 
				$traitInfo = array('name' => '', 'notes' => '');
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
			if (sizeof($this->traits[$type])) 
				foreach ($this->traits[$type] as $key => $trait) 
					$this->traitEditFormat($key + 1, $type, $trait);
			else 
				$this->traitEditFormat(1, $type);
		}

		public function displayTraits($type) {
			if ($this->traits[$type]) { foreach ($this->traits[$type] as $trait) { ?>
					<div class="trait tr clearfix">
						<div class="name"><?=$trait['name']?></div>
<?	if (strlen($trait['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$trait['notes']?></div>
<?	} ?>
					</div>
<?
			} } else 
				echo "\t\t\t\t\t<p id=\"noTraits\">This character has no {$type} traits.</p>\n";
		}
		
		public function addTrait($type, $trait) {
			if (strlen($trait['name'])) {
				newItemized('trait', $trait['name'], $this::SYSTEM);
				foreach ($trait as $key => $value) 
					$trait[$key] = sanitizeString($value);
				$this->traits[$type][] = $trait;
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

				$this->clearVar('traits');
				if (sizeof($data['traits'])) 
					foreach ($data['traits'] as $type => $traits) 
						foreach ($traits as $traitInfo) 
							$this->addTrait($type, $traitInfo);

				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>