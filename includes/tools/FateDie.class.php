<?
	class FateDie extends BaseDie {
		public static $sides = array(0 => 'Blank', 1 => 'Positive', 2 => 'Negative');

		public function __construct() { }

		public function __toString() {
			return $this->result;
		}

		public function roll() {
			$this->result = FateDie::$sides[mt_rand(0, 2)];

			return $this->result;
		}
	}
?>