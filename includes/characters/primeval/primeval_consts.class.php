<?
	class primeval_consts {
		private static $skills = array('Athletics', 'Animal Handling', 'Convince', 'Craft', 'Fighting', 'Knowledge', 'Marksman', 'Medicine', 'Science', 'Subterfuge', 'Survival', 'Technology', 'Transport'); 

		public static function getSkills($skill = null) {
			if ($skill == null) 
				return self::$skills;
			elseif (array_key_exists($skill, self::$skills)) 
				return self::$skills[$skill];
			else 
				return false;
		}
	}
?>