<?
	class cthulhu_brs7eCharacter extends Character {
		const SYSTEM = 'cthulhu_brs7e';

		protected $occupation = '';
		protected $characteristics = array('str' => 0, 'dex' => 0, 'int' => 0, 'con' => 0, 'app' => 0, 'pow' => 0, 'siz' => 0, 'edu' => 0, 'move' => 0);
		protected $hp = array('current' => 0, 'max' => 0, 'major' => false);
		protected $sanity = array('current' => 0, 'max' => 0, 'temp' => false, 'indef' => false);
		protected $luck = 0;
		protected $mp = array('current' => 0, 'max' => 0);
		protected $skills = array();
		protected $weapons = array();
		protected $combat = array('damage_bonus' => 0, 'build' => 0);
		protected $items = array();

		public function setOccupation($value) {
			$this->occupation = sanitizeString($value);
		}

		public function setCharacteristic($characteristic, $value = 0) {
			$value = (int) $value;
			if (array_key_exists($characteristic, $this->characteristics) && $value >= 0) 
				$this->characteristics[$characteristic] = $value;
			else 
				return false;
		}

		public function setHP($type, $value = 0) {
			if (array_key_exists($type, $this->hp)) {
				if ($type == 'major') 
					$this->hp[$type] = (bool) $value;
				else 
					$this->hp[$type] = (int) $value >= 0?(int) $value:0;
			} else 
				return false;
		}

		public function setSanity($type, $value = 0) {
			if (array_key_exists($type, $this->sanity)) {
				if ($type == 'current' || $type == 'max') 
					$this->sanity[$type] = (int) $value >= 0?(int) $value:0;
				else 
					$this->sanity[$type] = (bool) $value;
			} else 
				return false;
		}

		public function setLuck($type, $value = 0) {
			$value = (int) $value >= 0?(int) $value:0;
			if (array_key_exists($type, $this->luck)) 
				$this->luck[$type] = $value;
			else 
				return false;
		}

		public function setMP($type, $value = 0) {
			$value = (int) $value >= 0?(int) $value:0;
			if (array_key_exists($type, $this->mp)) 
				$this->mp[$type] = $value;
			else 
				return false;
		}

		public function addSkill($skill) {
			if (strlen($skill->name) && (int) $skill->value > 0) {
				characters::newItemized('skill', $skill->name, $this::SYSTEM);
				$this->skills[] = array(
					'name' => sanitizeString($skill->name),
					'value' => (int) $skill->value,
				);
			}
		}

		public function addWeapon($weapon) {
			if (strlen($weapon->name) && strlen($weapon->damage)) 
				$this->weapons[] = array(
					'name' => sanitizeString($weapon->name),
					'regular' => (int) $weapon->regular,
					'hard' => (int) $weapon->hard,
					'extreme' => (int) $weapon->extreme,
					'damage' => sanitizeString($weapon->damage),
					'range' => $weapon->range?(int) $weapon->range:'-',
					'attacks' => (int) $weapon->attacks,
					'ammo' => $weapon->ammo?(int) $weapon->ammo:null,
					'malf' => $weapon->malf?(int) $weapon->malf:null
				);
		}

		public function addItem($item) {
			if (strlen($item->name)) 
				$this->items[] = array(
					'name' => sanitizeString($item->name),
					'notes' => sanitizeString($item->notes)
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
				$this->setOccupation($data->occupation);
				foreach ($data->characteristics as $characteristic => $value) 
					$this->setCharacteristic($characteristic, $value);
				foreach ($data->hp as $type => $value) 
					$this->setHP($type, $value);
				foreach ($data->sanity as $type => $value) 
					$this->setSanity($type, $value);
				foreach ($data->luck as $type => $value) 
					$this->setLuck($type, $value);
				foreach ($data->mp as $type => $value) 
					$this->setMP($type, $value);
				$this->clearVar('skills');
				if (sizeof($data->skills)) 
					foreach ($data->skills as $skill) 
						$this->addSkill($skill);
				$this->clearVar('weapons');
				if (sizeof($data->weapons)) 
					foreach ($data->weapons as $weapon) 
						$this->addWeapon($weapon);
				$this->clearVar('items');
				if (sizeof($data->items)) 
					foreach ($data->items as $item) 
						$this->addItem($item);
				$this->setNotes($data->notes);
			}

			parent::save();
		}
	}
?>