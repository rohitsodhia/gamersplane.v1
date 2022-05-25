<?
	class hellfrostCharacter extends savageworldsCharacter {
		const SYSTEM = 'hellfrost';

		protected $spells = '';

		public function skillEditFormat($key = null, $skillInfo = NULL) {
			if ($key == null) {
				$key = 1;
			}
			if ($skillInfo == null) {
				$skillInfo = ['name' => '', 'diceType' => ''];
			}
?>
							<div class="skill clearfix">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skillName placeholder" data-placeholder="Skill Name">
								<div class="diceSelect"><span>d</span> <select name="skills[<?=$key?>][diceType]" class="diceType">
<?			foreach ([4, 6, 8, 10, 12] as $dCount) { ?>
									<option<?=$skillInfo['diceType'] == $dCount?' selected="selected"':''?>><?=$dCount?></option>
<?			} ?>
								</select></div>
								<a href="" class="sprite cross small remove"></a>
							</div>
<?
		}

		public function showSkillsEdit($unnecssary = null) {
			if (sizeof($this->skills)) {
				foreach ($this->skills as $key => $skill) {
					$this->skillEditFormat($key + 1, $skill);
				}
			}
		}

		public function displaySkills($unnecssary = null) {
			if ($this->skills) {
				foreach ($this->skills as $skill) {
?>
								<div class="skill clearfix">
									<div class="skillName"><?=$skill['name']?></div>
									<div class="diceType">d<?=$skill['diceType']?></div>
								</div>
<?
				}
			}
		}

		public function addSkill($skill) {
			if (strlen($skill['name']) && in_array((int) $skill['diceType'], [4, 6, 8, 10, 12])) {
				newItemized('skill', $skill['name'], $this::SYSTEM);
				$this->skills[] = ['name' => sanitizeString($skill['name']), 'diceType' => (int) $skill['diceType']];
			}
		}

		public function setSpells($spells) {
			$this->spells = sanitizeString($spells);
		}

		public function getSpells() {
			return $this->spells;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				foreach ($data['traits'] as $trait => $value) {
					$this->setTrait($trait, $value);
				}
				foreach ($data['derivedTraits'] as $trait => $value) {
					$this->setDerivedTrait($trait, $value);
				}

				$this->clearVar('skills');
				if ($data['skills'] && sizeof($data['skills'])) {
					foreach ($data['skills'] as $skillInfo) {
						$this->addSkill($skillInfo);
					}
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

			parent::save($bypass);
		}
	}
?>
