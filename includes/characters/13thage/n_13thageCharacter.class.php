<?
	class n_13thageCharacter extends d20Character {
		const SYSTEM = '13thage';

		protected $race = '';
		protected $saves = [
			'ac' => [
				'base' => 10,
				'misc' => 0
			],
			'pd' => [
				'base' => 10,
				'misc' => 0
			],
			'md' => [
				'base' => 10,
				'misc' => 0
			]
		];
		protected $hp = ['current' => 0, 'maximum' => 0];
		protected $recoveries = ['current' => 0, 'maximum' => 0];
		protected $recoveryRoll = 0;
		protected $uniqueThing = '';
		protected $iconRelationships = '';
		protected $backgrounds = [];
		protected $feats = [];
		protected $abilitiesTalents = [];
		protected $powers = [];
		protected $basicAttacks = [
			'melee' => [
				'stat' => 'str',
				'misc' => 0,
				'hit' => '',
				'miss' => ''
			],
			'ranged' => [
				'stat' => 'dex',
				'misc' => 0,
				'hit' => '',
				'miss' => ''
			]
		];
		protected $attacks = [];

		public function __construct($characterID = null, $userID = null) {
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

		public function getSave($save = null, $key = null) {
			if (array_key_exists($save, $this->saves)) {
				if ($key == null)
					return $this->saves[$save];
				elseif (array_key_exists($key, $this->saves[$save]))
					return $this->saves[$save][$key];
				elseif ($key == 'total') {
					$total = 0;
					foreach ($this->saves[$save] as $value)
						if (is_numeric($value))
							$total += $value;
					$total += $this->getStatMod($this->getSaveStat($save), false) + $this->getLevel();
					return $total;
				} else
					return false;
			} elseif ($save == null)
				return $this->saves;
			else
				return false;
		}

		public function getSaveStat($stat) {
			if ($stat == 'ac')
				$stats = ['dex', 'con', 'wis'];
			elseif ($stat == 'pd')
				$stats = ['str', 'dex', 'con'];
			elseif ($stat == 'md')
				$stats = ['int', 'wis', 'cha'];
			else
				return false;

			if ($this->getStatMod($stats[1], false) > $this->getStatMod($stats[0], false)) {
				$hold = $stats[0];
				$stats[0] = $stats[1];
				$stats[1] = $hold;
			}
			if ($this->getStatMod($stats[2], false) > $this->getStatMod($stats[1], false)) {
				$hold = $stats[1];
				$stats[1] = $stats[2];
				$stats[2] = $hold;
			}
			if ($this->getStatMod($stats[1], false) > $this->getStatMod($stats[0], false)) {
				$hold = $stats[0];
				$stats[0] = $stats[1];
				$stats[1] = $hold;
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
			$value = str_replace(' ', '', strtolower($value));
			if (preg_match('/\d+d\d+([+-]\d+)?/', $value))
				$this->recoveryRoll = $value;
			else
				$this->recoveryRoll = '';
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
				$backgroundInfo = ['name' => '', 'notes' => ''];
?>
								<div class="tr background clearfix">
									<input type="text" name="backgrounds[<?=$key?>][name]" value="<?=$backgroundInfo['name']?>" class="name placeholder" data-placeholder="Background/Racial Abilities">
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
						<span class="name"><?=$background['name']?></span>
<?	if (strlen($background['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$background['notes']?></div>
<?	} ?>
					</div>
<?
			} } else
				echo "\t\t\t\t\t<p id=\"noBackgrounds\">This character currently has no backgrounds/racial abilities.</p>\n";
		}

		public function addBackground($background) {
			if (strlen($background['name'])) {
				newItemized('background', $background['name'], $this::SYSTEM);
				foreach ($background as $key => $value)
					$background[$key] = sanitizeString($value);
				$this->backgrounds[] = $background;
			}
		}

		public static function abilityTalentEditFormat($key = 1, $info = null) {
			if ($info == null)
				$info = ['name' => '', 'notes' => ''];
?>
							<div class="abilityTalent tr clearfix">
								<input type="text" name="abilitiesTalents[<?=$key?>][name]" value="<?=$info['name']?>" class="name placeholder" data-placeholder="Ability/Talent">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="abilitiesTalents[<?=$key?>][notes]"><?=$info['notes']?></textarea>
							</div>
<?
		}

		public function showAbilitiesTalentsEdit() {
			if (sizeof($this->abilitiesTalents))
				foreach ($this->abilitiesTalents as $key => $info)
					$this->abilityTalentEditFormat($key + 1, $info);
			else
				$this->abilityTalentEditFormat();
		}

		public function displayAbilitiesTalents() {
			if ($this->abilitiesTalents) {
				foreach ($this->abilitiesTalents as $abilityTalent) {
?>
					<div class="abilityTalent tr clearfix">
						<span class="name"><?=$abilityTalent['name']?></span>
<?					if (strlen($abilityTalent['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$abilityTalent['notes']?></div>
<?					} ?>
					</div>
<?
				}
			} else
				echo "\t\t\t\t\t<p id=\"noAbilitiesTalents\">This character currently has no class abilities/talents.</p>\n";
		}

		public function addAbilitiesTalents($abilityTalent) {
			if (strlen($abilityTalent['name'])) {
				newItemized('abilityTalent', $abilityTalent['name'], $this::SYSTEM);
				foreach ($abilityTalent as $key => $value)
					$abilityTalent[$key] = sanitizeString($value);
				$this->abilitiesTalents[] = $abilityTalent;
			}
		}

		public static function powerEditFormat($key = 1, $powerInfo = null) {
			if ($powerInfo == null)
				$powerInfo = ['name' => '', 'notes' => ''];
?>
							<div class="power tr clearfix">
								<input type="text" name="powers[<?=$key?>][name]" value="<?=$powerInfo['name']?>" class="name placeholder" data-placeholder="Power">
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
						<span class="name"><?=$power['name']?></span>
<?	if (strlen($power['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$power['notes']?></div>
<?	} ?>
					</div>
<?
			} } else
				echo "\t\t\t\t\t<p id=\"noPowers\">This character currently has no powers.</p>\n";
		}

		public function addPower($power) {
			if (strlen($power['name'])) {
				newItemized('power', $power['name'], $this::SYSTEM);
				foreach ($power as $key => $value)
					$power[$key] = sanitizeString($value);
				$this->powers[] = $power;
			}
		}

		public function setBasicAttack($attack, $key, $value) {
			if (array_key_exists($attack, $this->basicAttacks)) {
				if (is_int($value))
					$this->basicAttacks[$attack][$key] = intval($value);
				else
					$this->basicAttacks[$attack][$key] = sanitizeString($value);
			}
		}

		public function getBasicAttacks($attack = null, $key = null) {
			if (array_key_exists($attack, $this->basicAttacks)) {
				if (array_key_exists($key, $this->basicAttacks[$attack]))
					return $this->basicAttacks[$attack][$key];
				elseif ($key == null)
					return $this->basicAttacks[$attack];
			} elseif ($attack == null)
				return $this->basicAttacks;
			else
				return null;
		}

		public static function attackEditFormat($key = 1, $attackInfo = null) {
			if ($attackInfo == null)
				$attackInfo = ['name' => '', 'notes' => ''];
?>
							<div class="attack tr clearfix">
								<input type="text" name="attacks[<?=$key?>][name]" value="<?=$attackInfo['name']?>" class="name placeholder" data-placeholder="Attack">
								<a href="" class="notesLink">Notes</a>
								<a href="" class="remove sprite cross"></a>
								<textarea name="attacks[<?=$key?>][notes]"><?=$attackInfo['notes']?></textarea>
							</div>
<?
		}

		public function showAttacksEdit() {
			if (sizeof($this->attacks)) { foreach ($this->attacks as $key => $attack) {
				$this->attackEditFormat($key + 1, $attack);
			} } else $this->attackEditFormat();
		}

		public function displayAttacks() {
			if ($this->attacks) { foreach ($this->attacks as $attack) { ?>
					<div class="attack tr clearfix">
						<span class="name"><?=$attack['name']?></span>
<?	if (strlen($attack['notes'])) { ?>
						<a href="" class="notesLink">Notes</a>
						<div class="notes"><?=$attack['notes']?></div>
<?	} ?>
					</div>
<?
			} } else
				echo "\t\t\t\t\t<p id=\"noAttacks\">This character currently has no attacks.</p>\n";
		}

		public function addAttack($attack) {
			if (strlen($attack['name'])) {
				newItemized('attack', $attack['name'], $this::SYSTEM);
				foreach ($attack as $key => $value)
					$attack[$key] = sanitizeString($value);
				$this->attacks[] = $attack;
			}
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
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
				$this->setHP('maximum', $data['hp']['maximum']);
				$this->setRecoveries('current', $data['recoveries']['current']);
				$this->setRecoveries('maximum', $data['recoveries']['maximum']);
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

				$this->clearVar('abilitiesTalents');
				if (sizeof($data['abilitiesTalents']))
					foreach ($data['abilitiesTalents'] as $info)
						$this->addAbilitiesTalents($info);

				$this->clearVar('powers');
				if (sizeof($data['powers']))
					foreach ($data['powers'] as $info)
						$this->addPower($info);

				foreach ($data['basicAttacks'] as $type => $field)
					foreach ($field as $key => $value)
						$this->setBasicAttack($type, $key, $value);

				$this->clearVar('attacks');
				if (sizeof($data['attacks']))
					foreach ($data['attacks'] as $info)
						$this->addAttack($info);

				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
