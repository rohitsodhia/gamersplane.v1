<?
	class modifier {
		var $name;
		var $cost = 0;
		var $costTo;
		var $level = 0;
		var $offset = 0;
		var $optionStones = 0;
		var $timesTaken = 0;
		var $details;
		
		function __construct($name, $cost = 0, $costTo = '') {
			$this->name = $name;
			$this->cost = $cost;
			$this->costTo = $costTo;
		}
		
		function setLevel($level) {
			$this->level = $level;
		}
		
		function getLevel() {
			return $this->level;
		}
		
		function setOffset($offset) {
			$this->offset = $offset;
		}
		
		function getOffset() {
			return $this->offset;
		}
		
		function setOptionStones($optionStones) {
			$this->optionStones = $optionStones;
		}
		
		function getOptionStones() {
			return $this->optionStones;
		}
		
		function getCostTo() {
			return $this->level;
		}
		
		function changeTimesTaken($change) {
			$this->timesTaken += $change;
		}
		
		function getTimesTaken() {
			return $this->timesTaken;
		}
		
		function setDetails($details) {
			$this->details = $details;
		}
		
		function getDetails() {
			return $this->details;
		}
		
		function storeModifier() {
		}
	}
?>