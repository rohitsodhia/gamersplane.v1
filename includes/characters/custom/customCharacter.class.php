<?
	class customCharacter extends Character {
		const SYSTEM = 'custom';

		public function save($bypass = false) {
			if (isset($_POST['character'])) {
				$data = $_POST['character'];
			} else {
				$data = $_POST;
			}

			if (!$bypass) {
				$this->setName($data->name);
				$this->setNotes($data->notes);
			}

			$this->saveCharacter();
		}

		public function saveCharacter(){
			parent::save();
		}
	}
?>
