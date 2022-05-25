<?
	class dnd3Character extends d20Character {
		const SYSTEM = 'dnd3';

		protected $race = '';
		protected $size = 0;
		protected $alignment = 'tn';
		protected $saves = [
			'fort' => ['base' => 0, 'stat' => 'con', 'magic' => 0, 'race' => 0, 'misc' => 0],
			'ref' => ['base' => 0, 'stat' => 'dex', 'magic' => 0, 'race' => 0, 'misc' => 0],
			'will' => ['base' => 0, 'stat' => 'wis', 'magic' => 0, 'race' => 0, 'misc' => 0]
		];
		protected $ac = ['armor' => 0, 'shield' => 0, 'dex' => 0, 'class' => 0, 'natural' => 0, 'deflection' => 0, 'misc' => 0];
		protected $hp = ['total' => 0, 'current' => 0, 'subdual' => 0];
		protected $damageReduction = '';
		protected $initiative = ['stat' => 'dex', 'misc' => 0];
		protected $weapons = [];
		protected $armor = [];
		protected $spells = '';

		public function setRace($value) {
			$this->race = sanitizeString($value);
		}

		public function getRace() {
			return $this->race;
		}

		public function setSize($value) {
			$value = (int) $value;
			$this->size = $value;
		}

		public function getSize() {
			return $this->size;
		}

		public function setAlignment($value) {
			if (dnd3_consts::getAlignments($value) && $value != null) {
				$this->alignment = $value;
			}
		}

		public function getAlignment() {
			return dnd3_consts::getAlignments($this->alignment);
		}

		public function setDamageReduction($value) {
			$this->damageReduction = sanitizeString($value);
		}

		public function getDamageReduction() {
			return $this->damageReduction;
		}

		public static function skillEditFormat($key = null, $skillInfo = null, $statBonus = null) {
			if ($key == null) {
				$key = 1;
			}
			if ($skillInfo == null) {
				$skillInfo = ['name' => '', 'stat' => 'str', 'ranks' => 0, 'misc' => 0];
			}
			if ($skillInfo['stat'] == null || $statBonus == null) {
				$statBonus = 0;
			}
?>
							<div class="skill clearfix sumRow">
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name medText placeholder dontAdd" data-placeholder="Skill Name">
								<span id="skillTotal_<?=$key?>" class="skill_total textLabel lrBuffer total addStat_<?=$skillInfo['stat']?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
								<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) {
		echo "								<option value=\"$short\"".($skillInfo['stat'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
	}
?>
								</select></span>
								<input type="text" name="skills[<?=$key?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
								<input type="text" name="skills[<?=$key?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
								<a href="" class="remove sprite cross lrBuffer"></a>
							</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) {
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
					<div class="skill tr clearfix">
						<span class="skill_name medText"><?=$skill['name']?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($this->getStatMod($skill['stat'], false) + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
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
				$this->weapons = $this->weapons;
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
							<div class="tr labelTR weapon_secondRow">
								<label class="shortText alignCenter lrBuffer">Critical</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][crit]" value="<?=$weaponInfo['crit']?>" class="weapon_crit shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][size]" value="<?=$weaponInfo['size']?>" class="weapon_size shortNum lrBuffer">
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
			if (sizeof($this->weapons) === 0) {
				return null;
			}
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
						<div class="tr labelTR weapon_secondRow">
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['crit']?></span>
							<span class="weapon_range shortText lrBuffer alignCenter"><?=$weapon['range']?></span>
							<span class="weapon_type shortText lrBuffer alignCenter"><?=$weapon['type']?></span>
							<span class="weapon_size shortText lrBuffer alignCenter"><?=$weapon['size']?></span>
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

		public function addArmor($armor) {
			if (strlen($armor['name']) && strlen($armor['ac'])) {
				foreach ($armor as $key => $value) {
					$armor[$key] = sanitizeString($value);
				}
				$this->armor[] = $armor;
			}
		}

		public function showArmorEdit($min) {
			$armorNum = 0;
			if (!is_array($this->armor)) {
				$this->armor = $this->armor;
			}
			foreach ($this->armor as $armorInfo) {
				$this->armorEditFormat($armorNum++, $armorInfo);
			}
			if ($armorNum < $min) {
				while ($armorNum < $min) {
					$this->armorEditFormat($armorNum++);
				}
			}
		}

		public function armorEditFormat($armorNum, $armorInfo = []) {
			if (!is_array($armorInfo) || sizeof($armorInfo) == 0) {
				$armorInfo = [];
			}
?>
						<div class="armor<?=$armorNum == 1?' first':''?>">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer borderBox shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">AC Bonus</label>
								<label class="shortText alignCenter lrBuffer">Max Dex</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armor[<?=$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armor[<?=$armorNum?>][ac]" value="<?=$armorInfo['ac']?>" class="armor_ac shortText lrBuffer">
								<input type="text" name="armor[<?=$armorNum?>][maxDex]" value="<?=$armorInfo['maxDex']?>" class="armor_maxDex shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortText alignCenter lrBuffer">Spell Failure</label>
								<label class="shortNum alignCenter lrBuffer">Speed</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armor[<?=$armorNum?>][type]" value="<?=$armorInfo['type']?>" class="armor_type shortText lrBuffer">
								<input type="text" name="armor[<?=$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armor[<?=$armorNum?>][spellFailure]" value="<?=$armorInfo['spellFailure']?>" class="armor_spellFailure shortText lrBuffer">
								<input type="text" name="armor[<?=$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortNum lrBuffer">
							</div>
							<div class="tr labelTR">
								<label class="lrBuffer shiftRight">Notes</label>
							</div>
							<div class="tr">
								<input type="text" name="armor[<?=$armorNum?>][notes]" value="<?=$armorInfo['notes']?>" class="armor_notes lrBuffer">
							</div>
							<div class="tr alignRight lrBuffer"><a href="" class="remove">[ Remove ]</a></div>
						</div>
<?
		}

		public function displayArmor() {
			if (sizeof($this->armor) === 0) {
				return null;
			}
			foreach ($this->armor as $armor) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">AC Bonus</label>
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armor['name']?></span>
							<span class="armors_ac shortText lrBuffer alignCenter"><?=$armor['ac']?></span>
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armor['maxDex']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
							<label class="shortText alignCenter lrBuffer">Spell Failure</label>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_type shortText lrBuffer alignCenter"><?=$armor['type']?></span>
							<span class="armor_check shortText lrBuffer alignCenter"><?=$armor['check']?></span>
							<span class="armor_spellFailure shortText lrBuffer alignCenter"><?=$armor['spellFailure']?></span>
							<span class="armor_speed shortText lrBuffer alignCenter"><?=$armor['speed']?></span>
						</div>
						<div class="tr labelTR">
							<label class="lrBuffer">Notes</label>
						</div>
						<div class="tr">
							<span class="armor_notes lrBuffer"><?=$armor['notes']?></span>
						</div>
					</div>
<?
			}
		}

		public function setItems($items) {
			$this->items = sanitizeString($items);
		}

		public function getItems() {
			return $this->items;
		}

		public function setSpells($spells) {
			$this->spells = sanitizeString($spells);
		}

		public function getSpells() {
			return $this->spells;
		}

		public function getAC($key = null) {
			$ac = parent::getAC($key);
			if(is_numeric($ac) && $key=='total'){
				$ac+=$this->getSize();
			}
			return $ac;
		}

		public function getAttackBonus($key = null, $type = null) {
			$attackBonus=parent::getAttackBonus($key,$type);
			if(is_numeric($attackBonus) && $key=='total'){
				$attackBonus+=$this->getSize();
			}

			return $attackBonus;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				$this->setRace($data['race']);
				$this->setSize($data['size']);
				foreach ($data['class'] as $key => $value) {
					if (strlen($value) && (int) $data['level'][$key] > 0) {
						$data['classes'][$value] = $data['level'][$key];
					}
				}
				$this->setClasses($data['classes']);
				$this->setAlignment($data['alignment']);

				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, $value);
				}
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) {
						$this->setSave($save, $sub, $value);
					}
				}
				$this->setHP('total', $data['hp']['total']);
				$this->setDamageReduction($data['damageReduction']);
				foreach ($data['ac'] as $key => $value) {
					$this->setAC($key, $value);
				}
				$this->setInitiative('stat', $data['initiative']['stat']);
				$this->setInitiative('misc', $data['initiative']['misc']);
				$this->setAttackBonus('base', $data['attackBonus']['base']);
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['melee'], 'melee');
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['ranged'], 'ranged');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['melee'], 'melee');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['ranged'], 'ranged');

				$this->clearVar('skills');
				if ($data['skills'] && sizeof($data['skills'])) {
					foreach ($data['skills'] as $skillInfo) {
						$this->addSkill($skillInfo);
					}
				}

				$this->clearVar('feats');
				if ($data['feats'] && sizeof($data['feats'])) {
					foreach ($data['feats'] as $featInfo){
						$this->addFeat($featInfo);
					}
				}

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) {
					$this->addWeapon($weapon);
				}

				$this->clearVar('armor');
				foreach ($data['armor'] as $armor) {
					$this->addArmor($armor);
				}

				$this->setItems($data['items']);
				$this->setSpells($data['spells']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>
