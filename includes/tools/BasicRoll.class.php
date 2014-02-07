<?
	class BasicRoll extends Roll {
		private $rolls = array();
		private $dice = array();
		private $rerollAces = FALSE;

		function __construct() { }

		function newRoll($diceString, $options = array()) {
			if (isset($options['rerollAces']) && $options['rerollAces']) $this->rerollAces = TRUE;
			preg_match_all('/(\d*)d(\d+)([+-]\d+)?/', $diceString, $rolls, PREG_SET_ORDER);
			foreach ($rolls as $roll) {
				if ($roll[1] == '') {
					$roll[1] = 1;
					$roll[0] = '1'.$roll[0];
				}
				if (!isset($roll[3])) $roll[3] = 0;
				else $roll[3] = intval($roll[3]);

				$this->rolls[] = array('string' => $roll[0], 'number' => $roll[1], 'sides' => $roll[2], 'modifier' => $roll[3], 'results' => array(), 'total' => 0);
				$this->dice[$roll[2]] = new BasicDie($roll[2]);
			}
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				for ($count = 0; $count < $roll['number']; $count++) {
					$result = $this->dice[$roll['sides']]->roll();

					if (isset($roll['results'][$count]) && is_array($roll['results'][$count])) $roll['results'][$count][] = $result;
					elseif ($result == $roll['sides'] && $this->rerollAces) $roll['results'][$count] = array($result);
					else $roll['results'][$count] = $result;
					$roll['total'] += $result;

					if ($this->rerollAces && $result == $roll['sides']) $count -= 1;
				}
				$roll['total'] += $roll['modifier'];
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
		}

		function showHTML($reason = '') {
			if (sizeof($this->rolls)) {
				echo '<div class="roll">';
				$rollStrings = $rollValues = array();
				$multipleRolls = sizeof($this->rolls) > 1?TRUE:FALSE;
				foreach ($this->rolls as $count => $roll) {
					$rollStrings[] = $roll['string'];
					$rollValues[$count] = '<p class="rollResults">'.($multipleRolls?"{$roll['string']} - ":'').'( ';
					$results = array();
					foreach ($roll['results'] as $key => $result) {
						if (is_array($result))  {
							$results[$key] = '[ '.implode(', ', $result).' ]';
						}
						else $results[$key] = $result;
					}
					$rollValues[$count] .= implode(', ', $results).' )';
					if ($roll['modifier'] < 0) $rollValues[$count] .= ' - '.abs($roll['modifier']);
					elseif ($roll['modifier'] > 0) $rollValues[$count] .= ' + '.$roll['modifier'];
					$rollValues[$count] .= ' = '.$roll['total'].'</p>';
				}
				echo '<p class="rollString">'.($reason != ''?"{$reason} - ":'').implode(', ', $rollStrings).'</p>';
				echo implode('', $rollValues);
				echo '</div>';
			}
		}
	}
?>