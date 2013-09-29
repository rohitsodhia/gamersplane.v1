<?
	if (intval($_POST['mapID']) && (isset($_POST['row']) || isset($_POST['column'])) {
		$mapID = intval($_POST['mapID']);
		$mapUpdate = $mysql->query('UPDATE maps SET '.(isset($_POST['row'])?'rows':'columns').' = '.(isset($_POST['row'])?'rows':'columns').' + 1 WHERE mapID = '.$mapID);
		
		header('Content-Type: text/xml');
		echo "<?xml version=\"1.0\" ?>\n\n";
		if ($mapUpdate->rowCount()) echo '<status>Success</status>';
		else echo '<status>Failed</status>';
	}
?>