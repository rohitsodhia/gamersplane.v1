<?
	class action {
		var $name;
		var $level = 0;
		var $cost = 0;
		var $offset = 0;
		var $details;
		var $specialties = array();
		var $advantages = array();
		var $disadvantages = array();
		
		function __construct($actionID, $name, $level) {
			$this->name = $name;
			$this->level = $level;
		}
		
		function setLevel($level) {
			$this->level = $level;
		}
		
		function getLevel() {
			return $this->level;
		}
		
		function setCost($cost) {
			$this->cost = $cost;
		}
		
		function getCost() {
			return $this->cost;
		}
		
		function setOffset($offset = 0) {
			$this->offset = $offset;
		}
		
		function getOffset() {
			return $this->offset;
		}
		
		function setDetails($details) {
			$this->details = $details;
		}
		
		function getDetails() {
			return $this->details;
		}
		
		function setSpecialties($specialties) {
			$this->specialties = $specialties;
		}
		
//		function addSpecialties($specialties) {
//			if (is_array($specialties)) { $this->specialties = array_merge($this->specialties, $specialties); }
//			else { array_push($this->specialties, $specialties); }
//		}
		
		function getSpecialty($specialty = -1) {
			if ($specialty == -1) { return $this->specialties; }
			elseif (isset($this->specialties[$specialty])) { return $this->specialties[$specialty]; }
			else { return FALSE; }
		}
	}
?>