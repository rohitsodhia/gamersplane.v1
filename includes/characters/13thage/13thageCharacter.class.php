<?
	class 13thageCharacter extends d20Character {
		const SYSTEM = '13thage';

		protected $race = '';
		protected $saves = array(
			'ac' => array(
				'base' => 10,
				'misc' => 0
			),
			'pd' => array(
				'base' => 10,
				'misc' => 0
			),
			'md' => array(
				'base' => 10,
				'misc' => 0
			)
		);
		protected $hp = array('current' => 0, 'maximum' => 0);
		protected $recoveries = array('current' => 0, 'maximum' => 0);
		protected $recoveryRoll = 0;
		protected $uniqueThing = '';
		protected $iconRelationships = '';
		protected $backgrounds = array();
		protected $classAbilities = array();
		protected $powers = array();
		protected $feats = array();

		public function __construct($characterID, $userID = null) {
			unset($this->ac, $this->speed, $this->initiative, $this->attackBonus, $this->skills);
			parent::__construct($characterID, $userID);
		}

		public function setRace($value) {
			$this->race = sanitizeString($value);
		}

		public function getRace() {
			return $this->race;
		}

		public function setHP($key, $value) {
			if (array_key_exists($key, $this->hp)) {
				$this->hp[$key] = intval($value);
			else 
				return false;
		}

		public function getHP($key = null) {
			if ($key == null) 
				return $this->hp;
			elseif (array_key_exists($key, $this->hp)) {
				return $this->hp[$key];
			else 
				return false;
		}

		public function setRecoveries($key, $value) {
			if (array_key_exists($key, $this->recoveries)) {
				$this->recoveries[$key] = intval($value);
			else 
				return false;
		}

		public function getRecoveries($key = null) {
			if ($key == null) 
				return $this->recoveries;
			elseif (array_key_exists($key, $this->recoveries)) {
				return $this->recoveries[$key];
			else 
				return false;
		}

		public function setRecoveryRoll($value) {
			$this->recoveryRoll = intval($value);
		}

		public function getRecoveryRoll() {
			return $this->recoveryRoll;
		}

		public function setUniqueThing($value) {
			$this->uniqueThing = sanitizeString($value);
		}

		public function getUniqueThing() {
			return $this->uniqueThing;
		}

		public function setIconRelationships($value) {
			$this->iconRelationships = sanitizeString($value);
		}

		public function getIconRelationships() {
			return $this->iconRelationships;
		}

		public static function backgroundEditFormat($key = 1, $backgroundInfo = null) {
			if ($backgroundInfo == null) 
				$backgroundInfo = array('name' => '', 'notes' => '');
?>
							<div class="background clearfix">
								<input type="text" name="backgrounds[<?=$key?>][name]" value="<?=$backgroundInfo['name']?>" class="background_name placeholder" data-placeholder="Background Name">
								<a href="" class="background_notesLink">Notes</a>
								<a href="" class="background_remove sprite cross"></a>
								<textarea name="backgrounds[<?=$key?>][notes]"><?=$backgroundInfo['notes']?></textarea>
							</div>
<?
		}

		public function showBackgroundsEdit() {
			if (sizeof($this->backgrounds)) { foreach ($this->backgrounds as $key => $background) {
				$this->backgroundEditFormat($key + 1, $background);
			} } else $this->backgroundEditFormat();
		}

		public function displayBackgrounds() {
			if ($this->backgrounds) { foreach ($this->backgrounds as $background) { ?>
					<div class="background tr clearfix">
						<span class="background_name"><?=$background['name']?></span>
<?	if (strlen($background['notes'])) { ?>
						<a href="" class="background_notesLink">Notes</a>
						<div class="notes"><?=$background['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noBackgrounds\">This character currently has no backgrounds/abilities.</p>\n";
		}
		
		public function addBackground($background) {
			if (strlen($background['name'])) {
				newItemized('background', $background['name'], $this::SYSTEM);
				foreach ($background as $key => $value) 
					$background[$key] = sanitizeString($value);
				$this->backgrounds[] = $background;
			}
		}

		public static function classAbilitiesEditFormat($key = 1, $classAbilitiesInfo = null) {
			if ($classAbilitiesInfo == null) 
				$classAbilitiesInfo = array('name' => '', 'notes' => '');
?>
							<div class="classAbilities clearfix">
								<input type="text" name="classAbilitiess[<?=$key?>][name]" value="<?=$classAbilitiesInfo['name']?>" class="classAbilities_name placeholder" data-placeholder="Feature/Talent Name">
								<a href="" class="classAbilities_notesLink">Notes</a>
								<a href="" class="classAbilities_remove sprite cross"></a>
								<textarea name="classAbilitiess[<?=$key?>][notes]"><?=$classAbilitiesInfo['notes']?></textarea>
							</div>
<?
		}

		public function showClassAbilitiessEdit() {
			if (sizeof($this->classAbilitiess)) { foreach ($this->classAbilitiess as $key => $classAbilities) {
				$this->classAbilitiesEditFormat($key + 1, $classAbilities);
			} } else $this->classAbilitiesEditFormat();
		}

		public function displayClassAbilitiess() {
			if ($this->classAbilitiess) { foreach ($this->classAbilitiess as $classAbilities) { ?>
					<div class="classAbilities tr clearfix">
						<span class="classAbilities_name"><?=$classAbilities['name']?></span>
<?	if (strlen($classAbilities['notes'])) { ?>
						<a href="" class="classAbilities_notesLink">Notes</a>
						<div class="notes"><?=$classAbilities['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noClassAbilitiess\">This character currently has no classAbilitiess/abilities.</p>\n";
		}
		
		public function addClassAbilities($classAbilities) {
			if (strlen($classAbilities['name'])) {
				newItemized('classAbilities', $classAbilities['name'], $this::SYSTEM);
				foreach ($classAbilities as $key => $value) 
					$classAbilities[$key] = sanitizeString($value);
				$this->classAbilitiess[] = $classAbilities;
			}
		}

		public static function powerEditFormat($key = 1, $powerInfo = null) {
			if ($powerInfo == null) 
				$powerInfo = array('name' => '', 'notes' => '');
?>
							<div class="power clearfix">
								<input type="text" name="powers[<?=$key?>][name]" value="<?=$powerInfo['name']?>" class="power_name placeholder" data-placeholder="Power Name">
								<a href="" class="power_notesLink">Notes</a>
								<a href="" class="power_remove sprite cross"></a>
								<textarea name="powers[<?=$key?>][notes]"><?=$powerInfo['notes']?></textarea>
							</div>
<?
		}

		public function showPowersEdit() {
			if (sizeof($this->powers)) { foreach ($this->powers as $key => $power) {
				$this->powerEditFormat($key + 1, $power);
			} } else $this->powerEditFormat();
		}

		public function displayPowers() {
			if ($this->powers) { foreach ($this->powers as $power) { ?>
					<div class="power tr clearfix">
						<span class="power_name"><?=$power['name']?></span>
<?	if (strlen($power['notes'])) { ?>
						<a href="" class="power_notesLink">Notes</a>
						<div class="notes"><?=$power['notes']?></div>
<?	} ?>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noPowers\">This character currently has no powers/abilities.</p>\n";
		}
		
		public function addPower($power) {
			if (strlen($power['name'])) {
				newItemized('power', $power['name'], $this::SYSTEM);
				foreach ($power as $key => $value) 
					$power[$key] = sanitizeString($value);
				$this->powers[] = $power;
			}
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);

				foreach ($data['stats'] as $stat => $value) 
					$this->setStat($stat, $value);

				$this->setInspiration($data['inspiration']);
				$this->setProfBonus($data['profBonus']);
				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, $value);
					$this->setSaveProf($stat, isset($data['statProf'][$stat])?true:false);
				}
				$this->setHP('total', $data['hp']['total']);
				$this->setHP('temp', $data['hp']['temp']);
				$this->setAC($data['ac']);
				$this->setInitiative($data['initiative']);
				$this->setSpeed($data['speed']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillInfo) {
					$this->addSkill($skillInfo);
				} }

				$this->clearVar('feats');
				if (sizeof($data['feats'])) { foreach ($data['feats'] as $featInfo) {
					$this->addFeat($featInfo);
				} }

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

				$this->clearVar('spells');
				if (sizeof($data['spells'])) { foreach ($data['spells'] as $spellInfo) {
					$this->addSpell($spellInfo);
				} }

				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>