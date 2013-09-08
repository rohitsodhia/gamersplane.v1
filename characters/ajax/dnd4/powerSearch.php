<?
	if (checkLogin(0)) {
		$search = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['search'])));
		$characterID = intval($_POST['characterID']);
		
		$powers = $mysql->query("SELECT powers.name FROM (SELECT name FROM dnd4_powers GROUP BY name) powers LEFT JOIN (SELECT characterID, name FROM dnd4_powers WHERE characterID = $characterID) charPowers  USING (name) WHERE powers.name LIKE '%$search%' AND charPowers.characterID IS NULL ORDER BY powers.name LIMIT 5");
		foreach ($powers as $info) {
			echo "<a href=\"\">".mb_convert_case($info['name'], MB_CASE_TITLE)."</a>\n";
		}
	}
?>