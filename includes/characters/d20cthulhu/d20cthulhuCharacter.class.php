<?
	class d20cthulhuCharacter extends d20Character {
		const SYSTEM = 'd20cthulhu';

		protected $ac = array('armor' => 0, 'dex' => 0, 'misc' => 0);
		protected $hp = array('total' => 0, 'current' => 0, 'subdual' => 0);
		protected $sanity = array('max' => 0, 'current' => 0);
		protected $weapons = array();
		protected $spells = '';

		public function setSanity($key, $value) {
			if (in_array($key, array_keys($this->sanity))) $this->sanity[$key] = intval($value);
			else return FALSE;
		}

		public function getSanity($key = null) {
			if (in_array($key, array_keys($this->sanity))) return $this->sanity[$key];
			elseif ($key == null) return $this->sanity;
			else return FALSE;
		}

		public static function skillEditFormat($key = null, $skillInfo = null, $statBonus = null) {
			if ($key == null) $key = 1;
			if ($skillInfo == null) $skillInfo = array('name' => '', 'stat' => 'n/a', 'ranks' => 0, 'misc' => 0);
			if ($skillInfo['stat'] == null || $skillInfo['stat'] == 'n/a' || $statBonus == null) $statBonus = 0;
?>
						<div class="skill clearfix sumRow">
							<a href="" class="edit sprite pencil small"></a>
							<span class="skill_name textLabel medText">
								<span><?=$skillInfo['name']?></span>
								<input type="text" name="skills[<?=$key?>][name]" value="<?=$skillInfo['name']?>" class="medText placeholder dontAdd" data-placeholder="Skill Name">
							</span>
							<span id="skillTotal_<?=$key?>" class="skill_total textLabel lrBuffer total<?=$skillInfo['stat'] != 'n/a'?' addStat_'.$skillInfo['stat']:''?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat"><select name="skills[<?=$key?>][stat]" class="abilitySelect" data-stat-hold="<?=$skillInfo['stat']?>" data-total-ele="skillTotal_<?=$key?>">
								<option value="n/a"<?=$skillInfo['stat'] == 'n/a'?' selected="selected"':''?>>N/A</option>
<?
	foreach (d20Character_consts::getStatNames() as $short => $stat) echo "							<option value=\"$short\"".($skillInfo['stat'] == $short?' selected="selected"':'').">".ucfirst($short)."</option>\n";
?>
							</select></span>
							<input type="text" name="skills[<?=$key?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$key?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<a href="" class="skill_remove sprite cross lrBuffer"></a>
						</div>
<?
		}

		public function showSkillsEdit() {
			if (sizeof($this->skills)) { foreach ($this->skills as $key => $skill) {
				$this->skillEditFormat($key + 1, $skill, $this->getStatMod($skill['stat']));
			} } else $this->skillEditFormat();
		}

		public function displaySkills() {
			if ($this->skills) { foreach ($this->skills as $skill) {
?>
					<div class="skill tr clearfix">
						<span class="skill_name medText"><?=$skill['name']?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($this->getStatMod($skill['stat'], false) + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=$skill['stat'] == 'n/a'?'N/A':ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}
		
		public function addWeapon($weapon) {
			if (strlen($weapon['name']) && strlen($weapon['ab']) && strlen($weapon['damage'])) $this->weapons[] = $weapon;
		}

		public function showWeaponsEdit($min) {
			$weaponNum = 0;
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
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['critical']?></span>
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

		public function setItems($items) {
			$this->items = $items;
		}

		public function getItems() {
			return $this->items;
		}

		public function setSpells($spells) {
			$this->spells = $spells;
		}

		public function getSpells() {
			return $this->spells;
		}

		public function save() {
			global $mysql;
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['class'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['classes'][$value] = $data['level'][$key];
				$this->setClasses($data['classes']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) $this->setSave($save, $sub, $value);
				}
				$this->setHP('total', $data['hp']['total']);
				$this->setHP('subdual', $data['hp']['subdual']);
				$this->setSanity('max', $data['hp']['max']);
				$this->setSanity('current', $data['hp']['current']);
				foreach ($data['ac'] as $key => $value) $this->setAC($key, $value);
				$this->setSpeed($data['speed']);
				$this->setInitiative('stat', $data['initiative']['stat']);
				$this->setInitiative('misc', $data['initiative']['misc']);
				$this->setAttackBonus('base', $data['attackBonus']['base']);
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['melee'], 'melee');
				$this->setAttackBonus('stat', $data['attackBonus']['stat']['ranged'], 'ranged');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['melee]'], 'melee');
				$this->setAttackBonus('misc', $data['attackBonus']['misc']['ranged'], 'ranged');

				$this->clearVar('skills');
				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillInfo) {
					if (strlen($skillInfo['name'])) $this->addSkill($skillInfo);
				} }

				$this->clearVar('feats');
				if (sizeof($data['feats'])) { foreach ($data['feats'] as $featInfo) {
					if (strlen($featInfo['name'])) $this->addFeat($featInfo);
				} }

				$this->clearVar('weapons');
				foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

				$this->setItems($data['items']);
				$this->setSpells($data['spells']);
				$this->setNotes($data['notes']);
			}

			parent::save();
		}
	}
?>