<?
	$marvel_cost = array(0 => 0, .33, .66, 1, 2, 3, 4, 6, 9, 12, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95);
	$marvel_wealthMod = array (-1 => -1, 0, (1/3), (2/3), 1, 2, 3, 4, 6, 9, 12, 15);
	
	function redStones($stones) {
		if ($stones - intval($stones) == 0) return 0;
		else {
			$redStones = intval(($charInfo['unusedStones'] - intval($charInfo['unusedStones'])) * 10 / 3);
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
?>