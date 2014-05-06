<?
	class d20Character_consts {
		private static $statNames = array('str' => 'Strength', 'dex' => 'Dexterity', 'con' => 'Constitution', 'int' => 'Intelligence', 'wis' => 'Wisdom', 'cha' => 'Charisma');
		private static $saveNames = array('fort' => 'Fortitude', 'ref' => 'Reflex', 'will' => 'Will');
		private static $saveStats = array('fort' => 'con', 'ref' => 'dex', 'will' => 'wis');

		public static function getStatNames($stat = NULL) {
			if ($stat == NULL) return self::$statNames;
			elseif (in_array($stat, array_keys(self::$statNames))) return self::$statNames[$stat];
			else return FALSE;
		}

		public static function getSaveNames($save = NULL) {
			if ($save == NULL) return self::$saveNames;
			elseif (in_array($save, array_keys(self::$saveNames))) return self::$saveNames[$save];
			else return FALSE;
		}

		public static function getSaveStats($save = NULL) {
			if ($save == NULL) return self::$saveStats;
			elseif (in_array($save, array_keys(self::$saveStats))) return self::$saveStats[$save];
			else return FALSE;
		}
	}
?>