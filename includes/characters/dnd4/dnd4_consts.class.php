<?
	class dnd4_consts {
		private static $alignments = [
			'g' => 'Good',
			'lg' => 'Lawful Good',
			'e' => 'Evil',
			'ce' => 'Chaotic Evil',
			'u' => 'Unaligned'
		];

		public static function getAlignments($alignment = null) {
			if ($alignment === null) {
				return self::$alignments;
			} elseif (array_key_exists($alignment, self::$alignments)) {
				return self::$alignments[$alignment];
			} else {
				return false;
			}
		}
	}
?>
