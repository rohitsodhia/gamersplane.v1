<?
	class BasicRoll extends Roll {
		protected $rerollAces = FALSE;

		function __construct() { }

		function newRoll($diceString, $options = array()) {
			$cleanDiceStrings = array();
			if (isset($options['rerollAces']) && $options['rerollAces']) $this->rerollAces = TRUE;
			$this->parseRolls($diceString);
		}

		function parseRolls($diceString) {
			preg_match_all('/(\d*)d(\d+)([+-]\d+)?/', $diceString, $rolls, PREG_SET_ORDER);
			if (sizeof($rolls)) {
				foreach ($rolls as $roll) {
					if ($roll[1] == '') {
						$roll[1] = 1;
						$roll[0] = '1'.$roll[0];
					}
					if (!isset($roll[3])) $roll[3] = 0;
					else $roll[3] = intval($roll[3]);

					$this->rolls[] = array('string' => $roll[0], 'number' => $roll[1], 'sides' => $roll[2], 'modifier' => $roll[3], 'indivRolls' => array(), 'result' => 0);
					$this->dice[$roll[2]] = new BasicDie($roll[2]);
				}

				return TRUE;
			} else return FALSE;
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				for ($count = 0; $count < $roll['number']; $count++) {
					$result = $this->dice[$roll['sides']]->roll();

					if (isset($roll['indivRolls'][$count]) && is_array($roll['indivRolls'][$count])) $roll['indivRolls'][$count][] = $result;
					elseif ($result == $roll['sides'] && $this->rerollAces) $roll['indivRolls'][$count] = array($result);
					else $roll['indivRolls'][$count] = $result;
					$roll['result'] += $result;

					if ($this->rerollAces && $result == $roll['sides']) $count -= 1;
				}
				$roll['result'] += $roll['modifier'];
			}
		}

		function forumLoad($rollData) {
			$this->rollID = $rollData['rollID'];
			$this->reason = $rollData['reason'];
			$this->parseRolls($rollData['roll']);
			$rollData['indivRolls'] = unserialize($rollData['indivRolls']);
			$rollData['results'] = unserialize($rollData['results']);
			foreach ($rollData['indivRolls'] as $key => $roll) {
				$this->rolls[$key]['indivRolls'] = $roll;
				$this->rolls[$key]['result'] = $rollData['results'][$key];
			}
			$this->setVisibility($rollData['visibility']);
			$rollData['extras'] = unserialize($rollData['extras']);
			$this->rerollAces = $rollData['extras']['ra'];
		}

		function forumSave($postID) {
			global $mysql;

			if (sizeof($this->rolls) == 0) return FALSE;

			$rolls = $results = $indivRolls = array();
			foreach ($this->rolls as $roll) {
				$rolls[] = $roll['string'];
				$results[] = $roll['result'];
				$indivRolls[] = $roll['indivRolls'];
			}
			$addRoll = $mysql->prepare("INSERT INTO rolls SET postID = $postID, type = 'basic', reason = :reason, roll = :roll, indivRolls = :indivRolls, results = :results, visibility = :visibility, extras = :extras");
			$addRoll->bindValue(':reason', $this->reason);
			$addRoll->bindValue(':roll', implode(',', $rolls));
			$addRoll->bindValue(':indivRolls', serialize($indivRolls));
			$addRoll->bindValue(':results', serialize($results));
			$addRoll->bindValue(':visibility', $this->visibility);
			$addRoll->bindValue(':extras', serialize(array('ra' => $this->rerollAces)));
			$addRoll->execute();
		}

		function getResults() {
		}

		function showHTML($showAll = FALSE) {
			if (sizeof($this->rolls)) {
				$hidden = FALSE;

				echo '<div class="roll">';
				$rollStrings = $rollValues = array();
				$multipleRolls = sizeof($this->rolls) > 1?TRUE:FALSE;
				foreach ($this->rolls as $count => $roll) {
					$rollStrings[] = $roll['string'];
					$rollValues[$count] = '<p class="rollResults">'.($this->visibility != 0 && $showAll?'<span class="hidden">':'').($multipleRolls?"{$roll['string']} - ":'').'( ';
					$results = array();
					foreach ($roll['indivRolls'] as $key => $result) {
						if (is_array($result))  {
							$results[$key] = '[ '.implode(', ', $result).' ]';
						}
						else $results[$key] = $result;
					}
					$rollValues[$count] .= implode(', ', $results).' )';
					if ($roll['modifier'] < 0) $rollValues[$count] .= ' - '.abs($roll['modifier']);
					elseif ($roll['modifier'] > 0) $rollValues[$count] .= ' + '.$roll['modifier'];
					$rollValues[$count] .= ' = '.$roll['result'].($this->visibility != 0?'</span>':'').'</p>';
				}
				echo '<p class="rollString">';
				echo ($showAll && $this->visibility > 0)?'<span class="hidden">'.$this->visText[$this->visibility].'</span> ':'';
				if ($this->visibility <= 2) echo $this->reason;
				elseif ($showAll) { echo '<span class="hidden">'.($this->reason != ''?"{$this->reason}":''); $hidden = TRUE; }
				else echo 'Secret Roll';
				if ($this->visibility > 1 && $showAll && !$hidden) {
					echo '<span class="hidden">';
					$hidden = TRUE;
				}
				if ($this->visibility <= 1 || $showAll) {
					if (strlen($this->reason)) echo ' - (';
					echo implode(', ', $rollStrings);
					if ($this->rerollAces) echo (strlen($this->reason)?', ':'').(strlen($this->reason) == 0?' [ ':'').'RA'.(strlen($this->reason) == 0?' ]':'');
					if (strlen($this->reason)) echo ')';
				}
				echo $hidden?'</span>':'';
				echo '</p>';
				if ($this->visibility == 0 || $showAll) echo implode('', $rollValues);
				echo '</div>';
			}
		}
	}
?>