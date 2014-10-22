<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('faqs', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	$formErrors->clearErrors();	

	$_id = new MongoId($_POST['mongoID']);
	if ($_id && strlen($_POST['question']) && strlen($_POST['answer'])) {
		$mongo->faqs->update(array('_id' => $_id), array('$set' => array('question' => htmlspecialchars_decode($_POST['question']), 'answer' => $_POST['answer'])));
		require_once(FILEROOT.'/javascript/markItUp/markitup.bbcode-parser.php');
		echo BBCode2Html(printReady($_POST['answer']));
	}
?>