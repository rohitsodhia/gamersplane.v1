<?
	class action {
		var $name;
		var $stat;
		var $ranks;
		var $misc;
		
		function __construct($name, $stat, $ranks, $misc = 0) {
			$this->name = $name;
			$this->stat = $stat;
			$this->ranks = $ranks;
			$this->misc = $misc;
		}
		
		function getTotal() {
			$action = $this->name;
			$action .= "~,~".$this->level;
			$action .= "~,~".$this->offset;
			$action .= "~,~".addslashes($options);
			$action .= "~,~".addslashes(storeArray($this->specialties, "+-+"));
			
			return $action;
		}
	}
?>