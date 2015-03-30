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
		protected $recovery = 0;
		protected $uniqueThing = '';
		protected $iconRelationships = '';
		protected $backgrounds = array();
		protected $classAbilities = array();
		protected $powers = array();

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

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				$this->setBackground($data['background']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);
				$this->setAlignment($data['alignment']);

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