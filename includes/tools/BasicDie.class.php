<?
	class BasicDie extends BaseDie {
		function __construct($sides) {
			parent::__construct($sides);
			if ($this->sides < 0){
				$this->sides=0;
			}
			if ($this->sides > 1000){
				$this->sides=1000;
			}
		}

		function __toString() {
			return $result;
		}

		function roll() {
			if($this->sides>=1){
				$this->result = mt_rand(1, $this->sides);
			}else{
				$this->result =0;
			}


			return $this->result;
		}
	}
?>