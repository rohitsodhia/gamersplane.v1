<?
	class d20Character_consts {
		private static $statNames = array('str' => 'Strength', 'dex' => 'Dexterity', 'con' => 'Constitution', 'int' => 'Intelligence', 'wis' => 'Wisdom', 'cha' => 'Charisma');
		private static $saveNames = array('fort' => 'Fortitude', 'ref' => 'Reflex', 'will' => 'Will');

		public static function getStatNames($stat = null) {
			if ($stat == null) return self::$statNames;
			elseif (array_key_exists($stat, self::$statNames)) return self::$statNames[$stat];
			else return false;
		}

		public static function getSaveNames($save = null) {
			if ($save == null) return self::$saveNames;
			elseif (array_key_exists($save, self::$saveNames)) return self::$saveNames[$save];
			else return false;
		}
	}
?>