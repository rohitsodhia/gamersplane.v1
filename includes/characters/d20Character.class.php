<?
	abstract class d20Character {
		protected $userID ;
		protected $charID;
		protected $name;
		protected $classes = '';
		protected $stats = array('str' => 0, 'dex' => 0, 'con' => 0, 'int' => 0, 'wis' => 0, 'cha' => 0);
		protected $ac = array();
		protected $damage = array();
		protected $saves = array ('fort' => array('base' => 0, 'misc' => 0),
								  'ref' => array('base' => 0, 'misc' => 0),
								  'will' => array('base' => 0, 'misc' => 0));
		protected $initiative = array();
		protected $attackBonus = array();
		protected $skills = array();
		protected $items = array();
		protected $experience = 0;
		
		public function __construct($userID = NULL) {
			if ($userID == NULL) $this->userID = intval($_SESSION['userID']);
			$this->userID = $userID;
		}
		
		public function setCharID($charID) {
			$this->charID = $charID;
			
			return TRUE;
		}
		
		public function setName($name) {
			$this->name = $name;
			
			return TRUE;
		}
		
		public function setStats() { }
		
		public function getStats($stat = '') { }
	}
?>