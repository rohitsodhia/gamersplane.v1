<?
	class d20Character_consts {
		private static $statNames = array('str' => 'Strength', 'dex' => 'Dexterity', 'con' => 'Constitution', 'int' => 'Intelligence', 'wis' => 'Wisdom', 'cha' => 'Charisma');

		public static function getStatNames($stat = NULL) {
			if ($stat == NULL) return self::$statNames;
			elseif (in_array($stat, array_keys(self::$statNames))) return self::$statNames[$stat];
			else return FALSE;
		}
	}
?>