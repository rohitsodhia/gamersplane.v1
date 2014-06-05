<?
	class dnd4_consts {
		private static $alignments = array('g' => 'Good', 'lg' => 'Lawful Good', 'e' => 'Evil', 'ce' => 'Chaotic Evil', 'u' => 'Unaligned');

		public static function getAlignments($alignment = NULL) {
			if ($alignment == NULL) return self::$alignments;
			elseif (array_key_exists($alignment, self::$alignments)) return self::$alignments[$alignment];
			else return FALSE;
		}
	}
?>