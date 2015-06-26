<?
	class dnd4Character extends d20Character {
		const SYSTEM = 'dnd4';

		protected $race = '';
		protected $alignment = 'tn';
		protected $totalLevels = 0;
		protected $paragon = '';
		protected $epic = '';
		protected $saves = array ('ac' => array('armor' => 0, 'class' => 0, 'feats' => 0, 'enh' => 0, 'misc' => 0),
								  'fort' => array('ability' => array('con', 'str'), 'class' => 0, 'feats' => 0, 'enh' => 0, 'misc' => 0),
								  'ref' => array('ability' => array('dex', 'int'), 'class' => 0, 'feats' => 0, 'enh' => 0, 'misc' => 0),
								  'will' => array('ability' => array('wis', 'cha'), 'class' => 0, 'feats' => 0, 'enh' => 0, 'misc' => 0));
		protected $hp = array('total' => 0, 'current' => 0, 'surges' => 0);
		protected $speed = array('base' => 0, 'armor' => 0, 'item' => 0, 'misc' => 0);
		protected $actionPoints = 0;
		protected $passiveSenses = array('insight' => 0, 'perception' => 0);
		protected $attacks = array();
		protected $powers = array('atwill' => array(), 'encounter' => array(), 'daily' => array());
		protected $weapons = '';
		protected $armor = '';

		public function setRace($value) {
			$this->race = sanitizeString($value);
		}

		public function getRace() {
			return $this->race;
		}

		public function setAlignment($value) {
			if (dnd4_consts::getAlignments($value)) 
				$this->alignment = $value;
		}

		public function getAlignment() {
			return dnd4_consts::getAlignments($this->alignment);
		}

		public function getHL($showSign = false) {
			$hl = floor(array_sum($this->classes) / 2);
			if ($showSign) return showSign($hl);
			else return $hl;
		}

		public function setParagon($paragon) {
			$this->paragon = sanitizeString($paragon);
		}

		public function getParagon() {
			return $this->paragon;
		}

		public function setEpic($epic) {
			$this->epic = sanitizeString($epic);
		}

		public function getEpic() {
			return $this->epic;
		}

		public function getStatModPHL($stat) {
			if (array_key_exists($stat, $this->stats)) 
				return showSign(floor(($this->stats[$stat] - 10) / 2) + $this->getHL());
			else 
				return false;
		}

		public function getSave($save = null, $key = null) {
			if (array_key_exists($save, $this->saves)) {
				if ($key == null) 
					return $this->saves[$save];
				elseif (array_key_exists($key, $this->saves[$save])) 
					return $this->saves[$save][$key];
				elseif ($key == 'total') {
					$total = 10 + $this->getHL();
					foreach ($this->saves[$save] as $value) 
						if (is_numeric($value)) 
							$total += $value;
					$abilities = $this->saves[$save]['ability'];
					$abilityMods = array($this->getStatMod($abilities[0], false), $this->getStatMod($abilities[1], false));
					if ($abilityMods[0] > $abilityMods[1]) 
						$total += $abilityMods[0];
					else 
						$total += $abilityMods[1];
					return $total;
				} else 
					return false;
			} elseif ($save == null) 
				return $this->saves;
			else 
				return false;
		}

		public function getInitiative($key = null) {
			$return = parent::getInitiative($key);
			if ($key == 'total') $return += $this->getHL();
			return $return;
		}

		public function setSpeed($key, $value) {
			if (array_key_exists($key, $this->speed)) 
				$this->speed[$key] = intval($value);
			else 
				return false;
		}

		public function getSpeed($key = null) {
			if ($key == null) 
				return array_merge(array('total' => array_sum($this->speed)), $this->speed);
			elseif (array_key_exists($key, $this->speed)) 
				return $this->speed[$key];
			elseif ($key == 'total') 
				return array_sum($this->speed);
			else 
				return false;
		}

		public function setActionPoints($value) {
			$value = intval($value);
			if ($value >= 0) 
				$this->actionPoints = $value;
			else 
				return false;
		}

		public function getActionPoints() {
			return $this->actionPoints;
		}

		public function setPassiveSenses($key, $value) {
			$value = intval($value);
			if (array_key_exists($key, $this->passiveSenses) && $value >= 0) 
				$this->passiveSenses[$key] = $value;
			else 
				return false;
		}

		public function getPassiveSenses($key) {
			if (array_key_exists($key, $this->passiveSenses)) 
				return $this->passiveSenses[$key];
			else 
				return false;
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

		public function showAttacksEdit($min) {
			$attackNum = 0;
			if (!is_array($this->attacks)) 
				$this->attacks = (array) $this->attacks;
			foreach ($this->attacks as $attackInfo) 
				$this->attackEditFormat($attackNum++, $attackInfo);
			if ($attackNum < $min) 
				while ($attackNum < $min) 
					$this->attackEditFormat($attackNum++);
		}

		public function attackEditFormat($attackNum, $attackInfo = array()) {
			$defaults = array('total' => 0, 'stat' => 0, 'class' => 0, 'prof' => 0, 'feat' => 0, 'enh' => 0, 'misc' => 0);
			foreach ($defaults as $key => $value) 
				if (!isset($attackInfo[$key])) 
					$attackInfo[$key] = $value;
			$total = $this->getHL();
			foreach ($attackInfo as $value) 
				$total += intval($value);
?>
							<div class="attackBonusSet">
								<div class="tr">
									<label class="medNum leftLabel">Ability</label>
									<input type="text" name="attacks[<?=$attackNum?>][ability]" value="<?=$attackInfo['ability']?>" class="ability">
								</div>
								<div class="tr labelTR">
									<label class="shortNum alignCenter lrBuffer">Total</label>
									<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
									<label class="shortNum alignCenter lrBuffer">Stat</label>
									<label class="shortNum alignCenter lrBuffer">Class</label>
									<label class="shortNum alignCenter lrBuffer">Prof</label>
									<label class="shortNum alignCenter lrBuffer">Feat</label>
									<label class="shortNum alignCenter lrBuffer">Enh</label>
									<label class="shortNum alignCenter lrBuffer">Misc</label>
								</div>
								<div class="tr sumRow">
									<span class="shortNum lrBuffer addHL total"><?=showSign($total)?></span>
									<span class="shortNum lrBuffer addHL">+<?=$this->getHL()?></span>
									<input type="text" name="attacks[<?=$attackNum?>][stat]" value="<?=$attackInfo['stat']?>" class="statInput lrBuffer">
									<input type="text" name="attacks[<?=$attackNum?>][class]" value="<?=$attackInfo['class']?>" class="statInput lrBuffer">
									<input type="text" name="attacks[<?=$attackNum?>][prof]" value="<?=$attackInfo['prof']?>" class="statInput lrBuffer">
									<input type="text" name="attacks[<?=$attackNum?>][feat]" value="<?=$attackInfo['feat']?>" class="statInput lrBuffer">
									<input type="text" name="attacks[<?=$attackNum?>][enh]" value="<?=$attackInfo['enh']?>" class="statInput lrBuffer">
									<input type="text" name="attacks[<?=$attackNum?>][misc]" value="<?=$attackInfo['misc']?>" class="statInput lrBuffer">
								</div>
							</div>
<?
		}

		public function displayAttacks() {
			foreach ($this->attacks as $attack) {
				$total = showSign($this->getHL() + $attack['stat'] + $attack['class'] + $attack['prof'] + $attack['feat'] + $attack['enh'] + $attack['misc']);
?>
					<div class="attackBonusSet">
						<div class="tr">
							<label class="medNum leftLabel">Ability:</label>
							<div class="lrBuffer ability"><?=$attack['ability']?></div>
						</div>
						<div class="tr labelTR">
							<label class="shortNum alignCenter lrBuffer">Total</label>
							<label class="shortNum alignCenter lrBuffer">1/2 Lvl</label>
							<label class="shortNum alignCenter lrBuffer">Stat</label>
							<label class="shortNum alignCenter lrBuffer">Class</label>
							<label class="shortNum alignCenter lrBuffer">Prof</label>
							<label class="shortNum alignCenter lrBuffer">Feat</label>
							<label class="shortNum alignCenter lrBuffer">Enh</label>
							<label class="shortNum alignCenter lrBuffer">Misc</label>
						</div>
						<div class="tr">
							<div class="shortNum alignCenter lrBuffer"><?=$total?></div>
							<div class="shortNum alignCenter lrBuffer">+<?=$this->getHL()?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['stat'])?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['class'])?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['prof'])?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['feat'])?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['enh'])?></div>
							<div class="shortNum alignCenter lrBuffer"><?=showSign($attack['misc'])?></div>
						</div>
					</div>
<?
			}
		}

		public static function skillEditFormat($key = null, $skillInfo = null, $statBonus = null) {
			if ($key == null) 
				$key = 1;
			if ($skillInfo == null) 
				$skillInfo = array('name' => '', 'stat' => 'str', 'ranks' => 0, 'misc' => 0);
			if ($skillInfo['stat'] == null || $statBonus == null) 
				$statBonus = 0;
?>
						<div class="skill clearfix sumRow">
							<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="medText placeholder dontAdd" data-placeholder="Skill Name">
							<span id="skillTotal_<?=$key?>" class="skill_total textLabel lrBuffer addStat_<?=$skillInfo['stat']?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
<?			foreach (d20Character_consts::getStatNames() as $short => $stat) { ?>
								<option value="<?=$short?>"<?=$skillInfo['stat'] == $short?' selected="selected"':''?>><?=ucfirst($short)?></option>
<?			} ?>
							</select></span>
							<input type="text" name="skills[<?=$key?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$key?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<a href="" class="remove sprite cross lrBuffer"></a>
						</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill, $this->getStatMod($skill['stat'], false));
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
					<div class="skill tr clearfix">
						<span class="skill_name medText"><?=$skill['name']?></span>
						<span class="skill_total shortNum lrBuffer"><?=showSign($this->getStatMod($skill['stat'], false) + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		static public function powerEditFormat($type = null, $power = null) {
?>
							<div class="power tr">
								<input type="text" name="powers[<?=$type?>][]" value="<?=$power?>" class="power_name placeholder" data-placeholder="Power Name">
								<a href="" class="power_remove sprite cross lrBuffer"></a>
							</div>
<?
		}

		public function showPowersEdit($type) {
			foreach ($this->powers[$type] as $power) 
				dnd4Character::powerEditFormat($type, $power);
		}

		public function powerSheetFormat($type) {
			foreach ($this->powers[$type] as $power) {
?>
					<div class="power"><?=$power?></div>
<?
			}
		}

		public function addPower($power, $type) {
			if (strlen($power)) {
				newItemized('dnd4_power', $power, $this::SYSTEM);
				$this->powers[$type][] = $power;
			}
		}

		public function setWeapons($weapons) {
			$this->weapons = sanitizeString($weapons);
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setArmor($armor) {
			$this->armor = sanitizeString($armor);
		}

		public function getArmor() {
			return $this->armor;
		}

		public function setItems($items) {
			$this->items = sanitizeString($items);
		}

		public function getItems() {
			return $this->items;
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				$this->setAlignment($data['alignment']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) $this->setSave($save, $sub, $value);
				}
				$this->setInitiative('misc', $data['initiative']['misc']);
				$this->setHP('total', $data['hp']);

				$this->setAttackBonus('base', $data['attackBonus']['base']);
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['melee]'], 'melee');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['ranged'], 'ranged');
				foreach ($data['speed'] as $key => $value) $this->setSpeed($key, $value);
				$this->setActionPoints($data['ap']);
				$this->setPassiveSenses('insight', $data['passiveSenses']['insight']);
				$this->setPassiveSenses('perception', $data['passiveSenses']['perception']);

				$this->clearVar('attacks');
				foreach ($data['attacks'] as $attack) 
					$this->addAttack($attack);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) 
					foreach ($data['skills'] as $skillInfo) 
						$this->addSkill($skillInfo);

				$this->clearVar('feats');
				if (sizeof($data['feats'])) 
					foreach ($data['feats'] as $featInfo) 
						$this->addFeat($featInfo);

				foreach (array('atwill', 'encounter', 'daily') as $type) {
					$this->powers[$type] = array();
					if (isset($data['powers'][$type])) 
						foreach ($data['powers'][$type] as $power) 
							$this->addPower($power, $type);
				}

				$this->setWeapons($data['weapons']);
				$this->setArmor($data['armor']);
				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>