<?
	class dresdenCharacter extends fateCharacter {
		const SYSTEM = 'dresden';

		protected $template = '';
		protected $musts = '';
		protected $fatePoints = array('current' => 3, 'refresh' => 0, 'adjustedRefresh' => 0);
		protected $powerLevel = 0;
		protected $skillCap = 0;
		protected $skillPoints = array('spent' => 0, 'available' => 0);
		protected $stress = array('physical' => 0, 'mental' => 0, 'social' => 0);

		public function setPowerLevel($powerLevel) {
			$this->powerLevel = intval($powerLevel);
		}

		public function getPowerLevel() {
			return $this->powerLevel;
		}

		public function setSkillCap($skillCap) {
			$this->skillCap = intval($skillCap);
		}

		public function getSkillCap() {
			return $this->skillCap;
		}

		public function setSkillPoints($key, $value) {
			if (array_key_exists($key, $this->skillPoints)) $this->skillPoints[$key] = intval($value);
			else return false;
		}

		public function getSkillPoints($key = null) {
			if ($key == null) return $this->skillPoints;
			elseif (array_key_exists($key, $this->skillPoints)) return $this->skillPoints[$key];
			else return false;
		}

		public static function stuntEditFormat($key = 1, $stuntInfo = null) {
			if ($stuntInfo == null) $stuntInfo = array('name' => '', 'cost' => 0, 'notes' => '');
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
				$stunt['cost'] = intval($stunt['cost']);
				$this->stunts[] = $stunt;
			}
		}

		public function save($bypass = false) {
			global $mysql;
			$data = $_POST;
			$system = $this::SYSTEM;

			if (!isset($data['create']) && !$bypass) {
				$this->setName($data['name']);
				$this->setPowerLevel($data['powerLevel']);
				$this->setFatePoints('current', $data['fatePoints']['current']);
				$this->setFatePoints('refresh', $data['fatePoints']['refresh']);
				$this->setFatePoints('adjustedRefresh', $data['fatePoints']['adjustedRefresh']);

				$this->setHighConcept($data['highConcept']);
				$this->setTrouble($data['trouble']);

				$this->clearVar('aspects');
				if (sizeof($data['aspects'])) {
					foreach ($data['aspects'] as $aspect) $this->addAspect($aspect);
				}

				$this->clearVar('stunts');
				if (sizeof($data['stunts'])) { foreach ($data['stunts'] as $stuntInfo) {
					$this->addStunt($stuntInfo);
				} }

				$this->setSkillCap($data['skillCap']);
				$this->setSkillPoints('spent', $data['skillPoints']['spent']);
				$this->setSkillPoints('available', $data['skillPoints']['available']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) {
					foreach ($data['skills'] as $skill) $this->addSkill($skill);
				}

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