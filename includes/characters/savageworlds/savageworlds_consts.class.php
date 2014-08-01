<?
	class savageworlds_consts {
		private static $stats = array('agi' => 'Agility', 'sma' => 'Smarts', 'spi' => 'Spirit', 'str' => 'Strength', 'vig' => 'Vigor'); 

		public static function getStats($stat = NULL) {
			if ($stat == NULL) return self::$stats;
			elseif (array_key_exists($stat, self::$stats)) return self::$stats[$stat];
			else return FALSE;
		}
	}
?>