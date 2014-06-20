<?
	class wod_consts {
		private static $attrNames = array('Power' => array('int' => 'Intelligence', 'str' => 'Strength', 'pre' => 'Presence'), 'Finesse' => array('wit' => 'Wits', 'dex' => 'Dexterity', 'man' => 'Manipulation'), 'Resistance' => array('res' => 'Resolve', 'sta' => 'Stamina', 'com' => 'Composure'));
		private static $skills = array(
					'Mental' => array('Academics', 'Computer', 'Crafts', 'Investigation', 'Medicine', 'Occult', 'Politics', 'Science'),
					'Physical' => array('Athletics', 'Brawl', 'Drive', 'Firearms', 'Larceny', 'Stealth', 'Survival', 'Weaponry'),
					'Social' => array('Animal Ken', 'Empathy', 'Expression', 'Intimidation', 'Persuasion', 'Socialize', 'Streetwise', 'Subterfuge')
		);

		public static function getAttrNames($category = NULL, $attr = NULL) {
			if ($category == NULL) return self::$attrNames;
			elseif (array_key_exists($category, self::$attrNames) && $attr == NULL) return self::$attrNames[$category];
			elseif (array_key_exists($category, self::$attrNames) && array_key_exists($attr, self::$attrNames[$category])) return self::$attrNames[$category][$attr];
			else return FALSE;
		}

		public static function getSkillNames($category = NULL, $skill = NULL) {
			if ($category == NULL) return self::$skills;
			elseif (array_key_exists($category, self::$skills) && $skill == NULL) return self::$skills[$category];
			elseif (array_key_exists($category, self::$skills) && array_key_exists($skill, self::$skills[$category])) return self::$skills[$category][$skill];
			else return FALSE;
		}
	}
?>