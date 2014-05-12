<?
	class customCharacter extends Character {
		const SYSTEM = 'custom';

		public function save() {
			$this->notes = $_POST['charSheet'];

			parent::save();
		}
	}
?>