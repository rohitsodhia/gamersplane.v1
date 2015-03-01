<?
	class customCharacter extends Character {
		const SYSTEM = 'custom';

		public function save() {
			if (!isset($_POST['create'])) $this->notes = sanitizeString($_POST['charSheet']);

			parent::save();
		}
	}
?>