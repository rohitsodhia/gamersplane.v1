<?
	class cthulhu_brs7e_consts {
		private static $skills = [
			'Accounting' => 5,
			'Anthropolgy' => 1,
			'Appraise' => 5,
			'Archaeology' => 1,
			'Art/Craft' => 5,
			'Charm' => 15,
			'Climb' => 20,
			'Disguise' => 5,
			'Drive Auto' => 20,
			'Elec Repair' => 10,
			'Fast Talk' => 5,
			'Fighting (Brawl)' => 25,
			'Firearms (Handgun)' => 20,
			'Firearms (Rifle/Shotgun)' => 25,
			'First Aid' => 30,
			'History' => 5,
			'Intimidate' => 15,
			'Jump' => 20,
			'Language (Other)' => 1,
			'Law' => 5,
			'Library Use' => 20,
			'Listen' => 20,
			'Locksmith' => 1,
			'Mech Repair' => 10,
			'Medicine' => 1,
			'Natural Wonder' => 10,
			'Navigate' => 10,
			'Occult' => 5,
			'Operate Heavy Machine' => 1,
			'Persuade' => 10,
			'Pilot' => 1,
			'Psychology' => 10,
			'Psychoanalysis' => 1,
			'Ride' => 5,
			'Science' => 1,
			'Sleight of Hand' => 10,
			'Spot Hidden' => 25,
			'Stealth' => 20,
			'Survival' => 10,
			'Swim' => 20,
			'Throw' => 20,
			'Track' => 10
		];

		public static function getStatNames($stat = null) {
			if ($stat === null) {
				return self::$statNames;
			} elseif (array_key_exists($stat, self::$statNames)) {
				return self::$statNames[$stat];
			} else {
				return false;
			}
		}
	}
?>
