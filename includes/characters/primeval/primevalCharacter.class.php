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
		protected $skills = array();
		protected $traits = array();
		protected $equipment = '';

		public function setAttributes($attribute, $key, $value = '') {
			if (array_key_exists($attribute,$this->attributes) && in_array($key, array('starting', 'current'))) {
				$value = intval($value);
				if ($value >= 0) 
					$this->attributes[$attribute] = $value;
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

		public static function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) 
				$skillInfo = array('name' => '', 'notes' => '');
?>
							<div class="skill tr clearfix">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="name placeholder" data-placeholder="Skill">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="skills[<?=$key?>][notes]"><?=$skillInfo['notes']?></textarea>
							</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill);
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) { ?>
					<div class="skill tr clearfix">
						<span class="name"><?=$skill['name']?></span>
<?	if (strlen($skill['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$skill['notes']?></div>
<?	} ?>
					</div>
<?
			} } else 
				echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}
		
		public function addSkill($skill) {
			if (strlen($skill['name'])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				foreach ($skill as $key => $value) 
					$skill[$key] = sanitizeString($value);
				$this->skills[] = $skill;
			}
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
						<span class="name"><?=$talent['name']?></span>
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

		public function getEquipment() {
			return $this->equipment;
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
					foreach ($data['skills'] as $skillInfo) 
						$this->addSkill($skillInfo);

				$this->clearVar('traits');
				if (sizeof($data['traits'])) 
					foreach ($data['traits'] as $traitInfo) 
						$this->addTrait($traitInfo);

				$this->setEquipment($data['equipment']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>