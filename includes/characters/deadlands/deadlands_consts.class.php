<?
	class deadlands_consts {
		private static $stats = [
			'cog' => 'Cognition',
			'kno' => 'Knowledge',
			'mie' => 'Mien',
			'sma' => 'Smarts',
			'spi' => 'Spirit',
			'def' => 'Deftness',
			'nim' => 'Nimbleness',
			'str' => 'Strength',
			'qui' => 'Quickness',
			'vig' => 'Vigor'
		];

		public static function getStats($stat = null) {
			if ($stat === null) {
				return self::$stats;
			} elseif (array_key_exists($stat, self::$stats)) {
				return self::$stats[$stat];
			} else {
				return FALSE;
			}
		}
	}
?>
