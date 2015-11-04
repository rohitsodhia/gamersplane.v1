<?
	class contact {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'send') 
				$this->send();
			else 
				displayJSON(array('failed' => true));
		}

		public function send() {
			global $mongo, $loggedIn, $currentUser;

			$inserts['name'] = $_POST['name'];
			$inserts['username'] = $loggedIn?$currentUser->username:$_POST['username'];
			$inserts['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)?$_POST['email']:'';
			$inserts['subject'] = $_POST['subject'];
			$inserts['comment'] = $_POST['comment'];

			$errors = array();
			$nonBlankFields = array('name', 'email', 'subject', 'comment');
			foreach ($nonBlankFields as $field) 
				if (!strlen($inserts[$field])) 
					$errors['empty'][] = $field;

			if (sizeof($errors)) 
				displayJSON(array('failed' => true, 'errors' => $errors));
			else {
				$inserts['date'] = new MongoDate();
				$mongo->contact->insert($inserts);
				
				$message = '';
				foreach ($inserts as $key => $value) 
					$message .= ucfirst($key).":\n".printReady($value)."\n\n";

				@mail('contact@gamersplane.com', 'Gamers Plane Contact: '.printReady($inserts['subject']), $message, 'From: '.$inserts['email']);

				displayJSON(array('success' => true));
			}
		}
	}
?>