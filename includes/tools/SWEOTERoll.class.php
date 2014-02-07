<?
	class SWEOTERoll extends Roll {
		private $rolls = array();
		private $dice = array();
		private $shortMap = array('a' => 'ability', 'p' => 'proficiency', 'b' => 'boost', 'd' => 'difficulty', 'c' => 'challenge', 's' => 'setback', 'f' => 'force');
		private $total = array('success' => 0, 'advantage' => 0, 'triumph' => 0, 'failure' => 0, 'threat' => 0, 'dispair' => 0, 'whiteDot' => 0, 'blackDot' => 0);

		function __construct() { }

		function newRoll($diceString) {
			preg_match_all('/(\w+)/', $diceString, $rolls, PREG_SET_ORDER);
			foreach ($rolls as $roll) {
				$die = strtolower($roll[0]);
				if (strlen($die) == 1 && array_key_exists($die, $this->shortMap)) $die = $this->shortMap[$die];
				elseif (!in_array($die, $this->shortMap)) continue;

				$this->rolls[] = array('die' => $die, 'results' => array());
				if (!array_key_exists($die, $this->dice)) $this->dice[$die] = new SWEOTEDie($die);
			}
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				$result = $this->dice[$roll['die']]->roll();

				$roll['results'] = $result;

				if (strlen($result)) foreach (explode('_', $result) as $icon) $this->total[$icon]++;
			}
		}

		function forumLoad($rollID) {

		}

		function forumSave($postID) {
			global $mysql;

			$addRoll = $mysql->prepare("INSERT INTO rolls SET postID = $postID, type = 'basic', reason = :reason, roll = :roll, indivRolls = :indivRolls, total = :total, visibility = :visibility, ra = :ra");
			$addRoll->bindValue(':reason', $roll['reason']);
			$addRoll->bindValue(':roll', $roll['roll']);
			$addRoll->bindValue(':ra', $roll['ra']);
			$addRoll->bindValue(':total', $roll['total']);
			$addRoll->bindValue(':indivRolls', $roll['indivRolls']);
			$addRoll->bindValue(':visibility', $roll['visibility']);
			$addRoll->execute();
		}

		function getResults() {
			return $this->total;
		}

		function showHTML($reason = '') {
			if (sizeof($this->rolls)) {
				echo '<div class="roll">';
				$totalString = '';
				foreach ($this->rolls as $count => $roll) {
					echo "<div class=\"sweote_dice {$roll['die']} {$roll['results']}\"><div></div></div>";
				}
				echo '<p>';
				if ($this->total['success']) $totalString .= $this->total['success'].' Success'.($this->total['success'] > 1?'es':'').', ';
				if ($this->total['advantage']) $totalString .= $this->total['advantage'].' Advantage, ';
				if ($this->total['triumph']) $totalString .= $this->total['triumph'].' Triumph, ';
				if ($this->total['failure']) $totalString .= $this->total['failure'].' Failure'.($this->total['failure'] > 1?'s':'').', ';
				if ($this->total['threat']) $totalString .= $this->total['threat'].' Threat, ';
				if ($this->total['dispair']) $totalString .= $this->total['dispair'].' Dispair, ';
				if ($this->total['whiteDot']) $totalString .= $this->total['whiteDot'].' White Force Point'.($this->total['whiteDot'] > 1?'s':'').', ';
				if ($this->total['blackDot']) $totalString .= $this->total['blackDot'].' Black Force Point'.($this->total['blackDot'] > 1?'s':'').', ';
				echo substr($totalString, 0, -2);
				echo '</p>';
				echo '</div>';
			}
		}
	}
?>