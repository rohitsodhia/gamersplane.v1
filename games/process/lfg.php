<?
	checkLogin(1);
	
	$userID = intval($_SESSION['userID']);
	if (isset($_POST['update'])) {
		$mysql->query("DELETE FROM lfg WHERE userID = $userID");
		$lfgInsert = $mysql->prepare('INSERT INTO lfg SET userID = :userID, systemID = :systemID');
		$lfgInsert->bindValue(':userID', $userID);
		$lfgInsert->bindParam(':systemID', $systemID);
		foreach ($_POST['lfg'] as $systemID => $junk) $lfgInsert->execute();
		
		if (isset($_POST['modal'])) echo 1;
		else header('Location: /games/lfg');
	} else {
		if (isset($_POST['modal'])) echo 0;
		else header('Location: /games/');
	}
?>