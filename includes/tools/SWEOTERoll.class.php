<?
	class SWEOTERoll extends Roll {
		private $d_shortMap = array('a' => 'ability', 'p' => 'proficiency', 'b' => 'boost', 'd' => 'difficulty', 'c' => 'challenge', 's' => 'setback', 'f' => 'force');
		private $totals = array('success' => 0, 'advantage' => 0, 'triumph' => 0, 'failure' => 0, 'threat' => 0, 'dispair' => 0, 'whiteDot' => 0, 'blackDot' => 0);
		private $resultsMap = array('', 'success', 'advantage', 'success_success', 'success_advantage', 'advantage_advantage', 'triumph', 'failure', 'threat', 'failure_failure', 'failure_threat', 'threat_threat', 'dispair', 'whiteDot', 'whiteDot_whiteDot', 'blackDot', 'blackDot_blackDot');

		function __construct() { }

		function newRoll($diceString) {
			preg_match_all('/(\w+)/', $diceString, $rolls, PREG_SET_ORDER);
			if (sizeof($rolls)) { foreach ($rolls as $roll) {
				$die = strtolower($roll[0]);
				if (strlen($die) == 1 && array_key_exists($die, $this->d_shortMap)) $die = $this->d_shortMap[$die];
				elseif (!in_array($die, $this->d_shortMap)) continue;

				$this->rolls[] = array('die' => $die, 'result' => NULL);
				if (!array_key_exists($die, $this->dice)) $this->dice[$die] = new SWEOTEDie($die);
			} }
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				$result = $this->dice[$roll['die']]->roll();

				$roll['result'] = $result;

				if (strlen($result)) foreach (explode('_', $result) as $icon) $this->totals[$icon]++;
			}
		}

		function forumLoad($rollData) {
			$this->rollID = $rollData['rollID'];
			$this->reason = $rollData['reason'];
			$this->newRoll($rollData['roll']);
			$rollData['indivRolls'] = unserialize($rollData['indivRolls']);
			foreach ($rollData['indivRolls'] as $key => $roll) {
				$this->rolls[$key]['result'] = $this->resultsMap[$roll];
			}
			$rollData['results'] = unserialize($rollData['results']);
			$count = 0;
			foreach ($this->totals as $symbol => $total) {
				$this->totals[$symbol] = $rollData['results'][$count++];
			}
			$this->setVisibility($rollData['visibility']);
		}

		function forumSave($postID) {
			global $mysql;

			$rolls = $indivRolls = $totals = array();
			foreach ($this->rolls as $roll) {
				$rolls[] = $roll['die'];
				$indivRolls[] = array_search($roll['result'], $this->resultsMap);
			}
			$count = 0;
			foreach ($this->totals as $total) {
				$totals[$count++] = $total;
			}

			$addRoll = $mysql->prepare("INSERT INTO rolls SET postID = $postID, type = 'sweote', reason = :reason, roll = :roll, indivRolls = :indivRolls, results = :results, visibility = :visibility");
			$addRoll->bindValue(':reason', $this->reason);
			$addRoll->bindValue(':roll', implode(',', array_map(function ($value) { return $value[0]; }, $rolls)));
			$addRoll->bindValue(':indivRolls', serialize($indivRolls));
			$addRoll->bindValue(':results', serialize($totals));
			$addRoll->bindValue(':visibility', $this->visibility);
			$addRoll->execute();
		}

		function getResults() {
		}

		function showHTML($showAll = FALSE) {
			if (sizeof($this->rolls)) {
				echo '<div class="roll">';
				$totalString = '';
				echo '<p class="rollString">';
				echo ($showAll && $this->visibility > 0)?'<span class="hidden">'.$this->visText[$this->visibility].'</span> ':'';
				if ($this->visibility <= 2) echo $this->reason;
				elseif ($showAll) { echo '<span class="hidden">'.($this->reason != ''?"{$this->reason}":''); $hidden = TRUE; }
				else echo 'Secret Roll';
				echo $hidden?'</span>':'';
				echo '</p>';
				if ($this->visibility <= 1 || $showAll) {
					echo '<div class="rollResults">';
					foreach ($this->rolls as $count => $roll) {
						echo "<div class=\"sweote_dice {$roll['die']} {$roll['result']}\">";
						if ($this->visibility == 0 || $showAll) echo '<div></div>';
						echo '</div>';
					}
					echo '</div>';
				}
				if ($this->visibility == 0 || $showAll) {
					echo '<p'.($this->visibility != 0?' class="hidden"':'').'>';
					if ($this->totals['success']) 
						$totalString .= $this->totals['success'].' Success, ';
					if ($this->totals['advantage']) 
						$totalString .= $this->totals['advantage'].' Advantage, ';
					if ($this->totals['triumph']) 
						$totalString .= $this->totals['triumph'].' Triumph, ';
					if ($this->totals['failure']) 
						$totalString .= $this->totals['failure'].' Failure, ';
					if ($this->totals['threat']) 
						$totalString .= $this->totals['threat'].' Threat, ';
					if ($this->totals['dispair']) 
						$totalString .= $this->totals['dispair'].' Dispair, ';
					if ($this->totals['whiteDot']) 
						$totalString .= $this->totals['whiteDot'].' White Force Point'.($this->totals['whiteDot'] > 1?'s':'').', ';
					if ($this->totals['blackDot']) 
						$totalString .= $this->totals['blackDot'].' Black Force Point'.($this->totals['blackDot'] > 1?'s':'').', ';
					echo substr($totalString, 0, -2);
					echo '</p>';
					echo '<p'.($this->visibility != 0?' class="hidden"':'').'>';
					$totalString = '';
					if ($this->totals['success'] != $this->totals['failure']) 
						$totalString .= abs($this->totals['success'] - $this->totals['failure']).' '.($this->totals['success'] > $this->totals['failure']?'Success':'Failure').', ';
					if ($this->totals['advantage'] != $this->totals['threat']) 
						$totalString .= abs($this->totals['advantage'] - $this->totals['threat']).' '.($this->totals['advantage'] > $this->totals['Threat']?'Advantage':'Threat').', ';
					if ($this->totals['threat']) 
						$totalString .= $this->totals['threat'].' Threat, ';
					if ($this->totals['dispair']) 
						$totalString .= $this->totals['dispair'].' Dispair, ';
					if (strlen($totalString)) 
						echo '<strong>Total:</strong> '.substr($totalString, 0, -2);
					echo '</p>';
				}
				echo '</div>';
			}
		}
	}
?>