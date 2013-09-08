<?
	checkLogin(0);
	
	if (isset($_POST['submit'])) {
		unset($_SESSION['errors']);
		unset($_SESSION['errorVals']);
		unset($_SESSION['errorTime']);
		
		$inserts['name'] = sanatizeString($_POST['name']);
		$inserts['date'] = date('Y-m-d H:i:s');
		$inserts['username'] = sanatizeString($_POST['username']);
		$inserts['email'] = sanatizeString($_POST['email']);
		$inserts['browser'] = sanatizeString($_POST['browser']);
		$inserts['javascript'] = $_POST['javascript']?1:0;
		$inserts['subject'] = sanatizeString($_POST['subject']);
		$inserts['comment'] = sanatizeString($_POST['comment']);
		
		$nonBlankFields = array('name', 'email', 'subject', 'comment');
		foreach ($inserts as $field => $value) { if ((in_array($field, $nonBlankFields) && $value == '') || ($field == 'browser' && $value == 'Select One')) {
			$_SESSION['errors'][$field] = 1;
		} }
		
		if (sizeof($_SESSION['errors'])) {
			$_SESSION['errorVals'] = $inserts;
			$_SESSION['errorTime'] = time() + 300;
			header('Location: '.SITEROOT.'/contact/failed');
		} else {
			$addContact = $mysql->prepare('INSERT INTO contact SET name = :name, date = :date, username = :username, email = :email, browser = :browser, javascript: javascript, subject = :subject, comment = :comment');
			$addContact->execute(array($inserts['name'], $inserts['date'], $inserts['username'], $inserts['email'], $inserts['browser'], $inserts['javascript'], $inserts['subject'], $inserts['comment']));
			
			$message = '';
			foreach ($inserts as $key => $value) { $message .= ucfirst($key).":\n".printReady($value)."\n\n"; }
			
			mail('contact@gamersplane.com', 'Gamers Plane Contact: '.printReady($inserts['subject']), $message, 'From: '.$_POST['email']);
			
			unset($_SESSION['errors']);
			unset($_SESSION['errorVals']);

			header('Location: '.SITEROOT.'/contact/success');
		}
	} else { header('Location: '.SITEROOT.'/contact'); }
?>