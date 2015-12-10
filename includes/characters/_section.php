<?
	function newItemized($type, $name, $system) {
		global $currentUser, $systems;

		if ($system == 'custom') 
			return false;
		if ($systems->verifySystem($system)) 
			return false;

		$searchName = sanitizeString($name, 'search_format');
		$ac = $mongo->charAutocomplete->findOne(array('searchName' => $searchName), array('_id' => true));
		$uai = array(
			'name' => $name,
			'itemID' => null,
			'action' => null,
			'system' => $system,
			'type' => $type,
			'addedBy' => array(
				'userID' => (int) $currentUser->userID,
				'username' => $currentUser->username,
				'on' => new MongoDate()
			),
			'actedBy' => array(
				'userID' => null,
				'username' => null,
				'on' => null
			)
		);
		if ($ac != null) {
			$uai['itemID'] = $ac['_id']->{$id};
			$mongo->userAddedItems->insert($uai);
		} else 
			$mongo->userAddedItems->insert($uai);

		return true;
	}
?>