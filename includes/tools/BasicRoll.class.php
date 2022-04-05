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
				preg_match_all('/(([\+\-]?)(\d*)([dD])?(\d+)(([hlHL])(\d+))?)/', $dicePart, $rolls, PREG_SET_ORDER);

				$totalModifier=0;
				$diceSides=$diceNumber=$diceModifier=$drop=$dropHigh=array();

				if(count($rolls)){
					foreach ($rolls as $roll){
						if(($roll[4]=="d"||$roll[4]=="D")){
							$dn=intval($roll[3]!=null?$roll[3]:1); //dice number
							$diceNumber[]=$dn;
							$diceModifier[]=$roll[2]?$roll[2]:'+';
							$diceSize=intval($roll[5]);
							$diceSides[]=$diceSize;
							//keep and drop
							if($roll[7] && intval($roll[8])){
								$drop[]= max(0,$dn-intval($roll[8]));  //keep is inverse of drop
								$dropHigh[] = ( ($roll[7]=="h" || $roll[7]=="H") ? 0:1 );
							}else{
								$drop[]=0;
								$dropHigh[] = 0;
							}
							$this->dice[$diceSize]=new BasicDie($diceSize);
						}
						else{
							$mod=intval($roll[1]);
							if($mod){
								$totalModifier=$totalModifier+$mod;
							}
						}
					}
					$this->rolls[] = array('string' => $dicePart, 'number' => $diceNumber, 'sides' => $diceSides, 'drop' => $drop, 'dropHigh' => $dropHigh, 'diceModifier' => $diceModifier, 'modifier' => $totalModifier, 'indivRolls' => array(), 'result' => 0);
					$hasDiceParts = true;
				}

			}

			return $hasDiceParts;
		}

		function roll() {
			foreach ($this->rolls as $key => &$roll) {
				for ($handful=0;$handful<count($roll['number']);$handful++) {
					$diceVals = array();
					for ($count = 0; $count < $roll['number'][$handful]; $count++) {
						$result = $this->dice[$roll['sides'][$handful]]->roll();
						if (isset($roll['indivRolls'][$handful][$count]) && is_array($roll['indivRolls'][$handful][$count])) {
							$roll['indivRolls'][$handful][$count][] = $result; // we rolled an ace with the last die
							$diceVals[array_keys($diceVals)[count($diceVals)-1]] += $result;
						}
						elseif ($result == $roll['sides'][$handful] && $this->rerollAces){
							$roll['indivRolls'][$handful][$count] = array($result);  // start an array for rerolling aces
							$diceVals[] = $result;
						}
						else {
							$roll['indivRolls'][$handful][$count] = $result; // not an ace or not rerolling aces
							$diceVals[] = $result;
						}

						if ($this->rerollAces && $result == $roll['sides'][$handful] && $result>1) $count -= 1;
					}

					if(!$roll['number'][$handful]) {
						$roll['indivRolls'][$handful][0] = 0; // 0d6
						$diceVals[] = 0;
					}

					//we have the individual rolls.  Calculate the result with droping dice
					if($roll['dropHigh'][$handful]){
						arsort($diceVals,SORT_NUMERIC);
					} else {
						asort($diceVals,SORT_NUMERIC);
					}
					$handfulTotal=0;
					$diceModifier=$roll['diceModifier'][$handful]=='-'?-1:1;
					$keeping = 0;
					foreach ( $diceVals as $key => $val ) {
						if(++$keeping>$roll['drop'][$handful]){
							$handfulTotal+=($diceModifier*$val);
						}
					}

					$roll['result'] += $handfulTotal;

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
			if($rollData['indivRolls']){
				foreach ($rollData['indivRolls'] as $key => $roll) {
					$this->rolls[$key]['indivRolls'] = $roll;
					$this->rolls[$key]['result'] = $rollData['results'][$key];
				}
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

		function getDroppedIndices($rolls,$dropDice,$dropHigh) {
			$ret = array();

			if(is_array($rolls) && $dropDice){
				$diceVals = array();
				foreach($rolls as $index=>$roll){
					if(is_array($roll)) {
						$diceVals[]=array_sum($roll); //collapse rerolled aces
					} else {
						$diceVals[]=$roll;
					}
				}

				if($dropHigh){
					arsort($diceVals,SORT_NUMERIC);
				} else {
					asort($diceVals,SORT_NUMERIC);
				}
				$keeping = 0;
				foreach ( $diceVals as $key => $val ) {
					if(++$keeping<=$dropDice){
						$ret[] = $key;
					}
				}
			}

			return $ret;
		}

		function resultsToText($rolls, $droppedIndices){
			$ret='';
			if(is_array($rolls)){
				foreach($rolls as $index=>$roll){
					if(is_array($roll)){
						//rerolled ace
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
							$ret.='<i data-ro="'.$index.'" data-rv="'.$rollTotal.'"'.(in_array($index,$droppedIndices)?' class="rollDrop"':'').'>';
							$ret.=$raHtml;
							$ret.='</i>';
						}
					} else {
						//dice roll
						$ret.='<i data-ro="'.$index.'" data-rv="'.$roll.'"'.(in_array($index,$droppedIndices)?' class="rollDrop"':'').'>'.$roll.'</i>';
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
							$diceModifier=$roll['diceModifier'][$key]=='-'?-1:1;
							$droppedIndices = $this->getDroppedIndices($result, $roll['drop'][$key], $roll['dropHigh'][$key]);
							$diceModifierText=($key || $diceModifier==-1)?($diceModifier==-1?'- ':'+ '):'';

							$results[$key]=$diceModifierText.'(<span class="rollValues parsedRolls" data-rollstring="'.$roll['number'][$key].'d'.$roll['sides'][$key].'">'.$this->resultsToText($result, $droppedIndices).'</span>)';
						}
					}else{
						//old data
						$results[0] = '(<span class="rollValues parsedRolls" data-rollstring="'.$roll['number'][0].'d'.$roll['sides'][0].'">'.$this->resultsToText($roll['indivRolls'], array()).'</span>)';
					}

					$rollValues[$count] .= implode(' ', $results);
					$rollValues[$count] .= ($roll['modifier'] == 0 ? "" : ($roll['modifier'] < 0 ? " - " : " + ").abs($roll['modifier']));
					$rollValues[$count] .= '<span class="rollResultTotal"> = <span class="rollTotal">'.$roll['result'].'</span></span>'.($this->visibility != 0?'</span>':'').'</p>';
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