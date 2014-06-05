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

		public function getSanity($key = NULL) {
			if (in_array($key, array_keys($this->sanity))) return $this->sanity[$key];
			elseif ($key == NULL) return $this->sanity;
			else return FALSE;
		}

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			if (array_key_exists($post['stat'], $this->stats)) $stat = sanitizeString($post['stat']);
			elseif ($post['stat'] == '') $stat = 'n/a';
			else return;
			$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $stat, 'ranks' => 0, 'misc' => 0);
			$addSkill = $mysql->query("INSERT INTO ".$this::SYSTEM."_skills (characterID, skillID, stat) VALUES ({$this->characterID}, $skillID, '$stat')");
			if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo, intval($post['statBonus']));
		}

		public function updateSkill($skillID, $skillInfo) {
			$updateSkill = $mysql->prepare("UPDATE ".$this::SYSTEM."_skills SET ranks = :ranks, misc = :misc WHERE characterID = :characterID AND skillID = :skillID");
			$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
			$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
			$updateSkill->bindValue(':characterID', $characterID);
			$updateSkill->bindValue(':skillID', $skillID);
			$updateSkill->execute();
		}

		public function skillEditFormat($skillInfo = NULL, $statBonus = NULL) {
			if ($statBonus == NULL) $statBonus = $this->getStatMod($skillInfo['stat']);
			else $statBonus = 0;
?>
						<div id="skill_<?=$skillInfo['skillID']?>" class="skill clearfix">
							<span class="skill_name textLabel medText"><?=mb_convert_case($skillInfo['name'], MB_CASE_TITLE)?></span>
							<span class="skill_total textLabel lrBuffer <?=$skillInfo['stat'] != 'n/a'?'addStat_'.$skillInfo['stat']:''?> shortNum"><?=showSign($statBonus + $skillInfo['ranks'] + $skillInfo['misc'])?></span>
							<span class="skill_stat textLabel lrBuffer alignCenter shortNum"><?=$skillInfo['stat'] == 'n/a'?'N/A':ucwords($skillInfo['stat'])?></span>
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][ranks]" value="<?=$skillInfo['ranks']?>" class="skill_ranks shortNum lrBuffer">
							<input type="text" name="skills[<?=$skillInfo['skillID']?>][misc]" value="<?=$skillInfo['misc']?>" class="skill_misc shortNum lrBuffer">
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}

		public function showSkillsEdit() {
			global $mysql;

			$skills = $mysql->query("SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc FROM ".$this::SYSTEM."_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
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
			$skills = $mysql->query("SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc FROM ".$this::SYSTEM."_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
			if ($skills->rowCount()) { foreach ($skills as $skill) {
?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($statBonus[$skill['stat']] + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
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

			$feats = $mysql->query("SELECT fl.featID, fl.name FROM ".$this::SYSTEM."_feats f INNER JOIN featsList fl USING (featID) WHERE f.characterID = {$this->characterID} ORDER BY fl.name");
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

			$feats = $mysql->query("SELECT f.featID, fl.name, f.notes FROM ".$this::SYSTEM."_feats f INNER JOIN featsList fl USING (featID) WHERE f.characterID = {$this->characterID} ORDER BY fl.name");
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

			if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillID => $skillInfo) {
				$this->updateSkill($skillID, $skillInfo);
			} }

			$this->clearVar('weapons');
			foreach ($data['weapons'] as $weapon) $this->addWeapon($weapon);

			$this->setItems($data['items']);
			$this->setSpells($data['spells']);
			$this->setNotes($data['notes']);

			parent::save();
		}
	}
?>