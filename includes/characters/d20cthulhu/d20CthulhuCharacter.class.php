<?
	class d20cthulhuCharacter extends d20Character {
		const SYSTEM = 'd20cthulhu';

		protected $professions = array();
		protected $ac = array('armor' => 0, 'dex' => 0, 'misc' => 0);
		protected $hp = array('total' => 0, 'current' => 0, 'subdual' => 0);
		protected $sanity = array('max' => 0, 'current' => 0);
		protected $initiative = array('misc' => 0);
		protected $attackBonus = array('base' => 0, 'misc' => array('melee' => 0, 'ranged' => 0));
		protected $weapons = array();
		protected $spells = '';

		public function setProfession($profession, $level = 1) {
			$this->professions[$profession] = $level;
		}

		public function removeProfession($profession) {
			if (isset($this->professions[$profession])) unset($this->professions[$profession]);
		}
		
		public function getProfessions($profession = NULL) {
			if ($profession == NULL) return $this->professions;
			elseif (in_array($profession, array_keys($this->professions))) return $this->professions[$profession];
			else return FALSE;
		}

		public function displayProfessions() {
			array_walk($this->professions(), function ($value, $key) {
				echo $key.' - '.$value.'<br>';
			});
		}

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
			$addSkill = $mysql->query("INSERT INTO ".self::SYSTEM."_skills (characterID, skillID, stat) VALUES ({$this->characterID}, $skillID, '$stat')");
			if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo, intval($post['statBonus']));
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
							<input type="image" name="skill<?=$skillInfo['skillID']?>_remove" src="<?=SITEROOT?>/images/cross.png" value="<?=$skillInfo['skillID']?>" class="skill_remove lrBuffer">
						</div>
<?
		}

		public function showSkillsEdit() {
			global $mysql;

			$system = self::SYSTEM;
			$skills = $mysql->query("SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
			if ($skills->rowCount()) { foreach ($skills as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} } else { ?>
						<p id="noSkills">This character currently has no skills.</p>
<?
			}
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".self::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills() {
			global $mysql;
			$skills = $mysql->query('SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc FROM '.self::SYSTEM.'_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = '.$this->characterID.' ORDER BY sl.name');
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
			$addFeat = $mysql->query("INSERT INTO ".self::SYSTEM."_feats (characterID, featID) VALUES ({$this->characterID}, $featID)");
			if ($addFeat->rowCount()) $this->featEditFormat($featInfo);
		}

		public function featEditFormat($featInfo) {
?>
						<div id="feat_<?=$featInfo['featID']?>" class="feat clearfix">
							<span class="feat_name textLabel"><?=mb_convert_case($featInfo['name'], MB_CASE_TITLE)?></span>
							<a href="<?=SITEROOT?>/characters/<?=self::SYSTEM?>/<?=$this->characterID?>/editFeatNotes/<?=$featInfo['featID']?>" id="featNotesLink_<?=$featInfo['featID']?>" class="feat_notesLink">Notes</a>
							<input type="image" name="featRemove_<?=$featInfo['featID']?>" src="<?=SITEROOT?>/images/cross.png" value="<?=$featInfo['featID']?>" class="feat_remove lrBuffer">
						</div>
<?
		}

		public function showFeatsEdit() {
			global $mysql;

			$system = self::SYSTEM;
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

			$removeFeat = $mysql->query("DELETE FROM ".self::SYSTEM."_feats WHERE characterID = {$this->characterID} AND featID = $featID");
			if ($removeFeat->rowCount()) echo 1;
			else echo 0;
		}

		public function displayFeats() {
			global $mysql;

			$feats = $mysql->query('SELECT f.featID, fl.name, f.notes FROM '.self::SYSTEM.'_feats f INNER JOIN featsList fl USING (featID) WHERE f.characterID = '.$this->characterID.' ORDER BY fl.name');
			if ($feats->rowCount()) { foreach ($feats as $feat) { ?>
					<div id="feat_<?=$feat['featID']?>" class="feat tr clearfix">
						<span class="feat_name"><?=mb_convert_case($feat['name'], MB_CASE_TITLE)?></span>
						<a href="<?=SITEROOT?>/characters/<?=self::SYSTEM?>/<?=$this->characterID?>/featNotes/<?=$feat['featID']?>" class="feat_notesLink">Notes</a>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noFeats\">This character currently has no feats/abilities.</p>\n";
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

		public function getItems() {
			return $this->items;
		}

		public function getSpells() {
			return $this->spells;
		}

		public function save() {
			global $mysql;

			$data = $_POST;
			foreach ($data['profession'] as $key => $value) if (strlen($value) && (int) $data['level'][$key] > 0) $data['professions'][$value] = $data['level'][$key];

			$updateSkill = $mysql->prepare("UPDATE ".self::SYSTEM."_skills SET ranks = :ranks, misc = :misc WHERE characterID = :characterID AND skillID = :skillID");
			if (sizeof($data['skills'])) { foreach ($data['skills'] as $skillID => $skillInfo) {
				$updateSkill->bindValue(':ranks', intval($skillInfo['ranks']));
				$updateSkill->bindValue(':misc', intval($skillInfo['misc']));
				$updateSkill->bindValue(':characterID', $characterID);
				$updateSkill->bindValue(':skillID', $skillID);
				$updateSkill->execute();
			} }

			$weaponsTmp = array();
			foreach ($data['weapons'] as $weapon) { if (strlen($weapon['name']) && strlen($weapon['ab']) && strlen($weapon['damage'])) $weaponsTmp[] = $weapon; }
			$data['weapons'] = $weaponsTmp;

			unset($data['save'], $data['characterID'], $data['system'], $data['profession'], $data['level'], $data['newSkill'], $data['newFeat_name'], $data['skills'], $data['save']);

			foreach ($data as $key => $value) if (isset($this->$key)) $this->$key = $value;

			parent::save();
		}

		public function delete() {
			$mysql->query('DELETE FROM '.self::SYSTEM.'_skills WHERE characterID = '.$this->characterID);
			$mysql->query('DELETE FROM '.self::SYSTEM.'_feats WHERE characterID = '.$this->characterID);
			
			parent::delete();
		}
	}
?>