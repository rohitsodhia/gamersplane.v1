<?
	class fate_consts {
		private static $ladder = [-2 => 'Terrible', 'Poor', 'Mediocre', 'Average', 'Fair', 'Good', 'Great', 'Superb', 'Fantastic', 'Epic', 'Legendary'];

		public static function getLadder($rating = null) {
			if ($rating == null) {
				return self::$ladder;
			} elseif (array_key_exists($rating, self::$ladder)) {
				return self::$ladder[$rating];
			} else {
				return false;
			}
		}
	}
?>
