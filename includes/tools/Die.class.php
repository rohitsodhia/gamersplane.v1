<?
	abstract class BaseDie {
		var $sides;
		var $result;
		
		function __construct($sides) {
			$this->sides = $sides;
			$this->result = 0;
		}

		function __toString() {
			return $result;
		}
		
		abstract function roll();
	}
?>