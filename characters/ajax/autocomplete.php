<?php
	if ($loggedIn) {
		$type = sanitizeString($_POST['type']);
		$searchName = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];
		$systemOnly = isset($_POST['systemOnly']) && $_POST['systemOnly'] ? true : false;

		if ($systems->verifySystem($system)) {
			$search = array('searchName' => new MongoDB\BSON\Regex("/{$searchName}/"));
			if ($systemOnly) {
				$search['systems'] = $system;
				$rCIL = $mongo->charAutocomplete->find(
					$search,
					[
						'sort' => ['searchName' => true],
						'limit' => 5
					]
				);
			} else {
				$rCIL = $mongo->charAutocomplete->aggregate([
					['$match' => [
						'searchName' => $search['searchName'],
						'type' => $type
					]],
					['$project' => [
						'name' => true,
						'inSystem' => [
							'$setIsSubset' => [
								[$system],
								'$systems'
							]
						]
					]],
					['$sort' => [
						'inSystem' => -1,
						'name' => 1
					]],
					['$limit' => 5]
				]);
			}
			$lastType = null;
			foreach ($rCIL['result'] as $item) {
				$classes = [];
				if (!$item['systemItem']) {
					$classes[] = 'nonSystemItem';
				}
				if ($item['systemItem'] != $lastType && $lastType != null) {
					$classes[] = 'lineAbove';
				}
				$lastType = $item['systemItem'];
				echo "<a href=\"\"" . (sizeof($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . ">{$item['name']}</a>\n";
			}
		}
	}
?>
