<?
	class shadowrun5Character extends Character {
		const SYSTEM = 'shadowrun5';

		protected $metatype = '';
		protected $reputation = array('street' => 0, 'notoriety' => 0, 'public' => 0);
		protected $karma = array('spent' => 0, 'total' => 0);
		protected $stats = array('body' => 0, 'agility' => 0, 'reaction' => 0, 'strength' => 0, 'willpower' => 0, 'logic' => 0, 'intuition' => 0, 'charisma' => 0, 'edge_total' => 0, 'edge_current' => 0, 'essence' => 0, 'mag_res' => 0, 'initiative' => 0, 'matrix_initiative' => 0, 'astral_initiative' => 0);
		protected $damage = array('physical' => array('total' => 0, 'current' => 0), 'stun' => array('total' => 0, 'current' => 0));
		protected $skills = array();
		protected $qualities = array();
		protected $contacts = array();
		protected $weapons = array('ranged' => array(), 'melee' => array());
		protected $armor = array();
		protected $programs = array();
		protected $augmentations = array();
		protected $sprcf = array();
		protected $powers = array();
		protected $gear = array();

		public function setMetatype($value) {
			$this->metatype = sanitizeString($value);
		}

		public function setReputation($rep, $value = 0) {
			if (array_key_exists($rep, $this->reputation) && (int) $value >= 0) 
				$this->reputation[$rep] = (int) $value;
			else 
				return false;
		}

		public function setKarma($key, $value = 0) {
			if (array_key_exists($key, $this->karma) && (int) $value >= 0) 
				$this->karma[$key] = (int) $value;
			else 
				return false;
		}

		public function setStat($stat, $value = 0) {
			$value = floatval($value);
			if (array_key_exists($stat, $this->stats) && $value >= 0) 
				$this->stats[$stat] = $value;
			else 
				return false;
		}

		public function setDamage($damage, $type, $value = 0) {
			if (array_key_exists($damage, $this->damage) && ($type == 'total' || $type == 'current') && (int) $value >= 0) 
				$this->damage[$type] = (int) $value;
			else 
				return false;
		}

		public function addSkill($skill) {
			if (strlen($skill->name) && (int) $skill->rating > 0 && ($skill->type == 'a' || $skill->type == 'k')) 
				$this->skills[] = array(
					'name' => sanitizeString($skill->name),
					'rating' => (int) $skill->rating,
					'type' => $skill->type
				);
		}

		public function addQuality($quality) {
			if (strlen($quality->name) && ($quality->type == 'p' || $quality->type == 'n')) 
				$this->qualities[] = array(
					'name' => sanitizeString($quality->name),
					'notes' => sanitizeString($quality->notes),
					'type' => $quality->type
				);
		}

		public function addContact($contact) {
			if (strlen($contact->name)) 
				$this->contacts[] = array(
					'name' => sanitizeString($contact->name),
					'loyalty' => (int) $contact->loyalty >= 0?(int) $contact->loyalty:0,
					'connection' => (int) $contact->connection >= 0?(int) $contact->connection:0,
					'notes' => sanitizeString($contact->notes)
				);
		}

		public function addWeapon($type, $weapon) {
			if ($type == 'ranged' && strlen($contact->name) && strlen($contact->damage)) 
				$this->weapons['ranged'][] = array(
					'name' => sanitizeString($weapon->name),
					'damage' => sanitizeString($weapon->damage),
					'acc' => sanitizeString($weapon->acc),
					'ap' => (int) $weapon->ap,
					'mode' => sanitizeString($weapon->mode),
					'rc' => (int) $weapon->rc,
					'ammo' => sanitizeString($weapon->ammo),
					'notes' => sanitizeString($weapon->notes)
				);
			elseif ($type == 'melee' && strlen($contact->name) && strlen($contact->damage)) 
				$this->weapons['melee'][] = array(
					'name' => sanitizeString($weapon->name),
					'damage' => sanitizeString($weapon->damage),
					'acc' => sanitizeString($weapon->acc),
					'ap' => (int) $weapon->ap,
					'reach' => (int) $weapon->reach,
					'notes' => sanitizeString($weapon->notes)
				);
		}

		public function addArmor($armor) {
			if (strlen($armor->name)) 
				$this->armor[] = array(
					'name' => sanitizeString($armor->name),
					'rating' => (int) $armor->rating >= 0?(int) $armor->rating:0,
					'notes' => sanitizeString($armor->notes)
				);
		}

		public function addProgram($program) {
			if (strlen($program->name)) 
				$this->programs[] = array(
					'name' => sanitizeString($program->name),
					'notes' => sanitizeString($program->notes)
				);
		}

		public function addAugmentation($augmentation) {
			if (strlen($augmentation->name)) 
				$this->augmentation[] = array(
					'name' => sanitizeString($augmentation->name),
					'rating' => (int) $augmentation->rating >= 0?(int) $augmentation->rating:0,
					'notes' => sanitizeString($augmentation->notes),
					'essence' => (float) $augmentation->rating >= 0?(float) $augmentation->rating:0,
				);
		}

		public function addSPRCF($sprcf) {
			if (strlen($sprcf->name)) 
				$this->sprcf[] = array(
					'name' => sanitizeString($sprcf->name),
					'tt' => sanitizeString($sprcf->tt),
					'range' => (int) $sprcf->range >= 0?(int) $sprcf->range:0,
					'duration' => (int) $sprcf->duration >= 0?(int) $sprcf->duration:0,
					'notes' => sanitizeString($sprcf->notes),
				);
		}

		public function addPower($power) {
			if (strlen($power->name)) 
				$this->power[] = array(
					'name' => sanitizeString($power->name),
					'rating' => (int) $power->rating >= 0?(int) $power->rating:0,
					'notes' => sanitizeString($power->notes)
				);
		}

		public function addGear($gear) {
			if (strlen($gear->name)) 
				$this->gear[] = array(
					'name' => sanitizeString($gear->name),
					'rating' => (int) $gear->rating >= 0?(int) $gear->rating:0,
					'notes' => sanitizeString($gear->notes)
				);
		}

		public function save($bypass = false) {
			global $mysql;
			if (isset($_POST['character'])) 
				$data = $_POST['character'];
			else 
				$data = $_POST;

			if (!$bypass) {
				$this->setName($data->name);
				$this->setMetatype($data->metatype);
				foreach ($data->reputations as $rep => $value) 
					$this->setReputation($rep, $value);
				foreach ($data->karma as $type => $value) 
					$this->setKarma($type, $value);
				foreach ($data->stats as $stat => $value) 
					$this->setStat($stat, $value);
				foreach ($data->damage->physical as $key => $value) 
					$this->setDamage('physical', $key, $value);
				foreach ($data->damage->stun as $key => $value) 
					$this->setDamage('stun', $key, $value);
				$this->clearVar('skills');
				if (sizeof($data->skills)) 
					foreach ($data->skills as $skill) 
						$this->addSkill($skill);
				$this->clearVar('qualities');
				if (sizeof($data->qualities)) 
					foreach ($data->qualities as $quality) 
						$this->addQuality($quality);
				$this->clearVar('contacts');
				if (sizeof($data->contacts)) 
					foreach ($data->contacts as $contact) 
						$this->addContact($contact);
				$this->clearVar('weapons');
				$this->weapons = array('ranged' => array(), 'melee' => array());
				if (sizeof($data->weapons['ranged'])) 
					foreach ($data->weapons['ranged'] as $weapon) 
						$this->addWeapon('ranged', $weapon);
				if (sizeof($data->weapons['melee'])) 
					foreach ($data->weapons['melee'] as $weapon) 
						$this->addWeapon($weapon);
				$this->clearVar('armor');
				if (sizeof($data->armors)) 
					foreach ($data->armor as $armor) 
						$this->addArmor($armor);
				$this->clearVar('programs');
				if (sizeof($data->programs)) 
					foreach ($data->programs as $program) 
						$this->addProgram($program);
				$this->clearVar('augmentations');
				if (sizeof($data->augmentations)) 
					foreach ($data->augmentations as $augmentation) 
						$this->addAugmentation($augmentation);
				$this->clearVar('sprcf');
				if (sizeof($data->sprcf)) 
					foreach ($data->sprcf as $sprcf) 
						$this->addSPRCF($sprcf);
				$this->clearVar('programs');
				if (sizeof($data->powers)) 
					foreach ($data->powers as $power) 
						$this->addPower($power);
				$this->clearVar('gear');
				if (sizeof($data->gear)) 
					foreach ($data->gear as $gear) 
						$this->addGear($gear);
				$this->setNotes($data->notes);
			}

			parent::save();
		}
	}
?>