<?
	class Character_consts {
		private static $charTypes = array('fort' => 'con', 'ref' => 'dex', 'will' => 'wis');

		public static function getCharTypes($type = NULL) {
			if ($type == NULL) return self::$charTypes;
			elseif (in_array($type, self::$charTypes)) return TRUE;
			else return FALSE;
		}
	}
?>