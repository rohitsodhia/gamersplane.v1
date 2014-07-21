<?
	class customCharacter extends Character {
		const SYSTEM = 'custom';

		public function save() {
			if (!isset($_POST['create'])) $this->notes = $_POST['charSheet'];

			parent::save();
		}
	}
?>