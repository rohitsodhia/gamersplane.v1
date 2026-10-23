<?php
	function newItemized($type, $name, $system) {
		global $currentUser, $systems;

		if ($system == 'custom') {
			return false;
		}
		if ($systems->verifySystem($system)) {
			return false;
		}

		return true;
	}
?>
