<?
	class Roller {
		private $rolls = array();
		private $currentRoll;
		private $diceDisplay;

		function newRoll($type, $diceString, $options = array()) {
			$this->diceDisplay = new DiceDisplay();
			if (class_exists($type.'Roll')) $classname = $type.'Roll';
			else throw new Exception('Invalid type');
			$this->rolls[] = new $classname($diceString, $options);
			$this->currentRoll = &$this->rolls[sizeof($this->rolls) - 1];
		}

		function roll() {
			$this->currentRoll->roll();
		}

		function getResults() {
			return $this->currentRoll->getResults();
		}

		function showHTML() {
			$this->diceDisplay->addRoll($this->currentRoll->getData());
			$this->diceDisplay->showHTML();
		}
	}
?>