<?
	class n_13thageCharacter extends d20Character {
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
		protected $feats = array();
		protected $classAbilities = array();
		protected $talents = array();
		protected $powers = array();
		protected $attacks = array();

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

		public function getHL($showSign = false) {
			$hl = floor(array_sum($this->classes) / 2);
			if ($showSign) 
				return showSign($hl);
			else 
				return $hl;
		}

		public function getSaveStat($stat) {
			if ($stat == 'ac') 
				$stats = array('dex', 'con', 'wis');
			elseif ($stat == 'pd') 
				$stats = array('str', 'dex', 'con');
			elseif ($stat == 'md') 
				$stats = array('int', 'wis', 'cha');
			else
				return false;

			if ($this->getStatMod($stats[1]) > $this->getStatMod($stats[0])) {
				$hold = $stats[0];
				$stats[0] = $stats[1];
				$stats[0] = $hold;
			}
			if ($this->getStatMod($stats[2]) > $this->getStatMod($stats[1])) {
				$hold = $stats[1];
				$stats[1] = $stats[2];
				$stats[1] = $hold;
			}
			if ($this->getStatMod($stats[1]) > $this->getStatMod($stats[0])) {
				$hold = $stats[0];
				$stats[0] = $stats[1];
				$stats[0] = $hold;
			}

			return $showSign?showSign($stats[1]):$stats[1];
		}

		public function setHP($key, $value) {
			if (array_key_exists($key, $this->hp)) 
				$this->hp[$key] = intval($value);
			else 
				return false;
		}

		public function getHP($key = null) {
			if ($key == null) 
				return $this->hp;
			elseif (array_key_exists($key, $this->hp)) 
				return $this->hp[$key];
			else 
				return false;
		}

		public function setRecoveries($key, $value) {
			if (array_key_exists($key, $this->recoveries)) 
				$this->recoveries[$key] = intval($value);
			else 
				return false;
		}

		public function getRecoveries($key = null) {
			if ($key == null) 
				return $this->recoveries;
			elseif (array_key_exists($key, $this->recoveries)) 
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
								<div class="tr background clearfix">
									<input type="text" name="backgrounds[<?=$key?>][name]" value="<?=$backgroundInfo['name']?>" class="background_name placeholder" data-placeholder="Background">
									<a href="" class="notesLink">Notes</a>
									<a href="" class="remove sprite cross"></a>
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
			} } else 
				echo "\t\t\t\t\t<p id=\"noBackgrounds\">This character currently has no backgrounds/abilities.</p>\n";
		}
		
		public function addBackground($background) {
			if (strlen($background['name'])) {
				newItemized('background', $background['name'], $this::SYSTEM);
				foreach ($background as $key => $value) 
					$background[$key] = sanitizeString($value);
				$this->backgrounds[] = $background;
			}
		}

		public static function classAbilityEditFormat($key = 1, $classAbilitiesInfo = null) {
			if ($classAbilitiesInfo == null) 
				$classAbilitiesInfo = array('name' => '', 'notes' => '');
?>
							<div class="classAbilities tr clearfix">
								<input type="text" name="classAbilities[<?=$key?>][name]" value="<?=$classAbilitiesInfo['name']?>" class="classAbilities_name placeholder" data-placeholder="Ability">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="classAbilities[<?=$key?>][notes]"><?=$classAbilitiesInfo['notes']?></textarea>
							</div>
<?
		}

		public function showClassAbilitiesEdit() {
			if (sizeof($this->classAbilities)) { foreach ($this->classAbilities as $key => $classAbilities) {
				$this->classAbilityEditFormat($key + 1, $classAbilities);
			} } else $this->classAbilityEditFormat();
		}

		public function displayClassAbilities() {
			if ($this->classAbilities) { foreach ($this->classAbilities as $classAbilities) { ?>
					<div class="classAbilities tr clearfix">
						<span class="classAbilities_name"><?=$classAbilities['name']?></span>
<?	if (strlen($classAbilities['notes'])) { ?>
						<a href="" class="classAbilities_notesLink">Notes</a>
						<div class="notes"><?=$classAbilities['notes']?></div>
<?	} ?>
					</div>
<?
			} } else 
				echo "\t\t\t\t\t<p id=\"noClassAbilities\">This character currently has no classAbilities/abilities.</p>\n";
		}
		
		public function addClassAbilities($classAbilities) {
			if (strlen($classAbilities['name'])) {
				newItemized('classAbilities', $classAbilities['name'], $this::SYSTEM);
				foreach ($classAbilities as $key => $value) 
					$classAbilities[$key] = sanitizeString($value);
				$this->classAbilities[] = $classAbilities;
			}
		}

		public static function talentEditFormat($key = 1, $talentsInfo = null) {
			if ($talentsInfo == null) 
				$talentsInfo = array('name' => '', 'notes' => '');
?>
							<div class="talents tr clearfix">
								<input type="text" name="talents[<?=$key?>][name]" value="<?=$talentsInfo['name']?>" class="talents_name placeholder" data-placeholder="Ability">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="talents[<?=$key?>][notes]"><?=$talentsInfo['notes']?></textarea>
							</div>
<?
		}

		public function showTalentsEdit() {
			if (sizeof($this->talents)) 
				foreach ($this->talents as $key => $talents) 
					$this->talentEditFormat($key + 1, $talents);
			else 
				$this->talentEditFormat();
		}

		public function displayTalents() {
			if ($this->talents) { foreach ($this->talents as $talents) {
?>
					<div class="talents tr clearfix">
						<span class="talents_name"><?=$talents['name']?></span>
<?	if (strlen($talents['notes'])) { ?>
						<a href="" class="talents_notesLink">Notes</a>
						<div class="notes"><?=$talents['notes']?></div>
<?	} ?>
					</div>
<?
			} } else 
				echo "\t\t\t\t\t<p id=\"noTalents\">This character currently has no talents/abilities.</p>\n";
		}
		
		public function addTalents($talents) {
			if (strlen($talents['name'])) {
				newItemized('talents', $talents['name'], $this::SYSTEM);
				foreach ($talents as $key => $value) 
					$talents[$key] = sanitizeString($value);
				$this->talents[] = $talents;
			}
		}

		public static function powerEditFormat($key = 1, $powerInfo = null) {
			if ($powerInfo == null) 
				$powerInfo = array('name' => '', 'notes' => '');
?>
							<div class="power tr clearfix">
								<input type="text" name="powers[<?=$key?>][name]" value="<?=$powerInfo['name']?>" class="power_name placeholder" data-placeholder="Power Name">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
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
			} } else 
				echo "\t\t\t\t\t<p id=\"noPowers\">This character currently has no powers/abilities.</p>\n";
		}
		
		public function addPower($power) {
			if (strlen($power['name'])) {
				newItemized('power', $power['name'], $this::SYSTEM);
				foreach ($power as $key => $value) 
					$power[$key] = sanitizeString($value);
				$this->powers[] = $power;
			}
		}

		public function addAttack($attack) {
			if (strlen($attack['ability'])) {
				foreach ($attack as $key => $value) {
					if ($key == 'ability') 
						$attack[$key] = sanitizeString($value);
					else 
						$attack[$key] = intval($value);
				}
				$this->attacks[] = $attack;
			}
		}

		public function showAttacksEdit($min = 2) {
			$attackNum = 0;
			if (!is_array($this->attacks)) 
				$this->attacks = (array) $this->attacks;
			foreach ($this->attacks as $attackInfo) 
				$this->attackEditFormat($attackNum++, $attackInfo);
			if ($attackNum < $min) 
				while ($attackNum < $min) 
					$this->attackEditFormat($attackNum++);
		}

		public static function attackEditFormat($attackNum, $attackInfo = array()) {
			$defaults = array('total' => 0, 'stat' => 0, 'class' => 0, 'prof' => 0, 'feat' => 0, 'enh' => 0, 'misc' => 0);
		}

		public function displayAttacks() {
			foreach ($this->attacks as $attack) {
				$total = showSign($this->getHL() + $attack['stat'] + $attack['class'] + $attack['prof'] + $attack['feat'] + $attack['enh'] + $attack['misc']);
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
				foreach ($data['saves'] as $save => $values) 
					foreach ($values as $sub => $value) 
						$this->setSave($save, $sub, $value);
				$this->setHP('current', $data['hp']['current']);
				$this->setHP('current', $data['hp']['maximum']);
				$this->setRecoveries('current', $data['recoveries']['current']);
				$this->setRecoveries('current', $data['recoveries']['maximum']);
				$this->setRecoveryRoll($data['recoveryRoll']);

				$this->setUniqueThing($data['uniqueThing']);
				$this->setIconRelationships($data['iconRelationships']);

				$this->clearVar('backgrounds');
				if (sizeof($data['backgrounds'])) 
					foreach ($data['backgrounds'] as $backgroundInfo) 
						$this->addBackground($backgroundInfo);

				$this->clearVar('feats');
				if (sizeof($data['feats'])) 
					foreach ($data['feats'] as $featInfo) 
						$this->addFeat($featInfo);

				$this->clearVar('classAbilities');
				if (sizeof($data['classAbilities'])) 
					foreach ($data['classAbilities'] as $info) 
						$this->addClassAbilities($info);

				$this->clearVar('powers');
				if (sizeof($data['powers'])) 
					foreach ($data['powers'] as $info) 
						$this->addPower($info);

				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>