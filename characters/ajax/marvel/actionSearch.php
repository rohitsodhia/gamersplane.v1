<?
	$search = sanitizeString($_POST['search'], 'search_format');
	$characterID = intval($_POST['characterID']);
	
	$actions = $mysql->prepare("SELECT al.actionID, al.name FROM marvel_actionsList al LEFT JOIN marvel_actions pa ON al.actionID = pa.actionID AND pa.characterID = $characterID WHERE al.searchName LIKE ? AND al.searchName != '$search' AND pa.actionID IS NULL LIMIT 5");
	$actions->execute(array("%$search%"));
	foreach ($actions as $info) 
		echo "<a id=\"newAction_{$info['actionID']}\" href=\"\">{$info['name']}</a>\n";
?>