<?
	class action {
		var $name;
		var $level = 0;
		var $offset = 0;
		var $options;
		var $specialties = array();
		var $advantages = array();
		var $disadvantages = array();
		
		function __construct($name, $level, $offset, $options, $specialties) {
			$this->name = $name;
			$this->level = $level;
			$this->offset = $offset;
			$this->options = $options;
			$this->specialties = $specialties;
		}
		
		function storeAction() {
			$action = $this->name;
			$action .= "~,~".$this->level;
			$action .= "~,~".$this->offset;
			$action .= "~,~".addslashes($options);
			$action .= "~,~".addslashes(storeArray($this->specialties, "+-+"));
			
			return $action;
		}
	}
?>