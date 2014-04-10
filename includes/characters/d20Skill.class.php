<?
	class d20Skill {
		protected $skillID;
		protected $name;
		protected $stat;
		protected $ranks;
		protected $misc;
		
		public function __construct($skillID = NULL) {
			$this->skillID = intval($skillID)?intval($skillID):NULL;
		}
		
		function getTotal() {
		}
	}
?>