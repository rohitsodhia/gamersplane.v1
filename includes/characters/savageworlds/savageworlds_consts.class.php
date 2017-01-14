<?
	class savageworlds_consts {
		private static $traits = ['agi' => 'Agility', 'sma' => 'Smarts', 'spi' => 'Spirit', 'str' => 'Strength', 'vig' => 'Vigor'];

		public static function getTraits($stat = null) {
			if ($stat == null) {
				return self::$traits;
			} elseif (array_key_exists($stat, self::$traits)) {
				return self::$traits[$stat];
			} else {
				return false;
			}
		}
	}
?>
