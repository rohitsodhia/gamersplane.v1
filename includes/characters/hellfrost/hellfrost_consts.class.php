<?
	class savageworlds_consts {
		private static $traits = array('agi' => 'Agility', 'sma' => 'Smarts', 'spi' => 'Spirit', 'str' => 'Strength', 'vig' => 'Vigor'); 

		public static function getTraits($stat = NULL) {
			if ($stat == NULL) return self::$traits;
			elseif (array_key_exists($stat, self::$traits)) return self::$traits[$stat];
			else return FALSE;
		}
	}
?>