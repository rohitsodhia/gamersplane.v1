<?php
	if ($loggedIn) {
		$searchName = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];

		if ($systems->verifySystem($system)) {
			$skills = $mongo->charAutocomplete->aggregate([
				['$match' => [
					'searchName' => $searchName
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
			$lastType = null;
			foreach ($skills as $info) {
				$classes = [];
				if (!$info['systemSkill']) {
					$classes[] = 'nonSystemSkill';
				}
				if ($info['systemSkill'] != $lastType && $lastType != null) {
					$classes[] = 'lineAbove';
				}
				$lastType = $info['systemSkill'];
				echo "<a href=\"\"" . (sizeof($classes) ? ' class="' . implode(' ', $classes) . '"' : '') . ">{$info['name']}</a>\n";
			}
		}
	}
?>
