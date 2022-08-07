<?
	class numeneraCharacter extends Character {
		const SYSTEM = 'numenera';

		protected $descriptor = '';
		protected $type = '';
		protected $focus = '';
		protected $tier = 0;
		protected $effort = 0;
		protected $xp = 0;
		protected $attributes = [
							'might' => [
								'pool' => [
									'current' => 0,
									'max' => 0
								],
								'edge' => 0
							],
							'speed' => [
								'pool' => [
									'current' => 0,
									'max' => 0
								],
								'edge' => 0
							],
							'intellect' => [
								'pool' => [
									'current' => 0,
									'max' => 0
								],
								'edge' => 0
							]
						];
		protected $damage = ['impaired' => false, 'debilitated' => false];
		protected $recovery = 0;
		protected $recoveryTimes = ['action' => false, 'ten_min' => false, 'hour' => false, 'ten_hours' => false];
		protected $armor = 0;
		protected $attacks = null;
		protected $skills = null;
		protected $specialAbilities = null;
		protected $cyphers = null;
		protected $possessions = '';

		public function setDescriptor($descriptor) {
			$this->descriptor = sanitizeString($descriptor);
		}

		public function setType($type) {
			$this->type = sanitizeString($type);
		}

		public function setFocus($focus) {
			$this->focus = sanitizeString($focus);
		}

		public function setTier($tier) {
			$this->tier = intval($tier);
		}

		public function setEffort($effort) {
			$this->effort = intval($effort);
		}

		public function setXP($xp) {
			$this->xp = intval($xp);
		}

		public function setAttribute($attr, $key, $value = null) {
			$key = explode('.', $key);
			$cKey = &$this->attributes[$attr];
			foreach ($key as $iKey) {
				if (!isset($cKey[$iKey]))
					return false;
				$cKey = &$cKey[$iKey];
			}
			$cKey = (int) $value;
		}

		public function setDamage($key, $value = null) {
			if (array_key_exists($key, $this->damage))
				$this->damage[$key] = (bool) $value;
			else
				return false;
		}

		public function setRecovery($recovery) {
			$this->recovery = intval($recovery);
		}

		public function setRecoveryTimes($key, $value = null) {
			if (array_key_exists($key, $this->recoveryTimes))
				$this->recoveryTimes[$key] = (bool) $value;
			else
				return false;
		}

		public function setArmor($armor) {
			$this->armor = intval($armor);
		}

		public function addAttack($attack) {
			if (strlen($attack->name)) {
				$attack = [
					'name' => sanitizeString($attack->name),
					'mod' => (int) $attack->mod,
					'dmg' => (int) $attack->dmg
				];
				$this->attacks[] = $attack;
			}
		}

		public function addSkill($skill) {
			if (strlen($skill->name)) {
				characters::newItemized('skill', $skill->name, $this::SYSTEM);
				$skill = [
					'name' => sanitizeString($skill->name),
					'attr' => array_search($skill->attr, ['m', 's', 'i'])?$skill->attr:'m',
					'prof' => array_search($skill->prof, ['', 't', 's'])?$skill->prof:''
				];
				$this->skills[] = $skill;
			}
		}

		public function addSpecialAbility($specialAbility) {
			if (strlen($specialAbility->name)) {
				characters::newItemized('specialAbility', $specialAbility->name, $this::SYSTEM);
				$specialAbility = [
					'name' => sanitizeString($specialAbility->name),
					'notes' => sanitizeString($specialAbility->notes)
				];
				$this->specialAbilities[] = $specialAbility;
			}
		}

		public function addCypher($cypher) {
			if (strlen($cypher->name)) {
				characters::newItemized('cypher', $cypher->name, $this::SYSTEM);
				$cypher = [
					'name' => sanitizeString($cypher->name),
					'notes' => sanitizeString($cypher->notes)
				];
				$this->cyphers[] = $cypher;
			}
		}

		public function setPossessions($possessions) {
			$this->possessions = sanitizeString($possessions);
		}

		public function save($bypass = false) {
			global $mysql;
			if (isset($_POST['character']))
				$data = $_POST['character'];
			else
				$data = $_POST;

			if (!$bypass) {
				$this->setName($data->name);
				$this->setDescriptor($data->descriptor);
				$this->setType($data->type);
				$this->setFocus($data->focus);
				$this->setTier($data->tier);
				$this->setEffort($data->effort);
				$this->setXP($data->xp);
				foreach ($data->attributes as $stat => $values) {
					$this->setAttribute($stat, 'pool.current', $values->pool->current);
					$this->setAttribute($stat, 'pool.max', $values->pool->max);
					$this->setAttribute($stat, 'edge', $values->edge);
				}
				$this->setDamage('impaired', $data->damage->impaired);
				$this->setDamage('debilitated', $data->damage->debilitated);
				$this->setRecovery($data->recovery);
				foreach (['action', 'ten_min', 'hour', 'ten_hours'] as $slug)
					$this->setRecoveryTimes($slug, $data->recoveryTimes->$slug);
				$this->setArmor($data->armor);

				$this->clearVar('attacks');
				if ($data->attacks && sizeof($data->attacks))
					foreach ($data->attacks as $attack)
						$this->addAttack($attack);

				$this->clearVar('skills');
				if ($data->skills && sizeof($data->skills))
					foreach ($data->skills as $skill) $this->addSkill($skill);

				$this->clearVar('specialAbilities');
				if ($data->specialAbilities && sizeof($data->specialAbilities))
					foreach ($data->specialAbilities as $specialAbility) $this->addSpecialAbility($specialAbility);

				$this->clearVar('cyphers');
				if ($data->cyphers && sizeof($data->cyphers))
					foreach ($data->cyphers as $cypher) $this->addCypher($cypher);

				$this->setPossessions($data->possessions);
				$this->setNotes($data->notes);
			}

			parent::save();
		}
	}
?>
