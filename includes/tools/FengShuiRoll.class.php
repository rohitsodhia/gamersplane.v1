<?
	class FengShuiRoll extends Roll {
		protected $type;
		protected $die;

		function __construct() {
			$this->die = new BasicDie(6);
		}

		function newRoll($actionValue, $options = array()) {
			$type = $options[0];
			$actionValue = intval($actionValue);
			$this->roll = $actionValue > 0?$actionValue:0;
			$this->type = $type;
		}

		function roll() {
			$this->rolls = array('p' => array(), 'n' => array(), 'e' => null);
			if ($this->type == 'standard' || $this->type == 'fortune') {
				do {
					$roll = $this->die->roll();
					$this->rolls['p'][] = $roll;
				} while ($roll == 6);
				do {
					$roll = $this->die->roll();
					$this->rolls['n'][] = $roll;
				} while ($roll == 6);
				if ($this->type == 'fortune')
					$this->rolls['e'] = $this->die->roll();
			} elseif ($this->type == 'closed') {
				$this->rolls['p'][] = $this->die->roll();
				$this->rolls['n'][] = $this->die->roll();
			}
		}

		function forumLoad($rollData) {
			$this->rollID = $rollData['rollID'];
			$this->reason = $rollData['reason'];
			$this->newRoll($rollData['roll']);
			$this->rolls = unserialize($rollData['indivRolls']);
			$this->setVisibility($rollData['visibility']);
		}

		function forumSave($postID) {
			global $mysql;

			$addRoll = $mysql->prepare("INSERT INTO rolls SET postID = $postID, type = 'fengshui', reason = :reason, roll = :roll, indivRolls = :indivRolls, visibility = :visibility, extras = :extras");
			$addRoll->bindValue(':reason', $this->reason);
			$addRoll->bindValue(':roll', $this->roll);
			$addRoll->bindValue(':indivRolls', serialize($this->rolls));
			$addRoll->bindValue(':visibility', $this->visibility);
			$addRoll->bindValue(':extras', $this->type);
			$addRoll->execute();
		}

		function getResults() {
		}

		function showHTML($showAll = false) {
			if (sizeof($this->rolls)) {
				$sum = 0;
				$hidden = false;
				echo '<div class="roll">';
				echo '<p class="rollString">';
				echo ($showAll && $this->visibility > 0)?'<span class="hidden">'.$this->visText[$this->visibility].'</span> ':'';
				if ($this->visibility <= 2) echo $this->reason;
				elseif ($showAll) { echo '<span class="hidden">'.($this->reason != ''?"{$this->reason}":''); $hidden = true; }
				else echo 'Secret Roll';
				echo $hidden?'</span>':'';
				echo '</p>';
				if ($this->visibility <= 1 || $this->visibility == 4 || $showAll) {
					echo '<div class="rollResults">';
					echo $this->roll;
					if ($this->type != 'closed') {
						echo ' + [ '.implode(', ', $this->rolls['p']).' ]';
						echo ' - [ '.implode(', ', $this->rolls['n']).' ]';
						if ($this->rolls['e'])
							echo ' + '.$this->rolls['e'];
					} else {
						echo ' + '.$this->rolls['p'][0];
						echo ' - '.$this->rolls['n'][0];
					}
					$sum = $this->roll + array_sum($this->rolls['p']) - array_sum($this->rolls['n']);
					if ($this->rolls['e'])
						$sum += $this->rolls['e'];
					echo ' = '.$sum;
					echo '</div>';
				}
				echo '</div>';
			}
		}
	}
?>