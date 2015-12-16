<?
	class tor_consts {
		private static $skills = array(
			'Body' => array(
				'personality' => 'Awe',
				'movement' => 'Athletics',
				'perception' => 'Awareness',
				'survival' => 'Explore',
				'custom' => 'Song',
				'vocation' => 'Craft',
			),
			'Heart' => array(
				'personality' => 'Inspire',
				'movement' => 'Travel',
				'perception' => 'Insight',
				'survival' => 'Healing',
				'custom' => 'Courtesy',
				'vocation' => 'Battle',
			),
			'Wits' => array(
				'personality' => 'Persuade',
				'movement' => 'Stealth',
				'perception' => 'Search',
				'survival' => 'Hunting',
				'custom' => 'Riddle',
				'vocation' => 'Lore'
			)
		);

		public static function getStatNames($stat = null) {
			if ($stat == null) 
				return self::$statNames;
			elseif (array_key_exists($stat, self::$statNames)) 
				return self::$statNames[$stat];
			else 
				return false;
		}
	}
?>