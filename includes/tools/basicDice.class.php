<?
	class BasicDice extends Dice {
		function __construct($sides) {
			parent::__construct($sides);
			if ($this->sides < 2) throw new Exception('Less than 2 sides');
			if ($this->sides > 1000) throw new Exception('More than 1000 sides');
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