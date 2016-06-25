<?
	class starwarsffg_consts {
		private static $statNames = array('bra' => 'Brawn', 'agi' => 'Agility', 'int' => 'Intellect', 'cun' => 'Cunning', 'wil' => 'Willpower', 'pre' => 'Presence');

		public static function getStatNames($stat = NULL) {
			if ($stat == NULL) return self::$statNames;
			elseif (array_key_exists($stat, self::$statNames)) return self::$statNames[$stat];
			else return FALSE;
		}
	}
?>
