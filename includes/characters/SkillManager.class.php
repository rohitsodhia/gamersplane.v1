<?
	class SkillManager {
		protected $characterID;
		protected $system;
		protected $skills;
		
		public function __construct($characterID, $system) {
			$this->characterID = $characterID;
			$this->system = $system;
		}

		public function loadSkills() {
			global $mysql;
			$allSkills = $mysql->query("SELECT * FROM {$this->system}_skills WHERE characterID = {$this->characterID}");
			foreach ($allSkills as $skill) {
				$this->skills[$skillID] = $skill['skillID'];
				unset($this->skills[$skillID]['characterID'], $this->skills[$skillID]['skillID']);
			}
		}

		public function getSkills() {
			return $this->skills;
		}
	}
?>