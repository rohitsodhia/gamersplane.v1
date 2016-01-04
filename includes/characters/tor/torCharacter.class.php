<?
	class torCharacter extends Character {
		const SYSTEM = 'tor';

		protected $culture = array('name' => '', 'blessing' => '');
		protected $sol = 0;
		protected $calling = '';
		protected $shadow_weakness = '';
		protected $traits = array('specialties' => '', 'features' => '');
		protected $experience = array('spent' => 0, 'total' => 0);
		protected $valor = 0;
		protected $wisdom = 0;
		protected $attributes = array('body' => array('standard' => 0, 'favoured' => 0), 'heart' => array('standard' => 0, 'favoured' => 0), 'wits' => array('standard' => 0, 'favoured' => 0));
		protected $skillGroups = array('personality' => 0, 'movement' => 0, 'perception' => 0, 'survival' => 0, 'custom' => 0, 'vocation' => 0);
		protected $skills = array('Awe' => 0, 'Athletics' => 0, 'Awareness' => 0, 'Explore' => 0, 'Song' => 0, 'Craft' => 0, 'Inspire' => 0, 'Travel' => 0, 'Insight' => 0, 'Healing' => 0, 'Courtesy' => 0, 'Battle' => 0, 'Persuade' => 0, 'Stealth' => 0, 'Search' => 0, 'Hunting' => 0, 'Riddle' => 0, 'Lore' => 0);
		protected $weaponSkills = array();
		protected $weapons = array();
		protected $combat = array('damage' => 0, 'ranged' => 0, 'parry' => 0, 'shield' => 0, 'armor' => 0, 'head' => 0);
		protected $gear = array();
		protected $hp = array('endurance' => array('max' => 0, 'current' => 0, 'rating' => 0, 'fatigue' => 0), 'hope' => array('max' => 0, 'current' => 0, 'rating' => 0, 'shadow' => 0));
		protected $status = array('weary' => false, 'miserable' => false, 'wounded' => false);
		protected $rewards = array();
		protected $virtues = array();
		protected $fellowship = 0;
		protected $advancement = 0;
		protected $treasure = 0;
		protected $standing = 0;

		public function setCulture($key, $value) {
			if ($key == 'name' || $key == 'blessing') 
				$this->culture[$key] = sanitizeString($value);
		}

		public function setSOL($value) {
			$this->sol = intval($value) >= 0?intval($value):0;
		}

		public function setCalling($value) {
			$this->calling = sanitizeString($value);
		}

		public function setShadowWeakness($value) {
			$this->shadow_weakness = sanitizeString($value);
		}

		public function setTrait($key, $value) {
			if ($key == 'specialties' || $key == 'features') 
				$this->traits[$key] = sanitizeString($value);
		}

		public function setExperience($key, $value) {
			if ($key == 'spent' || $key == 'total') 
				$this->traits[$key] = intval($value);
		}

		public function setValor($value) {
			$this->valor = intval($value) >= 0?intval($value):0;
		}

		public function setWidsom($value) {
			$this->wisdom = intval($value) >= 0?intval($value):0;
		}

		public function setAttribute($attribute, $type, $value = 0) {
			if (array_key_exists($attribute, $this->attributes) && array_key_exists($type, $this->attributes[$attribute])) 
				$this->attributes[$attribute][$type] = intval($value) >= 0?intval($value):0;
		}

		public function setSkillGroup($group, $value = 0) {
			if (array_key_exists($group, $this->skillGroups)) 
				$this->skillGroups[$group] = intval($value) >= 0 && intval($value) <= 3?intval($value):0;
		}

		public function setSkill($group, $value = 0) {
			if (array_key_exists($group, $this->skills)) 
				$this->skills[$group] = intval($value) >= 0 && intval($value) <= 5?intval($value):0;
		}

		public function addWeaponSkill($name, $rank = 0) {
			$name = sanitizeString($name);
			$rank = intval($rank) >= 0 && intval($rank) <= 5?intval($rank):0;
			if (strlen($name)) 
				$this->weaponSkills[] = array('name' => $name, 'rank' => $rank);
		}

		public function addWeapon($rawWeapon) {
			$weapon = array(
				'name' => sanitizeString($rawWeapon->name),
				'damage' => intval($rawWeapon->damage),
				'edge' => intval($rawWeapon->edge),
				'injury' => intval($rawWeapon->injury),
				'enc' => intval($rawWeapon->enc)
			);
			if (strlen($weapon['name'])) 
				$this->weapons[] = $weapon;
		}

		public function setCombat($stat, $value = 0) {
			if (array_key_exists($stat, $this->combat)) 
				$this->combat[$stat] = intval($value) >= 0 && intval($value) <= 5?intval($value):0;
		}

		public function addGear($name, $enc = 0) {
			$name = sanitizeString($name);
			$enc = intval($enc) >= 0 && intval($enc) <= 5?intval($rank):0;
			if (strlen($name)) 
				$this->gear[] = array('name' => $name, 'enc' => $enc);
		}

		public function setHP($hp, $state, $value = 0) {
			if (array_key_exists($hp, $this->hp) && array_key_exists($state, $this->hp[$hp])) 
				$this->hp[$hp][$state] = intval($value) >= 0?intval($value):0;
		}

		public function setStatus($status, $value = false) {
			if (array_key_exists($status, $this->status)) 
				$this->status[$status] = (bool) $value;
		}

		public function setRewards($value) {
			$this->rewards = sanitizeString($value);
		}

		public function setVirtues($value) {
			$this->virtues = sanitizeString($value);
		}

		public function setFellowship($value) {
			$this->fellowship = intval($value);
		}

		public function setAdvancement($value) {
			$this->advancement = intval($value);
		}

		public function setTreasure($value) {
			$this->treasure = intval($value);
		}

		public function setStanding($value) {
			$this->standing = intval($value);
		}

		public function save($bypass = false) {
			global $mysql;
			if (isset($_POST['character'])) 
				$data = $_POST['character'];
			else 
				$data = $_POST;

			if (!$bypass) {
				$this->setName($data->name);
				$this->setCulture('name', $data->culture->name);
				$this->setCulture('blessing', $data->culture->blessing);
				$this->setSOL($data->sol);
				$this->setCalling($data->calling);
				$this->setShadowWeakness($data->shadow_weakness);
				foreach ($data->traits as $trait => $value) 
					$this->setTrait($trait, $value);
				foreach ($data->experience as $experience => $value) 
					$this->setExperience($experience, $value);
				$this->setValor($data->valor);
				$this->setWidsom($data->wisdom);
				foreach ($data->attributes as $attribute => $set) 
					foreach ($set as $type => $value) 
						$this->setAttribute($attribute, $type, $value);
				foreach ($data->skillGroups as $group => $value) 
					$this->setSkillGroup($group, $value);
				foreach ($data->skills as $skill => $value) 
					$this->setSkill($skill, $value);
				$this->clearVar('weaponSkills');
				if (sizeof($data->weaponSkills)) 
					foreach ($data->weaponSkills as $skill) 
						$this->addWeaponSkill($skill->name, $skill->rank);
				$this->clearVar('weapons');
				if (sizeof($data->weapons)) 
					foreach ($data->weapons as $weapon) 
						$this->addweapon($weapon);
				foreach ($data->combat as $stat => $value) 
					$this->setCombat($stat, $value);
				$this->clearVar('gear');
				if (sizeof($data->mainGear)) 
					foreach ($data->mainGear as $gear) 
						$this->addGear($gear->name, $gear->enc);
				if (sizeof($data->gear)) 
					foreach ($data->gear as $gear) 
						$this->addGear($gear->name, $gear->enc);
				foreach ($data->hp as $type => $hp) 
					foreach ($hp as $state => $value) 
						$this->setHP($type, $state, $value);
				foreach ($data->status as $status => $value) 
					$this->setStatus($status, $value);
				$this->setRewards($data->rewards);
				$this->setVirtues($data->virtues);
				$this->setFellowship($data->fellowship);
				$this->setAdvancement($data->advancement);
				$this->setTreasure($data->treasure);
				$this->setStanding($data->standing);
				$this->setNotes($data->notes);
			}

			parent::save();
		}
	}
?>