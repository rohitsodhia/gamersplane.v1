<?
	class spycraft2Character extends d20Character {
		const SYSTEM = 'spycraft2';

		protected $codename = '';
		protected $talent = '';
		protected $specialty = '';
		protected $ac = array('class' => 0, 'armor' => 0, 'dex' => 0, 'misc' => 0);
		protected $hp = array('vitality' => 0, 'wounds' => 0, 'subdual' => 0, 'stress' => 0);
		protected $initiative = array('stat' => 'dex', 'misc' => 0);
		protected $actionDie = array('number' => 1, 'type' => 4);
		protected $extraStats = array('knowledge' => 0, 'request' => 0, 'gear' => 0);
		protected $focuses = array();
		protected $weapons = array();
		protected $armor = array();
		protected $items = '';

		public function setCodename($value) {
			$this->codename = $value;
		}

		public function getCodename() {
			return $this->codename;
		}

		public function setTalent($value) {
			$this->talent = $value;
		}

		public function getTalent() {
			return $this->talent;
		}

		public function setSpecialty($value) {
			$this->specialty = $value;
		}

		public function getSpecialty() {
			return $this->specialty;
		}

		public function setActionDie($part, $value = 0) {
			if (array_key_exists($part, $this->actionDie)) {
				if ($part == 'number' && $value > 0) $this->actionDie['number'] = intval($value);
				elseif ($part == 'type' && preg_match('/d?(\d+)/', $value, $match)) {
					if (intval($match[1]) >= 4) $this->actionDie['type'] = $value;
				}
			} else return false;
		}

		public function getActionDie($part = null) {
			if ($part == null) return $this->actionDie['number'].'d'.$this->actionDie['type'];
			elseif (array_key_exists($part, $this->actionDie)) return $this->actionDie[$part];
			else return false;
		}

		public function setExtraStats($stat, $value = 0) {
			$value = (int) $value;
			if ($value < 0) $value = 0;
			if (array_key_exists($stat, $this->extraStats)) return $this->extraStats[$stat];
			else return false;
		}

		public function getExtraStats($stat = null) {
			if ($stat == null) return $this->extraStats;
			elseif (array_key_exists($stat, $this->extraStats)) return $this->extraStats[$stat];
			else return false;
		}

		public static function skillEditFormat($key = 1, $skillInfo = null, $statBonus_1 = null, $statBonus_2 = null) {
			$total_1 = $statBonus_1 + $skillInfo['ranks'] + $skillInfo['misc'];
			$total_2 = $statBonus_2 + $skillInfo['ranks'] + $skillInfo['misc'];
?>
					<div class="skill clearfix sumRow">
						<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="skill_name medText placeholder dontAdd" data-placeholder="Skill Name">
						<span class="skill_total lrBuffer shortNum"><span id="skillTotal_<?=$key?>_1" class="skill_total_1 total addStat_<?=$skillInfo['stat_1']?>"><?=showSign($total_1)?></span>/<span id="skillTotal_<?=$key?>_2" class="skill_total_2 total addStat_<?=$skillInfo['stat_2']?>"><?=showSign($total_2)?></span></span>
						<span class="skill_stat">
							<select name="skills[<?=$key?>][stat_1]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat_1']?>" data-total-ele="skillTotal_<?=$key?>_1">
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) echo "						<option value=\"$short\"".($skillInfo['stat_1'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
?>
							</select> / 
							<select name="skills[<?=$key?>][stat_2]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat_2']?>" data-total-ele="skillTotal_<?=$key?>_2">
								<option value="n/a"<?=$skillInfo['stat'] == 'n/a'?' selected="selected"':''?>>N/A</option>
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) echo "						<option value=\"$short\"".($skillInfo['stat_2'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
?>
							</select>
						</span>
						<input type="text" name="skills[<?=$key?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
						<input type="text" name="skills[<?=$key?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
						<input type="text" name="skills[<?=$key?>][error]" value="<?=$skillInfo['error']?>" class="skill_error medNum lrBuffer dontAdd">
						<input type="text" name="skills[<?=$key?>][threat]" value="<?=$skillInfo['threat']?>" class="skill_threat medNum lrBuffer dontAdd">
						<a href="" class="skill_remove sprite cross lrBuffer"></a>
					</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill, $this->getStatMod($skill['stat_1'], false), $this->getStatMod($skill['stat_2'], false));
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
				$total_1 = $this->getStatMod($skillInfo['stat_1'], false) + $skillInfo['ranks'] + $skillInfo['misc'];
				$total_2 = $this->getStatMod($skillInfo['stat_2'], false) + $skillInfo['ranks'] + $skillInfo['misc'];
?>
					<div id="skill_<?=$skillInfo['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total lrBuffer shortNum"><span class="skill_total_1 addStat_<?=$skillInfo['stat_1']?>"><?=showSign($total_1).'</span>'.($skillInfo['stat_2'] != '' ? "/<span class=\"skill_total_2 addStat_".$skillInfo['stat_2']."\">".showSign($total_2).'</span>' : '')?></span>
						<span class="skill_stat lrBuffer alignCenter shortText"><?=ucwords($skillInfo['stat_1']).($skillInfo['stat_2'] != '' ? '/'.ucwords($skillInfo['stat_2']) : '')?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skillInfo['misc'])?></span>
						<span class="skill_ranks alignCenter medNum lrBuffer"><?=$skillInfo['error']?></span>
						<span class="skill_ranks alignCenter medNum lrBuffer"><?=$skillInfo['threat']?></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addFocus($focus) {
			if (strlen($focus['name'])) {
//				newItemized('focus', $focus['name'], $this::SYSTEM);
				$this->focuses[] = $focus;
			}
		}

		static public function focusEditFormat($key = 1, $focusInfo = null) {
			if ($focusInfo == null) $focusInfo = array('name' => '', 'forte' => false);
?>
						<div class="focus tr clearfix">
							<input type="checkbox" name="focuses[<?=$key?>][forte]"<?=$focusInfo['forte']?' checked="checked"':''?>>
							<input type="text" name="focuses[<?=$key?>][name]" value="<?=$focusInfo['name']?>" class="focus_name">
							<a href="" class="skill_remove sprite cross lrBuffer"></a>
						</div>
<?
		}

		public function showFocusesEdit() {
			if (sizeof($this->focuses)) { foreach ($this->focuses as $key => $focus) {
				$this->focusEditFormat($key + 1, $focus);
			} } else $this->focusEditFormat();
		}

		public function displayFocuses() {
			if ($this->focuses) {
?>
						<div class="labelTR"><label class="shortNum alignCenter">Forte</label></div>
<?
				foreach ($this->focuses as $focusInfo) {
?>
						<div id="focus_<?=$focusInfo['focusID']?>" class="focus tr clearfix">
							<span class="shortNum alignCenter"><?=$focusInfo['forte']?'<div class="sprite check"></div>':''?></span>
							<span class="focus_name"><?=$focusInfo['name']?></span>
						</div>
<?
				}
			} else echo "\t\t\t\t\t\t<p id=\"noFocuses\">This character currently has no focuses.</p>\n";
		}

		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['ab']) && strlen($weapon['damage'])) $this->weapons[] = $weapon;
		}

		public function showWeaponsEdit($min) {
			$weaponNum = 0;
			if (!is_array($this->weapons)) $this->weapons = (array) $this->weapons;
			foreach ($this->weapons as $weaponInfo) $this->weaponEditFormat($weaponNum++, $weaponInfo);
			if ($weaponNum < $min) while ($weaponNum < $min) $this->weaponEditFormat($weaponNum++);
		}

		public function weaponEditFormat($weaponNum, $weaponInfo = array()) {
			if (!is_array($weaponInfo) || sizeof($weaponInfo) == 0) $weaponInfo = array();
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
								<label class="shortText alignCenter lrBuffer">Recoil</label>
								<label class="shortText alignCenter lrBuffer">Error/Threat</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][recoil]" value="<?=$weaponInfo['recoil']?>" class="weapon_recoil shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][et]" value="<?=$weaponInfo['et']?>" class="weapon_et shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][range]" value="<?=$weaponInfo['range']?>" class="weapon_range shortNum lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][type]" value="<?=$weaponInfo['type']?>" class="weapon_type shortNum lrBuffer">
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
							<label class="shortText alignCenter lrBuffer">Recoil</label>
							<label class="shortText alignCenter lrBuffer">Error/Threat</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_recoil shortText lrBuffer alignCenter"><?=$weaponInfo['recoil']?></span>
							<span class="weapon_et shortText lrBuffer alignCenter"><?=$weaponInfo['et']?></span>
							<span class="weapon_range shortText lrBuffer alignCenter"><?=$weaponInfo['range']?></span>
							<span class="weapon_type shortText lrBuffer alignCenter"><?=$weaponInfo['type']?></span>
							<span class="weapon_size shortText lrBuffer alignCenter"><?=$weaponInfo['size']?></span>
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
			if (strlen($armor['name']) && strlen($armor['ac'])) $this->armor[] = $armor;
		}

		public function showArmorEdit($min) {
			$armorNum = 0;
			if (!is_array($this->armor)) $this->armor = (array) $this->armor;
			foreach ($this->armor as $armorInfo) $this->armorEditFormat($armorNum++, $armorInfo);
			if ($armorNum < $min) while ($armorNum < $min) $this->armorEditFormat($armorNum++);
		}

		public function armorEditFormat($armorNum, $armorInfo = array()) {
			if (!is_array($armorInfo) || sizeof($armorInfo) == 0) $armorInfo = array();
?>
						<div class="armor<?=$armorNum == 1?' first':''?>">
							<div class="tr labelTR armor_firstRow">
								<label class="medText lrBuffer borderBox shiftRight">Name</label>
								<label class="shortText alignCenter lrBuffer">Def Reduct</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armor[<?=$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][reduction]" value="<?=$armorInfo['reduction']?>" class="armors_reduction shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Def Pen</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortText alignCenter lrBuffer">Speed</label>
								<label class="shortText alignCenter lrBuffer">Notice/Search DC</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=$armorNum?>][penalty]" value="<?=$armorInfo['penalty']?>" class="armor_penalty shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][dc]" value="<?=$armorInfo['dc']?>" class="armor_dc shortText lrBuffer">
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
			foreach ($this->armor as $armor) {
?>
					<div class="armor">
						<div class="tr labelTR armor_firstRow">
							<label class="medText lrBuffer">Name</label>
							<label class="shortText alignCenter lrBuffer">Dam Reduct</label>
							<label class="shortText alignCenter lrBuffer">Dam Resist</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armorInfo['name']?></span>
							<span class="armors_reduction shortText lrBuffer alignCenter"><?=$armorInfo['reduct']?></span>
							<span class="armor_resist shortText lrBuffer alignCenter"><?=$armorInfo['resist']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter">Def Pen</label>
							<label class="shortText alignCenter">Check Penalty</label>
							<label class="shortText alignCenter">Speed</label>
							<label class="shortText alignCenter">Notice/Search DC</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_penalty shortText alignCenter"><?=$armorInfo['penalty']?></span>
							<span class="armor_check shortText alignCenter"><?=$armorInfo['check']?></span>
							<span class="armor_speed shortText alignCenter"><?=$armorInfo['speed']?></span>
							<span class="armor_dc shortText alignCenter"><?=$armorInfo['dc']?></span>
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
			$this->items = $items;
		}

		public function getItems() {
			return $this->items;
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				$this->setCodename($data['codename']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);
				$this->setTalent($data['talent']);
				$this->setSpecialty($data['specialty']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) $this->setSave($save, $sub, $value);
				}
				$this->setHP('vitality', $data['hp']['vitality']);
				$this->setHP('wounds', $data['hp']['wounds']);
				$this->setHP('subdual', $data['hp']['subdual']);
				$this->setHP('stress', $data['hp']['stress']);
				foreach ($data['ac'] as $key => $value) $this->setAC($key, $value);
				$this->setInitiative('stat', $data['initiative']['stat']);
				$this->setInitiative('misc', $data['initiative']['misc']);
				$this->setAttackBonus('base', $data['attackBonus']['base']);
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['melee'], 'melee');
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['ranged'], 'ranged');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['melee'], 'melee');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['ranged'], 'ranged');
				$this->setActionDie('number', $data['actionDie']['number']);
				$this->setActionDie('type', $data['actionDie']['type']);
				$this->setExtraStats('knowledge', $data['extraStats']['knowledge']);
				$this->setExtraStats('request', $data['extraStats']['request']);
				$this->setExtraStats('gear', $data['extraStats']['gear']);

				$this->clearVar('skills');
				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillInfo) {
					$this->addSkill($skillInfo);
				} }

				$this->clearVar('focuses');
				if (sizeof($data['focuses'])) { foreach ($data['focuses'] as $focusInfo) {
					$this->addFocus($focusInfo);
				} }

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

				$this->clearVar('armor');
				foreach ($data['armor'] as $armor) $this->addArmor($armor);

				$this->setItems($data['items']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>