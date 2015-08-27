<?
	class customCharacter extends Character {
		const SYSTEM = 'custom';

		public function setName($name) {
			$this->name = sanitizeString($name);
		}

		public function getName() {
			return $this->name;
		}

		public function save($bypass = false) {
			$data = $_POST;

			if (!$bypass) {
				$this->setName($data['name']);
				$this->setNotes($_POST['charSheet']);
			}

			parent::save();
		}
	}
?>