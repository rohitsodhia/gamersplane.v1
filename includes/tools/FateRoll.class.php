<?
	class FateRoll extends Roll {
		protected $die;
		private $sMap = array(-1 => 'negative', 'blank', 'positive');

		function __construct() {
			$this->die = new BasicDie(3);
		}

		function newRoll($numDice) {
			$numDice = intval($numDice);
			if ($numDice <= 0) return false;
			else $this->roll = $numDice;
		}

		function roll() {
			for ($count = 0; $count < $this->roll; $count++) $this->rolls[] = $this->die->roll() - 2;
		}

		function forumLoad($rollData) {
			$this->rollID = $rollData['rollID'];
			$this->reason = $rollData['reason'];
			$this->newRoll($rollData['roll']);
			$rollData['indivRolls'] = unserialize($rollData['indivRolls']);
			foreach ($rollData['indivRolls'] as $key => $roll) {
				$this->rolls[$key] = $roll;
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

			$addRoll = $mysql->prepare("INSERT INTO rolls SET postID = $postID, type = 'fate', reason = :reason, roll = :roll, indivRolls = :indivRolls, visibility = :visibility");
			$addRoll->bindValue(':reason', $this->reason);
			$addRoll->bindValue(':roll', $this->roll);
			$addRoll->bindValue(':indivRolls', serialize($this->rolls));
			$addRoll->bindValue(':visibility', $this->visibility);
			$addRoll->execute();
		}

		function getResults() {
		}

		function showHTML($showAll = false) {
			if (sizeof($this->rolls)) {
				$hidden = false;
				echo '<div class="roll">';
				$totals = array(-1 => 0, 0, 0);
				$sum = 0;
				echo '<p class="rollString">';
				echo ($showAll && $this->visibility > 0)?'<span class="hidden">'.$this->visText[$this->visibility].'</span> ':'';
				if ($this->visibility <= 2) echo $this->reason;
				elseif ($showAll) { echo '<span class="hidden">'.($this->reason != ''?"{$this->reason}":''); $hidden = true; }
				else echo 'Secret Roll';
				echo $hidden?'</span>':'';
				echo '</p>';
				if ($this->visibility <= 1 || $showAll) {
					echo '<div class="rollResults">';
					foreach ($this->rolls as $count => $roll) {
						echo "<div class=\"fate_dice {$this->sMap[$roll]}\">";
						if ($this->visibility == 0 || $showAll) echo '<div></div>';
						echo '</div>';
						$totals[$roll]++;
						$sum += $roll;
					}
					echo '</div>';
				}
				if ($this->visibility == 0 || $showAll) {
					echo '<p>';
					if ($this->visibility != 0) echo '<span class="hidden">';
					echo "{$totals[1]} Positive, {$totals[0]} Blank, {$totals[-1]} Negative - Total: ".showSign($sum);
					if ($this->visibility != 0) echo '</span>';
					echo '</p>';
				}
				echo '</div>';
			}
		}
	}
?>