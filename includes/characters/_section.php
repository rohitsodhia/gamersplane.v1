<?php
	function newItemized($type, $name, $system) {
		global $currentUser, $systems;

		if ($system == 'custom') {
			return false;
		}
		if ($systems->verifySystem($system)) {
			return false;
		}

		$searchName = sanitizeString($name, 'search_format');
		$ac = $mongo->charAutocomplete->findOne(['searchName' => $searchName], ['projection' => ['_id' => true]]);
		$uai = array(
			'name' => $name,
			'itemID' => null,
			'action' => null,
			'system' => $system,
			'type' => $type,
			'addedBy' => [
				'userID' => (int) $currentUser->userID,
				'username' => $currentUser->username,
				'on' => genMongoDate()
			],
			'actedBy' => [
				'userID' => null,
				'username' => null,
				'on' => null
			]
		);
		if ($ac != null) {
			$uai['itemID'] = $ac['_id']->{$id};
			$mongo->userAddedItems->insertOne($uai);
		} else {
			$mongo->userAddedItems->insertOne($uai);
		}

		return true;
	}
?>
