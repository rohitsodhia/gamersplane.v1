<?
	checkLogin();
	
	$userID = intval($_SESSION['userID']);
	
	if (isset($_POST['submit'])) {
		if (in_array($_POST['postSide'], array('l', 'r', 'c'))) $postSide = $_POST['postSide'];
		else $postSide = 'l';
		
		$mysql->query("UPDATE users SET postSide = '$postSide' WHERE userID = $userID");
		
		header('Location: /ucp/cp/?updated=1');
	} else header('Location: /user');
?>