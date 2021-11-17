<?
	class BasicDie extends BaseDie {
		function __construct($sides) {
			parent::__construct($sides);
			if ($this->sides < 1){
				$this->sides=1;
			}
			if ($this->sides > 1000){
				$this->sides=1000;
			}
		}

		function __toString() {
			return $result;
		}

		function roll() {
			$this->result = mt_rand(1, $this->sides);

			return $this->result;
		}
	}
?>