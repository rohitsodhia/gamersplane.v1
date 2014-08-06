<?
	class savageworldsCharacter extends Character {
		const SYSTEM = 'savageworlds';

		protected $stats = array('agi' => 4, 'sma' => 4, 'spi' => 4, 'str' => 4, 'vig' => 4
		);
		protected $skills = null;
		protected $derivedStats = array('pace' => 0, 'parry' => 0, 'charisma' => 0, 'toughness' => 0);
		protected $edgesHindrances = '';
		protected $wounds = 0;
		protected $fatigue = 0;
		protected $injuries = '';
		protected $weapons = '';
		protected $equipment = '';
		protected $advances = '';

		protected $linkedTables = array('skills');

		public function __construct($characterID, $userID = NULL) {
			parent::__construct($characterID, $userID);

			$this->mongoIgnore['save'][] = 'skills';
		}

		public function setStat($stat, $value = null) {
			if (array_key_exists($stat, $this->stats)) $this->stats[$stat] = intval($value);
			else return FALSE;
		}
		
		public function getStats($stat = NULL) {
			if ($stat == NULL) return $this->stats;
			elseif (array_key_exists($stat, $this->stats)) return $this->stats;
			else return FALSE;
		}

		public function addSkill($skillID, $name, $post) {
			global $mysql;

			$skillInfo = array('skillID' => $skillID, 'name' => $name, 'stat' => $post['stat']);
			if (!array_key_exists($skillInfo['stat'], $this->stats)) return false;
			try {
				$addSkill = $mysql->query("INSERT INTO ".$this::SYSTEM."_skills (characterID, skillID, diceType, stat) VALUES ({$this->characterID}, $skillID, 4, '{$skillInfo['stat']}')");
				if ($addSkill->rowCount()) $this->skillEditFormat($skillInfo, 'stat');
			} catch (Exception $e) { var_dump($e); }
		}

		public function updateSkill($skillID, $skillInfo) {
			global $mysql;

			$updateSkill = $mysql->prepare("UPDATE ".$this::SYSTEM."_skills SET diceType = :diceType WHERE characterID = :characterID AND skillID = :skillID");
			$updateSkill->bindValue(':diceType', intval($skillInfo['diceType']));
			$updateSkill->bindValue(':characterID', $this->characterID);
			$updateSkill->bindValue(':skillID', $skillID);
			$updateSkill->execute();
		}

		public function skillEditFormat($skillInfo = NULL) {
?>
								<div id="skill_<?=$skillInfo['skillID']?>" class="skill clearfix">
									<div class="traitName"><?=$skillInfo['name']?></div>
									<div class="diceSelect"><span>d</span> <select name="skills[<?=$abbrev?>]" class="diceType">
<?			foreach (array(4, 6, 8, 10, 12) as $dCount) { ?>
										<option><?=$dCount?></option>
<?			} ?>
									</select></div>
									<div class="remove"><a href="" class="sprite cross small"></a></div>
								</div>
<?
		}

		public function showSkillsEdit($stat) {
			if ($this->skills == null) {
				$this->skills = array();

				global $mysql;
				$system = $this::SYSTEM;
				$skills = $mysql->query("SELECT s.skillID, sl.name, s.stat, s.diceType FROM {$system}_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = {$this->characterID} ORDER BY sl.name");
				foreach ($skills as $skill) $this->skills[$skill['stat']][] = $skill;
			}

			if (sizeof($this->skills[$stat])) { foreach ($this->skills[$stat] as $skillInfo) {
				$this->skillEditFormat($skillInfo);
			} }
		}

		public function removeSkill($skillID) {
			global $mysql;

			$removeSkill = $mysql->query("DELETE FROM ".$this::SYSTEM."_skills WHERE characterID = {$this->characterID} AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		}

		public function displaySkills() {
			global $mysql;
			
			$skills = $mysql->query('SELECT s.skillID, sl.name, s.stat, s.ranks, s.misc FROM '.$this::SYSTEM.'_skills s INNER JOIN skillsList sl USING (skillID) WHERE s.characterID = '.$this->characterID.' ORDER BY sl.name');
			if ($skills->rowCount()) { foreach ($skills as $skill) {
?>
					<div id="skill_<?=$skill['skillID']?>" class="skill tr clearfix">
						<span class="skill_name medText"><?=mb_convert_case($skill['name'], MB_CASE_TITLE)?></span>
						<span class="skill_total addStat_<?=$skill['stat']?> shortNum lrBuffer"><?=showSign($this->getStatMod($skill['stat'], false) + $skill['ranks'] + $skill['misc'])?></span>
						<span class="skill_stat alignCenter shortNum lrBuffer"><?=ucwords($skill['stat'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['ranks'])?></span>
						<span class="skill_ranks alignCenter shortNum lrBuffer"><?=showSign($skill['misc'])?></span>
					</div>
<?
			} } else echo "\t\t\t\t\t<p id=\"noSkills\">This character currently has no skills.</p>\n";
		}

		public function setEdgesHindrances($edgesHindrances) {
			$this->edgesHindrances = $edgesHindrances;
		}

		public function getEdgesHindrances() {
			return $this->edgesHindrances;
		}

		public function setWounds($value) {
			$this->wounds = intval($wounds);
		}

		public function getWounds() {
			return $this->wounds;
		}

		public function setFatigue($fatigue) {
			$this->fatigue = intval($fatigue);
		}

		public function getFatigue() {
			return $this->fatigue;
		}

		public function setInjuries($injuries) {
			$this->injuries = $injuries;
		}

		public function getInjuries() {
			return $this->injuries;
		}

		public function setWeapons($weapons) {
			$this->weapons = $weapons;
		}

		public function getWeapons() {
			return $this->weapons;
		}

		public function setEquipment($equipment) {
			$this->equipment = $equipment;
		}

		public function getEquipment() {
			return $this->equipment;
		}

		public function setAdvances($advances) {
			$this->advances = $advances;
		}

		public function getAdvances() {
			return $this->advances;
		}

		public function save() {
			$data = $_POST;

			if (!isset($data['create'])) {
				$this->setName($data['name']);
				foreach ($data['stats'] as $stat => $value) {
					$this->setStat($stat, 'dice', $value['numDice'].'d'.$value['typeDice']);
					$this->setStat($stat, 'skills', $value['skills']);
				}
				$this->setEdgesHindrances($data['edgesHindrances']);
				$this->setWounds($data['wounds']);
				$this->setFatigue($data['fatigue']);
				$this->setInjuries($data['injuries']);
				$this->setWeapons($data['weapons']);
				$this->setEquipment($data['equipment']);
				$this->setAdvances($data['advances']);
			}

			parent::save();
		}
	}
?>