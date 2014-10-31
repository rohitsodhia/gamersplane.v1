<?
	$gameID = intval($_POST['gameID']);
	$gmCheck = $mysql->query("SELECT primaryGM FROM players WHERE isGM = 1 AND gameID = $gameID AND userID = {$currentUser->userID}");
	if (isset($_POST['create']) && $gmCheck->rowCount()) {
		$addMap = $mysql->prepare("INSERT INTO maps SET gameID = $gameID, name = :name, rows = :rows, columns = :columns, visible = :visible");
		$addMap->bindValue(':name', sanitizeString($_POST['name']));
		$addMap->bindValue(':rows', sanitizeString($_POST['rows']));
		$addMap->bindValue(':columns', sanitizeString($_POST['columns']));
		$addMap->bindValue(':visible', isset($_POST['visible'])?1:0);
		$addMap->execute();

		echo 1;
	} elseif (isset($_POST['edit']) && $gmCheck->rowCount()) {
		$mapID = intval($_POST['mapID']);
		$addMap = $mysql->prepare("UPDATE maps SET name = :name, rows = :rows, columns = :columns, visible = :visible WHERE gameID = $gameID AND mapID = $mapID");
		$addMap->bindValue(':name', sanitizeString($_POST['name']));
		$addMap->bindValue(':rows', sanitizeString($_POST['rows']));
		$addMap->bindValue(':columns', sanitizeString($_POST['columns']));
		$addMap->bindValue(':visible', isset($_POST['visible'])?1:0);
		$addMap->execute();

		echo 1;
	} else echo 0;
?>