<?
	if ($loggedIn) {
		$searchName = sanitizeString($_POST['search'], 'search_format');
		$characterID = intval($_POST['characterID']);
		$system = $_POST['system'];
		
		if ($systems->verifySystem($system)) {
			$skills = $mongo->charAutocomplete->aggregate(array(
				array(
					'$match' => array(
						'searchName' => $searchName
					)
				),
				array(
					'$project' => array(
						'name' => true,
						'inSystem' => array(
							'$setIsSubset' => array(
								array($system),
								'$systems'
							)
						)
					)
				),
				array(
					'$sort' => array(
						'inSystem' => -1,
						'name' => 1
					)
				),
				array(
					'$limit' => 5
				)
			));
			$lastType = null;
			foreach ($skills as $info) {
				$classes = array();
				if (!$info['systemSkill']) 
					$classes[] = 'nonSystemSkill';
				if ($info['systemSkill'] != $lastType && $lastType != null) 
					$classes[] = 'lineAbove';
				$lastType = $info['systemSkill'];
				echo "<a href=\"\"".(sizeof($classes)?' class="'.implode(' ', $classes).'"':'').">{$info['name']}</a>\n";
			}
		}
	}
?>