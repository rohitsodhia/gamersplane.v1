<?
	if (isset($_POST['update'])) {
		$mysql->query("DELETE FROM lfg WHERE userID = {$currentUser->userID}");
		$lfgInsert = $mysql->prepare('INSERT INTO lfg SET userID = {$currentUser->userID}, systemID = :systemID');
		$lfgInsert->bindParam(':system', $systemID);
		foreach ($_POST['lfg'] as $system) 
			$lfgInsert->execute();
		
		if (isset($_POST['modal'])) 
			echo 1;
		else 
			header('Location: /games/lfg');
	} else {
		if (isset($_POST['modal'])) 
			echo 0;
		else 
			header('Location: /games/');
	}
?>