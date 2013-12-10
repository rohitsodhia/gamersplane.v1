<?
	class basicDice {
		var $sides;
		var $rerollAces = FALSE;
		var $total;
		var $roles = array();
		
		function __construct($sides, $rerollAces = FALSE) {
			$this->sides = $sides;
			$this->rerollAces = $rerollAces;
		}
		
		function roll() {
			
		}
	}
?>