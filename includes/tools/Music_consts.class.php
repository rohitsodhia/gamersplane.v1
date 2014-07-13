<?
	class Music_consts {
		private static $genres = array('Horror/Survival', 'Wild West', 'Fantasy', 'Modern', 'Epic', 'Cyberpunk', 'Espionage', 'Sci-fi');

		public static function getGenres() {
			$genres = self::$genres;
			sort($genres);
			return $genres;
		}

		public static function validateGameType($genre) {
			if (in_array($genre, self::$genres)) return TRUE;
			else return FALSE;
		}
	}
?>