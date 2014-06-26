<?
	class Music_consts {
		private static $gameTypes = array('Horror/Survival', 'Wild West', 'Fantasy', 'Modern', 'Epic', 'Cyberpunk', 'Espionage', 'Sci-fi');

		public static function getGameTypes() {
			$types = self::$gameTypes;
			sort($types);
			return $types;
		}

		public static function validateGameType($type) {
			if (in_array($type, self::$gameTypes)) return TRUE;
			else return FALSE;
		}
	}
?>