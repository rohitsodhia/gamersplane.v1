<?
	$marvel_cost = array(0 => 0, .33, .66, 1, 2, 3, 4, 6, 9, 12, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95);
	$marvel_wealthMod = array (-1 => -1, 0, (1/3), (2/3), 1, 2, 3, 4, 6, 9, 12, 15);
	
	function redStones($stones) {
		if ($stones - intval($stones) == 0) return 0;
		else {
			$redStones = intval(($stones - intval($stones)) * 10 / 3);
			if ($redStones == 3) $redStones = 0;
		}
		
		return $redStones;
	}
	
	function whiteStones($stones) {
		if (redStones($stones) == 0) { return intval($stones); }
		elseif ($stones > 0) { return intval($stones); }
		else { return '-'.intval(abs($stones)); }
	}
	
	function formatStones($stones) {
		if ($stones - intval($stones) == 0) { return intval($stones); }
		else {
			$decimal = substr($stones, strpos($stones, '.') + 1, 1);
			if ($decimal == 3) { return floatval(substr($stones, 0, strpos($stones, '.')).'.33'); }
			elseif ($decimal == 6) { return floatval(substr($stones, 0, strpos($stones, '.')).'.66'); }
			elseif ($decimal > 7) { return round($stones); }
			else { return substr($stones, 0, strpos($stones, '.')); }
//			else { return floatval(substr($stones, 0, strpos($stones, '.') + 3)); }
		}
	}

	function actionFormFormat($actionInfo = array(), $count = 1) {
		if (!is_array($actionInfo) || sizeof($actionInfo) == 0) $actionInfo = array();
		if (!is_numeric($count)) $count = 1;
		$defaults = array('cost' => 0, 'level' => 0);
		foreach ($defaults as $key => $value) if (!isset($actionInfo[$key])) $actionInfo[$key] = $value;
?>
					<div class="actionWrapper"><div class="action">
						<div class="tr labelTR clearfix">
							<label class="level">Level</label>
							<label class="cost">Cost</label>
						</div>
						<div class="clearfix">
							<span class="actionName"><?=$actionInfo['name']?></span>
							<input type="text" name="action[<?=$actionInfo['actionID']?>][level]" value="<?=$actionInfo['level']?>" class="level">
							<input type="text" name="action[<?=$actionInfo['actionID']?>][cost]" value="<?=$actionInfo['cost']?>" class="cost">
						</div>
						<textarea name="action[<?=$actionInfo['actionID']?>][details]"><?=$actionInfo['details']?></textarea>
						<div class="removeDiv alignRight"><a href="" class="remove">[ Remove ]</a></div>
					</div></div>
<?
	}

	function modifierFormFormat($modifierInfo = array(), $count = 1) {
		if (!is_array($modifierInfo) || sizeof($modifierInfo) == 0) $modifierInfo = array();
		if (!is_numeric($count)) $count = 1;
		$defaults = array('cost' => 0, 'level' => 0);
		foreach ($defaults as $key => $value) if (!isset($modifierInfo[$key])) $modifierInfo[$key] = $value;
?>
					<div class="modifierWrapper"><div class="modifier">
						<div class="tr labelTR clearfix">
							<label class="level">Level</label>
							<label class="cost">Cost</label>
						</div>
						<div class="clearfix">
							<span class="modifierName"><?=$modifierInfo['name']?></span>
							<input type="text" name="modifier[<?=$modifierInfo['modifierID']?>][level]" value="<?=$modifierInfo['level']?>" class="level">
							<input type="text" name="modifier[<?=$modifierInfo['modifierID']?>][cost]" value="<?=$modifierInfo['cost']?>" class="cost">
						</div>
						<textarea name="modifier[<?=$modifierInfo['modifierID']?>][details]"><?=$modifierInfo['details']?></textarea>
						<div class="removeDiv alignRight"><a href="" class="remove">[ Remove ]</a></div>
					</div></div>
<?
	}

	function challengeFormFormat($challengeInfo = array()) {
		if (!is_array($challengeInfo) || sizeof($challengeInfo) == 0) $challengeInfo = array();
?>
					<div id="challenge_<?=$challengeInfo['challengeID']?>" class="tr challenge clearfix">
						<span class="challengeName"><?=$challengeInfo['challenge']?></span>
						<input type="text" name="challenge[<?=$challengeInfo['challengeID']?>]" value="<?=$challengeInfo['stones']?>" class="challengeStones">
						<a href="" class="remove">[ Remove ]</a>
					</div>
<?
	}
?>