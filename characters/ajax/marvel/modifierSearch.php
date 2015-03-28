<?
	$search = sanitizeString($_POST['search'], 'search_format');
	$characterID = intval($_POST['characterID']);
	
	$modifiers = $mysql->prepare("SELECT ml.modifierID, ml.name FROM marvel_modifiersList ml LEFT JOIN marvel_modifiers cm ON ml.modifierID = cm.modifierID AND cm.characterID = $characterID WHERE ml.name LIKE ? AND cm.modifierID IS NULL ORDER BY ml.name LIMIT 5");
	$modifiers->execute(array("%$search%"));
	foreach ($modifiers as $info) 
		echo "<a id=\"newModifier_{$info['modifierID']}\" href=\"\">{$info['name']}</a>\n";
?>