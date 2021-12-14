<?
	class dnd5Character extends d20Character {
		const SYSTEM = 'dnd5';

		protected $race = '';
		protected $background = '';
		protected $alignment = 'tn';
		protected $inspiration = 0;
		protected $profBonus = 0;
		protected $saveProf = ['str'=> false, 'dex'=> false, 'con'=> false, 'int'=> false, 'wis'=> false, 'cha'=> false];
		protected $ac = 0;
		protected $initiative = 0;
		protected $speed = 0;
		protected $hp = ['total' => 0, 'current' => 0, 'temp' => 0];
		protected $deathSaves = ['success' => 0, 'failure' => 0];
		protected $languages = '';
		protected $skills = [
			['name' => 'Acrobatics', 'stat' => 'dex'],
			['name' => 'Animal Handling', 'stat' => 'wis'],
			['name' => 'Arcana', 'stat' => 'int'],
			['name' => 'Athletics', 'stat' => 'Str'],
			['name' => 'Deception', 'stat' => 'cha'],
			['name' => 'History', 'stat' => 'int'],
			['name' => 'Insight', 'stat' => 'wis'],
			['name' => 'Intimidation', 'stat' => 'cha'],
			['name' => 'Investigation', 'stat' => 'int'],
			['name' => 'Medicine', 'stat' => 'wis'],
			['name' => 'Nature', 'stat' => 'int'],
			['name' => 'Perception', 'stat' => 'wis'],
			['name' => 'Performance', 'stat' => 'cha'],
			['name' => 'Persuasion', 'stat' => 'cha'],
			['name' => 'Religion', 'stat' => 'int'],
			['name' => 'Sleight of Hand', 'stat' => 'dex'],
			['name' => 'Stealth', 'stat' => 'dex'],
			['name' => 'Survival', 'stat' => 'wis'],
		];
		protected $spells = [];

		public function __construct($characterID = null, $userID = null) {
			unset($this->saves, $this->attackBonus);
			parent::__construct($characterID, $userID);
		}

		public function setRace($value) {
			$this->race = sanitizeString($value);
		}

		public function getRace() {
			return $this->race;
		}

		public function setBackground($value) {
			$this->background = sanitizeString($value);
		}

		public function getBackground() {
			return $this->background;
		}

		public function setAlignment($value) {
			if (dnd5_consts::getAlignments($value) && $value != null) {
				$this->alignment = $value;
			}
		}

		public function getAlignment() {
			return dnd5_consts::getAlignments($this->alignment);
		}

		public function setInspiration($value) {
			$this->inspiration = (int) $value;
		}

		public function getInspiration() {
			return $this->inspiration;
		}

		public function setProfBonus($value) {
			$this->profBonus = (int) $value;
		}

		public function getProfBonus() {
			return $this->profBonus;
		}

		public function setSaveProf($save, $value) {
			if (d20Character_consts::getStatNames($save)) {
				$this->saveProf[$save] = $value;
			} else {
				return false;
			}
		}

		public function getSaveProf($save = null) {
			if (d20Character_consts::getStatNames($save)) {
				return $this->saveProf[$save];
			} elseif ($save == null) {
				return $this->saveProf;
			} else {
				return false;
			}
		}

		public function setAC($key, $value) {
			$this->ac = (int) $value;
		}

		public function getAC($key = null) {
			return $this->ac;
		}

		public function setInitiative($key, $value) {
			$this->initiative = (int) $value;
		}

		public function getInitiative($key = null) {
			return $this->initiative;
		}

		public function setSpeed($value, $hold = null) {
			$this->speed = (int) $value;
		}

		public function getSpeed($key = null) {
			return $this->speed;
		}

		static public function skillEditFormat($key = 1, $skillInfo = null) {
			if ($skillInfo == null) {
				$skillInfo = ['name' => '', 'proficient' => false];
			}
?>
						<div class="skill clearfix">
							<span class="shortNum alignCenter skill_prof"><input type="checkbox" name="skills[<?=$key?>][proficient]"<?=$skillInfo['proficient']?' checked="checked"':''?>></span>
							<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name medText placeholder dontAdd" data-placeholder="Skill Name">
							<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
<?				foreach (d20Character_consts::getStatNames() as $short => $stat) { ?>
								<option value="<?=$short?>"<?=$skillInfo['stat'] == $short?' selected="selected"':''?>><?=ucfirst($short)?></option>
<?				} ?>
							</select></span>
						</div>
<?
		}

		public function showSkillsEdit() {
			if ($this->skills && sizeof($this->skills)) {
				foreach ($this->skills as $key => $skill) {
					$this->skillEditFormat($key + 1, $skill, $this->getStatMod($skill['stat'], false));
				}
			} else {
				$this->skillEditFormat();
			}
		}

		public function displaySkills() {
			if ($this->skills) {
				foreach ($this->skills as $skill) {
?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="shortNum alignCenter skill_prof"><?=$skill['proficient']?'<div class="sprite check"></div>':''?></span>
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_stat"><?=showSign($this->getStatMod($skill['stat'], false) + ($skill['proficient']?$this->getProfBonus():0))?> (<?=ucwords($skill['stat'])?>)</span>
					</div>
<?
				}
			} else {
				echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
			}
		}

		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['ab']) && strlen($weapon['damage'])) {
				foreach ($weapon as $key => $value) {
					$weapon[$key] = sanitizeString($value);
				}
				$this->weapons[] = $weapon;
			}
		}

		public function showWeaponsEdit($min) {
			$weaponNum = 0;
			if (!is_array($this->weapons)) {
				$this->weapons = (array) $this->weapons;
			}
			foreach ($this->weapons as $weaponInfo) {
				$this->weaponEditFormat($weaponNum++, $weaponInfo);
			}
			if ($weaponNum < $min) {
				while ($weaponNum < $min) {
					$this->weaponEditFormat($weaponNum++);
				}
			}
		}

		public function weaponEditFormat($weaponNum, $weaponInfo = []) {
			if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) {
				$weaponInfo = [];
			}
?>
						<div class="weapon">
							<div class="tr labelTR">
								<label class="medText lrBuffer shiftRight borderBox">Name</label>
								<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
								<label class="shortText alignCenter lrBuffer">Damage</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][name]" value="<?=$weaponInfo['name']?>" class="weapon_name medText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][ab]" value="<?=$weaponInfo['ab']?>" class="weapons_ab shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][damage]" value="<?=$weaponInfo['damage']?>" class="weapon_damage shortText lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="weapons[<?=$weaponNum?>][notes]" value="<?=$weaponInfo['notes']?>" class="weapon_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
		}

		public function displayWeapons() {
			if ($this->weapons && sizeof($this->weapons)) {
				foreach ($this->weapons as $weapon) {
?>
					<div class="weapon">
						<div class="tr labelTR">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Attack Bonus</label>
							<label class="shortText alignCenter lrBuffer">Damage</label>
						</div>
						<div class="tr">
							<span class="weapon_name medText lrBuffer"><?=$weapon['name']?></span>
							<span class="weapons_ab shortText lrBuffer alignCenter"><?=$weapon['ab']?></span>
							<span class="weapon_damage shortText lrBuffer alignCenter"><?=$weapon['damage']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="weapon_notes lrBuffer"><?=$weapon['notes']?></span>
						</div>
					</div>
<?
				}
			}
		}

		public function setSpellStats($stat, $value = null) {
			if (is_array($stat)) {
				$this->spellStats = $stat;
			} elseif (in_array($stat, $this->spellStats)) {
				$this->spellStats[$stat] = $value;
			}
		}

		public function getSpellStats($stat = null) {
			if ($stat == null) {
				return $this->spellStats;
			} elseif (in_array($stat, $this->spellStats)) {
				return $this->spellStats[$stat];
			}
		}

		public static function spellEditFormat($key = 1, $spellInfo = null) {
			if ($spellInfo == null) {
				$spellInfo = ['name' => '', 'notes' => ''];
			}
?>
							<div class="spell clearfix tr">
								<input type="text" name="spells[<?=$key?>][name]" value="<?=$spellInfo['name']?>" class="spell_name placeholder" data-placeholder="Spell Name">
								<span class="spell_stat"><select name="spells[<?=$key?>][stat]">
<?			foreach (array_keys(d20Character_consts::getStatNames()) as $stat) { ?>
									<option value="<?=$stat?>"<?=$spellInfo['stat'] == $stat?' selected="selected"':''?>><?=ucfirst($stat)?></option>
<?			} ?>
								</select></span>
								<a href="" class="spell_notesLink">Notes</a>
								<a href="" class="spell_remove sprite cross"></a>
								<textarea name="spells[<?=$key?>][notes]"><?=$spellInfo['notes']?></textarea>
							</div>
<?
		}

		public function showSpellsEdit() {
			if ($this->spells && sizeof($this->spells)) {
				foreach ($this->spells as $key => $spell) {
					$this->spellEditFormat($key + 1, $spell);
				}
			} else {
				$this->spellEditFormat();
			}
		}

		public function displaySpells() {
			if ($this->spells) {
				foreach ($this->spells as $spell) {
?>
					<div class="spell tr clearfix">
						<span class="spell_name"><?=$spell['name']?> (<?=ucwords($spell['stat'])?>)</span>
						<span class="spell_ab shortNum"><?=showSign($this->getStatMod($spell['stat'], false) + $this->getProfBonus())?></span>
						<span class="spell_save shortNum"><?=showSign($this->getStatMod($spell['stat'], false) + $this->getProfBonus() + 8)?></span>
<?	if (strlen($spell['notes'])) { ?>
						<a href="" class="spell_notesLink">Notes</a>
						<div class="spell_notes"><?=printReady($spell['notes'])?></div>
<?	} ?>
					</div>
<?
				}
			} else {
				echo "\t\t\t\t\t<p id=\"noSpells\">This character currently has no spells/abilities.</p>\n";
			}
		}

		public function addSpell($spell) {
			if (strlen($spell['name'])) {
				newItemized('spell', $spell['name'], $this::SYSTEM);
				$this->spells[] = $spell;
			}
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				$this->setBackground($data['background']);
				foreach ($data['class'] as $key => $value) {
					if (strlen($value) && (int) $data['level'][$key] > 0) {
						$data['classes'][$value] = $data['level'][$key];
					}
				}
				$this->setClasses($data['classes']);
				$this->setAlignment($data['alignment']);

				$this->setInspiration($data['inspiration']);
				$this->setProfBonus($data['profBonus']);
				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, $value);
					$this->setSaveProf($stat, isset($data['statProf'][$stat]));
				}
				$this->setHP('total', $data['hp']['total']);
				$this->setHP('temp', $data['hp']['temp']);
				$this->setHP('current', $data['hp']['current']);
				$this->setAC(null, $data['ac']);
				$this->setInitiative(null, $data['initiative']);
				$this->setSpeed($data['speed']);

				$this->clearVar('skills');
				if ($data['skills'] && sizeof($data['skills'])) {
					foreach ($data['skills'] as $skillInfo) {
						$this->addSkill($skillInfo);
					}
				}

				$this->clearVar('feats');
				if ($data['feats'] && sizeof($data['feats'])) {
					foreach ($data['feats'] as $featInfo) {
						$this->addFeat($featInfo);
					}
				}

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) {
					$this->addWeapon($weapon);
				}

				$this->clearVar('spells');
				if ($data['spells'] && sizeof($data['spells'])) {
					foreach ($data['spells'] as $spellInfo) {
						$this->addSpell($spellInfo);
					}
				}

				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
