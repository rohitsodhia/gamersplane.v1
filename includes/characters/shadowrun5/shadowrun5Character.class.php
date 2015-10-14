<?
	class shadowrun5Character extends Character {
		const SYSTEM = 'shadowrun5';

		protected $metatype = '';
		protected $reputation = array('street' => 0, 'notoriety' => 0, 'public' => 0);
		protected $karma = array('spent' => 0, 'total' => 0);
		protected $stats = array('body' => 0, 'agility' => 0, 'reaction' => 0, 'strength' => 0, 'willpower' => 0, 'logic' => 0, 'intuition' => 0, 'charisma' => 0, 'edge' => 0, 'essence' => 0, 'mag_res' => 0, 'initiative' => 0, 'matrix_initiative' => 0, 'astral_initiative' => 0);
		protected $limits = array('physical' => 0, 'mental' => 0, 'social' => 0);
		protected $damage = array('physical' => array('modify' => 0, 'current' => 0, 'overflow' => 0), 'stun' => array('modify' => 0, 'current' => 0));
		protected $skills = array();
		protected $qualities = array();
		protected $contacts = array();
		protected $weapons = array('ranged' => array(), 'melee' => array());
		protected $armor = array();
		protected $cyberdeck = array('model' => '', 'rating' => 0, 'attack' => 0, 'sleaze' => 0, 'data' => 0, 'firewall' => 0, 'programs' => array(), 'condition' => 0, 'notes' => '');
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

		public function setLimit($limit, $value) {
			$value = (int) $value;
			if (array_key_exists($limit, $this->limits) && $value >= 0) 
				$this->limits[$limit] = $value;
			else 
				return false;
		}

		public function setDamage($damage, $type, $value = 0) {
			if (array_key_exists($damage, $this->damage) && array_key_exists($type, $this->damage[$damage])) 
				$this->damage[$damage][$type] = (int) $value;
			else 
				return false;
		}

		public function addSkill($skill) {
			if (strlen($skill->name) && (int) $skill->rating > 0 && ($skill->type == 'a' || $skill->type == 'k')) {
				characters::newItemized('skill', $skill->name, $this::SYSTEM);
				$this->skills[] = array(
					'name' => sanitizeString($skill->name),
					'rating' => (int) $skill->rating,
					'type' => $skill->type
				);
			}
		}

		public function addQuality($quality) {
			if (strlen($quality->name) && ($quality->type == 'p' || $quality->type == 'n')) {
				characters::newItemized('quality', $quality->name, $this::SYSTEM);
				$this->qualities[] = array(
					'name' => sanitizeString($quality->name),
					'notes' => sanitizeString($quality->notes),
					'type' => $quality->type
				);
			}
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
			if ($type == 'ranged' && strlen($weapon->name) && strlen($weapon->damage)) 
				$this->weapons['ranged'][] = array(
					'name' => sanitizeString($weapon->name),
					'damage' => sanitizeString($weapon->damage),
					'accuracy' => sanitizeString($weapon->accuracy),
					'ap' => (int) $weapon->ap,
					'mode' => sanitizeString($weapon->mode),
					'rc' => (int) $weapon->rc,
					'ammo' => sanitizeString($weapon->ammo),
					'notes' => sanitizeString($weapon->notes)
				);
			elseif ($type == 'melee' && strlen($weapon->name) && strlen($weapon->damage)) 
				$this->weapons['melee'][] = array(
					'name' => sanitizeString($weapon->name),
					'damage' => sanitizeString($weapon->damage),
					'accuracy' => sanitizeString($weapon->accuracy),
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

		public function setCyberdeck($key, $value) {
			if (array_key_exists($key, $this->cyberdeck)) {
				if ($key == 'model' || $key == 'notes') 
					$this->cyberdeck[$key] = sanitizeString($value);
				else 
					$this->cyberdeck[$key] = intval($value) >= 0?intval($value):0;
			}
		}

		public function addProgram($program) {
			if (strlen($program->name)) {
				characters::newItemized('program', $program->name, $this::SYSTEM);
				$this->cyberdeck['programs'][] = array(
					'name' => sanitizeString($program->name),
					'notes' => sanitizeString($program->notes)
				);
			}
		}

		public function addAugmentation($augmentation) {
			if (strlen($augmentation->name)) {
				characters::newItemized('augmentation', sanitizeString($augmentation->name), $this::SYSTEM);
				$this->augmentations[] = array(
					'name' => sanitizeString($augmentation->name),
					'rating' => (int) $augmentation->rating >= 0?(int) $augmentation->rating:0,
					'notes' => sanitizeString($augmentation->notes),
					'essence' => (float) $augmentation->essence >= 0?(float) $augmentation->essence:0,
				);
			}
		}

		public function addSPRCF($sprcf) {
			if (strlen($sprcf->name)) {
				characters::newItemized('sprcf', sanitizeString($sprcf->name), $this::SYSTEM);
				$this->sprcf[] = array(
					'name' => sanitizeString($sprcf->name),
					'tt' => sanitizeString($sprcf->tt),
					'range' => sanitizeString($sprcf->range),
					'duration' => sanitizeString($sprcf->duration),
					'drain' => sanitizeString($sprcf->drain),
					'notes' => sanitizeString($sprcf->notes)
				);
			}
		}

		public function addPower($power) {
			if (strlen($power->name)) {
				characters::newItemized('power', sanitizeString($power->name), $this::SYSTEM);
				$this->powers[] = array(
					'name' => sanitizeString($power->name),
					'rating' => (int) $power->rating >= 0?(int) $power->rating:0,
					'notes' => sanitizeString($power->notes)
				);
			}
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
				foreach ($data->reputation as $rep => $value) 
					$this->setReputation($rep, $value);
				foreach ($data->karma as $type => $value) 
					$this->setKarma($type, $value);
				foreach ($data->stats as $stat => $value) 
					$this->setStat($stat, $value);
				foreach ($data->limits as $limit => $value) 
					$this->setLimit($limit, $value);
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
				if (sizeof($data->weapons->ranged)) 
					foreach ($data->weapons->ranged as $weapon) 
						$this->addWeapon('ranged', $weapon);
				if (sizeof($data->weapons->melee)) 
					foreach ($data->weapons->melee as $weapon) 
						$this->addWeapon('melee', $weapon);
				$this->clearVar('armor');
				if (sizeof($data->armor)) 
					foreach ($data->armor as $armor) 
						$this->addArmor($armor);
				$this->cyberdeck['programs'] = array();
				$this->setCyberdeck('model', $data->cyberdeck->model);
				$this->setCyberdeck('rating', $data->cyberdeck->rating);
				$this->setCyberdeck('attack', $data->cyberdeck->attack);
				$this->setCyberdeck('sleaze', $data->cyberdeck->sleaze);
				$this->setCyberdeck('data', $data->cyberdeck->data);
				$this->setCyberdeck('firewall', $data->cyberdeck->firewall);
				$this->setCyberdeck('condition', $data->cyberdeck->condition);
				if (sizeof($data->cyberdeck->programs)) 
					foreach ($data->cyberdeck->programs as $program) 
						$this->addProgram($program);
				$this->setCyberdeck('notes', $data->cyberdeck->notes);
				$this->clearVar('augmentations');
				if (sizeof($data->augmentations)) 
					foreach ($data->augmentations as $augmentation) 
						$this->addAugmentation($augmentation);
				$this->clearVar('sprcf');
				if (sizeof($data->sprcf)) 
					foreach ($data->sprcf as $sprcf) 
						$this->addSPRCF($sprcf);
				$this->clearVar('powers');
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