<?
	class dnd3_consts {
		private static $alignments = [
			'lg' => 'Lawful Good',
			'ng' => 'Neutral Good',
			'cg' => 'Chaotic Good',
			'ln' => 'Lawful Neutral',
			'tn' => 'True Neutral',
			'cn' => 'Chaotic Neutral',
			'le' => 'Lawful Evil',
			'ne' => 'Neutral Evil',
			'ce' => 'Chaotic Evil'
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
