<?
	class spycraftCharacter extends d20Character {
		const SYSTEM = 'spycraft';

		protected $codename = '';
		protected $department = '';
		protected $ac = array('armor' => 0, 'dex' => 0, 'size' => 0, 'misc' => 0);
		protected $hp = array('vitality' => 0, 'wounds' => 0);
		protected $initiative = array('stat' => 'dex', 'misc' => 0);
		protected $actionDie = array('number' => 1, 'type' => 4);
		protected $extraStats = array('inspiration' => 0, 'education' => 0);
		protected $weapons = array();
		protected $armor = array();
		protected $items = array();

		protected $linkedTables = array('feats', 'skills');

		public function setCodename($value) {
			$this->codename = $value;
		}

		public function getCodename() {
			return $this->codename;
		}

		public function setDepartment($value) {
			$this->department = $value;
		}

		public function getDepartment() {
			return $this->department;
		}

		public function setActionDie($part, $value = 0) {
			$value = (int) $value;
			if (array_key_exists($part, $this->actionDie)) {
				if ($part == 'number' && $value > 0) $this->actionDie['number'] = $value;
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

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			if (array_key_exists($post['stat'], $this->stats)) $stat = sanitizeString($post['stat']);
			else return;
			$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $post['stat'], 'ranks' => 0, 'misc' => 0);
			try {
				$addSkill = $mysql->query("INSERT INTO ".$this::SYSTEM."_skills (characterID, skillID, stat) VALUES ({$this->characterID}, $skillID, '$stat')");
				if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo, intval($post['statBonus']));
			} catch (Exception $e) {}
		}

		public function updateSkill($skillID, $skillInfo) {
			$updateSkill = $mysql->prepare("UPDATE spycraft_skills SET ranks = :ranks, misc = :misc, error = :error, threat = :threat WHERE characterID = :characterID AND skillID = :skillID");
			$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
			$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
			$updateSkill->bindValue(':error', sanitizeString($skillInfo['error']));
			$updateSkill->bindValue(':threat', sanitizeString($skillInfo['threat']));
			$updateSkill->bindValue(':characterID', $this->characterID);
			$updateSkill->bindValue(':skillID', $skillID);
			$updateSkill->execute();
		}

		public function skillEditFormat($skillInfo = null, $statBonus = null) {
			if ($statBonus == null) $statBonus = $this->getStatMod($skillInfo['stat'], false);
			else $statBonus = 0;
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill clearfix">
							<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
							<span class="skill_total textLabel lrBuffer total <?=$skillInfo['stat'] != 'n/a'?'addStat_'.$skillInfo['stat']:''?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat textLabel lrBuffer alignCenter shortNum"><?=ucwords($skillInfo['stat'])?></span>
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][error]" value="<?=$skillInfo['error']?>" class="skill_error medNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][threat]" value="<?=$skillInfo['threat']?>" class="skill_threat medNum lrBuffer">
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}

		public function showSkillsEdit() {
			global $mysql;

			$system = $this::SYSTEM;
			$skills = $mysql->query("SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc, s.error, s.threat FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
			if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?
			}
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".$this::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills() {
			global $mysql;
			$skills = $mysql->query('SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc, s.error, s.threat FROM '.$this::SYSTEM.'_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = '.$this->characterID.' ORDER BY sl.name');
			if ($skills->rowCount()) { foreach ($skills as $skill) {
?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($this->getStatMod($skill['stat']) + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_error alignCenter medNum lrBuffer"><?=$skillInfo['error']?></span>
						<span class="skill_threat alignCenter medNum lrBuffer"><?=$skillInfo['threat']?></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function addFeat($featID, $name) {
			global $mysql;

			$featInfo = array('featID' => $featID, 'name' => $name);
			$addFeat = $mysql->query("INSERT INTO ".$this::SYSTEM."_feats (characterID, featID) VALUES ({$this->characterID}, $featID)");
			if ($addFeat->rowCount()) $this->featEditFormat($featInfo);
		}

		public function featEditFormat($featInfo) {
?>
						<div id="feat_<?=$featInfo['featID']?>" class="feat clearfix">
							<span class="feat_name textLabel"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
							<a href="/characters/<?=$this::SYSTEM?>/<?=$this->characterID?>/editFeatNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
							<input type="image" name="featRemove_<?=$featInfo['featID']?>" src="/images/cross.png" value="<?=$featInfo['featID']?>" class="feat_remove lrBuffer">
						</div>
<?
		}

		public function showFeatsEdit() {
			global $mysql;

			$system = $this::SYSTEM;
			$feats = $mysql->query("SELECT fl.featID, fl.name FROM {$system}_feats f INNER JOIN featsList fl USING (featID) WHERE f.characterID = {$this->characterID} ORDER BY fl.name");
			if ($feats->rowCount()) { foreach ($feats as $featInfo) {
				$this->featEditFormat($featInfo);
			} } else { ?>
					<p id="noFeats">This character currently has no feats/abilities.</p>
<?
			}
		}

		public function removeFeat($featID) {
			global $mysql;

			$removeFeat = $mysql->query("DELETE FROM ".$this::SYSTEM."_feats WHERE characterID = {$this->characterID} AND featID = $featID");
			if ($removeFeat->rowCount()) echo 1;
			else echo 0;
		}

		public function displayFeats() {
			global $mysql;

			$feats = $mysql->query('SELECT f.featID, fl.name, f.notes FROM '.$this::SYSTEM.'_feats f INNER JOIN featsList fl USING (featID) WHERE f.characterID = '.$this->characterID.' ORDER BY fl.name');
			if ($feats->rowCount()) { foreach ($feats as $feat) { ?>
					<div id="feat_<?=$feat['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($feat['name'], MB_CASE_TITLE)?></span>
						<a href="/characters/<?=$this::SYSTEM?>/<?=$this->characterID?>/featNotes/<?=$feat['featID']?>" class="feat_notesLink">Notes</a>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
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
								<label class="shortText alignCenter lrBuffer">Error</label>
								<label class="shortText alignCenter lrBuffer">Threat</label>
								<label class="shortText alignCenter lrBuffer">Range</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortNum alignCenter lrBuffer">Size</label>
							</div>
							<div class="tr weapon_secondRow">
								<input type="text" name="weapons[<?=$weaponNum?>][error]" value="<?=$weaponInfo['error']?>" class="weapon_error shortText lrBuffer">
								<input type="text" name="weapons[<?=$weaponNum?>][threat]" value="<?=$weaponInfo['threat']?>" class="weapon_crit shortText lrBuffer">
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
							<label class="shortText alignCenter lrBuffer">Error</label>
							<label class="shortText alignCenter lrBuffer">Critical</label>
							<label class="shortText alignCenter lrBuffer">Range</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortNum alignCenter lrBuffer">Size</label>
						</div>
						<div class="tr weapon_secondRow">
							<span class="weapon_crit shortText lrBuffer alignCenter"><?=$weapon['error']?></span>
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
								<label class="shortText alignCenter lrBuffer">Def Bonus</label>
								<label class="shortText alignCenter lrBuffer">Dam Resist</label>
							</div>
							<div class="tr armor_firstRow">
								<input type="text" name="armor[<?=$armorNum?>][name]" value="<?=$armorInfo['name']?>" class="armor_name medText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][def]" value="<?=$armorInfo['def']?>" class="armors_def shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][resist]" value="<?=$armorInfo['resist']?>" class="armors_resist shortText lrBuffer">
							</div>
							<div class="tr labelTR armor_secondRow">
								<label class="shortText alignCenter lrBuffer">Max Dex</label>
								<label class="shortText alignCenter lrBuffer">Type</label>
								<label class="shortText alignCenter lrBuffer">Check Penalty</label>
								<label class="shortNum alignCenter lrBuffer">Speed</label>
							</div>
							<div class="tr armor_secondRow">
								<input type="text" name="armors[<?=$armorNum?>][maxDex]" value="<?=$armorInfo['maxDex']?>" class="armor_maxDex shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][type]" value="<?=$armorInfo['type']?>" class="armor_type shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][check]" value="<?=$armorInfo['check']?>" class="armor_check shortText lrBuffer">
								<input type="text" name="armors[<?=$armorNum?>][speed]" value="<?=$armorInfo['speed']?>" class="armor_speed shortNum lrBuffer">
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
							<label class="shortText alignCenter lrBuffer">Def Bonus</label>
							<label class="shortText alignCenter lrBuffer">Dam Resist</label>
						</div>
						<div class="tr armor_firstRow">
							<span class="armor_name medText lrBuffer"><?=$armor['name']?></span>
							<span class="armors_ac shortText lrBuffer alignCenter"><?=$armor['def']?></span>
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armor['resist']?></span>
						</div>
						<div class="tr labelTR armor_secondRow">
							<label class="shortText alignCenter lrBuffer">Max Dex</label>
							<label class="shortText alignCenter lrBuffer">Type</label>
							<label class="shortText alignCenter lrBuffer">Check Penalty</label>
							<label class="shortNum alignCenter lrBuffer">Speed</label>
						</div>
						<div class="tr armor_secondRow">
							<span class="armor_maxDex shortText lrBuffer alignCenter"><?=$armor['maxDex']?></span>
							<span class="armor_type shortText lrBuffer alignCenter"><?=$armor['type']?></span>
							<span class="armor_check shortText lrBuffer alignCenter"><?=$armor['check']?></span>
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
				$this->setDepartment($data['department']);

				foreach ($data['stats'] as $stat => $value) $this->setStat($stat, $value);
				foreach ($data['saves'] as $save => $values) {
					foreach ($values as $sub => $value) $this->setSave($save, $sub, $value);
				}
				$this->setHP('vitality', $data['hp']['vitality']);
				$this->setHP('wounds', $data['hp']['wounds']);
				$this->setSpeed($data['speed']);
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
				$this->setExtraStats('inspiration', $data['extraStats']['inspiration']);
				$this->setExtraStats('education', $data['extraStats']['education']);

				if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillID => $skillInfo) {
					$this->updateSkill($skillID, $skillInfo);
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