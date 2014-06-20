<?
	class shadowrun4_consts {
		private static $statNames = array('body' => 'Body', 'agility' => 'Agility', 'reaction' => 'Reaction', 'strength' => 'Strength', 'charisma' => 'Charisma', 'intuition' => 'Intuition', 'logic' => 'Logic', 'willpower' => 'Willpower', 'edge_total' => 'Total Edge', 'edge_current' => 'Current Edge', 'essence' => 'Essence', 'mag_res' => 'Magic or Resonance', 'initiative' => 'Initiative', 'initiative_passes' => 'Initiative Passes', 'matrix_initiative' => 'Matrix Initiative', 'astral_initiative' => 'Astral Initiative');

		public static function getStatNames($stat = NULL) {
			if ($stat == NULL) return self::$statNames;
			elseif (array_key_exists($stat, self::$statNames)) return self::$statNames[$stat];
			else return FALSE;
		}
	}
?>