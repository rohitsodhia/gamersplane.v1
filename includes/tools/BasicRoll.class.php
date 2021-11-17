<?
	class BasicRoll extends Roll {
		protected $rerollAces = false;

		function __construct() { }

		function newRoll($diceString, $options = array()) {
			if (isset($options['rerollAces']) && $options['rerollAces']) $this->rerollAces = true;
			$this->parseRolls($diceString);
		}

		function parseRolls($diceString) {
			$diceString=str_replace(" ", "", $diceString);  //remove spaces, easier here than in the regex

			$hasDiceParts=false;

			$diceParts=explode(",",$diceString);

			foreach ($diceParts as $dicePart){
				preg_match_all('/(([\+\-]?)(\d*)([dD])?(\d+))/', $dicePart, $rolls, PREG_SET_ORDER);

				$totalModifier=0;
				$diceSides=$diceNumber=array();

				if(count($rolls)){
					foreach ($rolls as $roll){
						if(($roll[4]=="d"||$roll[4]=="D")){
							$diceNumber[]=intval($roll[3]?$roll[3]:1);
							$diceSize=intval($roll[5]);
							$diceSides[]=$diceSize;
							$this->dice[$diceSize]=new BasicDie($diceSize);
						}
						else{
							$mod=intval($roll[1]);
							if($mod){
								$totalModifier=$totalModifier+$mod;
							}
						}
					}
					$this->rolls[] = array('string' => $dicePart, 'number' => $diceNumber, 'sides' => $diceSides, 'modifier' => $totalModifier, 'indivRolls' => array(), 'result' => 0);
					$hasDiceParts = true;
				}

			}

			return $hasDiceParts;
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				for ($handful=0;$handful<count($roll['number']);$handful++){
					for ($count = 0; $count < $roll['number'][$handful]; $count++) {
						$result = $this->dice[$roll['sides'][$handful]]->roll();

						if (isset($roll['indivRolls'][$handful][$count]) && is_array($roll['indivRolls'][$handful][$count])) $roll['indivRolls'][$handful][$count][] = $result;
						elseif ($result == $roll['sides'][$handful] && $this->rerollAces) $roll['indivRolls'][$handful][$count] = array($result);
						else $roll['indivRolls'][$handful][$count] = $result;
						$roll['result'] += $result;

						if ($this->rerollAces && $result == $roll['sides'][$handful] && $result>1) $count -= 1;
					}
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

			if (sizeof($this->rolls) == 0) return false;

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

		function resultsToText($rolls){
			$ret='';
			if(is_array($rolls)){
				foreach($rolls as $index=>$roll){
					if(is_array($roll)){
						$rollCount = count($roll);
						if($rollCount>0){
							$rollTotal=0;
							$raHtml='';
							foreach($roll as $rerolledAce){
								if(--$rollCount>0){
									$raHtml.='<s>'.$rerolledAce.'</s>';
								}
								else{
									$raHtml.=$rerolledAce;
								}

								$rollTotal+=$rerolledAce;
							}
							$ret.='<i data-ro="'.$index.'" data-rv="'.$rollTotal.'">';
							$ret.=$raHtml;
							$ret.='</i>';
						}
					} else {
						$ret.='<i data-ro="'.$index.'" data-rv="'.$roll.'">'.$roll.'</i>';
					}
				}
			} else {
				$ret.='<i data-ro="0" data-rv="'.$rolls.'">'.$rolls.'</i>';
			}
			return $ret;
		}

		function showHTML($showAll = false) {
			if (sizeof($this->rolls)) {
				$hidden = false;

				echo '<div class="roll">';
				$rollStrings = $rollValues = array();
				$multipleRolls = sizeof($this->rolls) > 1?true:false;
				foreach ($this->rolls as $count => $roll) {
					$rollStrings[] = $roll['string'];
					$rollValues[$count] = '<p class="rollResults"">'.($this->visibility != 0 && $showAll?'<span class="hidden">':'').($multipleRolls?"{$roll['string']} : ":'');
					$results = array();

					if(is_array($roll['indivRolls']) && count($roll['indivRolls']) && is_array($roll['indivRolls'][0])){
						//new multidice
						foreach ($roll['indivRolls'] as $key => $result) {
							$results[$key]='(<span class="rollValues parsedRolls" data-rollstring="'.$roll['number'][$key].'d'.$roll['sides'][$key].'">'.$this->resultsToText($result).'</span>)';
						}
					}else{
						//old data
						$results[0] = '(<span class="rollValues parsedRolls" data-rollstring="'.$roll['number'][0].'d'.$roll['sides'][0].'">'.$this->resultsToText($roll['indivRolls']).'</span>)';
					}

					$rollValues[$count] .= implode(' + ', $results);
					$rollValues[$count] .= ($roll['modifier'] == 0 ? "" : ($roll['modifier'] < 0 ? " - " : " + ").abs($roll['modifier']));
					$rollValues[$count] .= ' = '.$roll['result'].($this->visibility != 0?'</span>':'').'</p>';
				}
				echo '<p class="rollString">';
				echo ($showAll && $this->visibility > 0)?'<span class="hidden">'.$this->visText[$this->visibility].'</span> ':'';
				if ($this->visibility <= 2) echo $this->reason;
				elseif ($showAll) { echo '<span class="hidden">'.($this->reason != ''?"{$this->reason}":''); $hidden = true; }
				else echo 'Secret Roll';
				if ($this->visibility > 1 && $this->visibility <4 && $showAll && !$hidden) {
					echo '<span class="hidden">';
					$hidden = true;
				}
				if ($this->visibility <= 1 || $this->visibility == 4 || $showAll) {
					if (strlen($this->reason)) echo ' - (';
					echo implode(', ', $rollStrings);
					if ($this->rerollAces) echo (strlen($this->reason)?', ':'').(strlen($this->reason) == 0?' [ ':'').'RA'.(strlen($this->reason) == 0?' ]':'');
					if (strlen($this->reason)) echo ')';
				}
				echo $hidden?'</span>':'';
				echo '</p>';
				if ($this->visibility == 0 || $this->visibility == 4 || $showAll) echo implode('', $rollValues);
				echo '</div>';
			}
		}
	}
?>