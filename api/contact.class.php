<?php
	class contact {
		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'send') {
				$this->send();
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function send() {
			global $loggedIn, $currentUser;

			// Enforce the same limits as the form's inputs on the server, since bots post to the API directly.
			$clip = function ($value, $max) {
				return is_scalar($value) ? mb_substr((string) $value, 0, $max) : '';
			};
			$email = $clip($_POST['email'] ?? '', 100);

			$inserts['name'] = $clip($_POST['name'] ?? '', 50);
			$inserts['username'] = $loggedIn ? $currentUser->username : $clip($_POST['username'] ?? '', 50);
			$inserts['email'] = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : '';
			$inserts['subject'] = $clip($_POST['subject'] ?? '', 100);
			$inserts['comment'] = $clip($_POST['comment'] ?? '', 5000);

			$errors = [];
			$nonBlankFields = ['name', 'email', 'subject', 'comment'];
			foreach ($nonBlankFields as $field) {
				if (!strlen($inserts[$field])) {
					$errors['empty'][] = $field;
				}
			}

			// Logged-in users skip the captcha. Only check it once the fields are valid, since a token is single-use.
			if (!sizeof($errors) && !$loggedIn && !verifyRecaptcha($_POST['captcha'] ?? '')) {
				$errors['captcha'] = true;
			}

			if (sizeof($errors)) {
				$errors += ['empty' => []];
				displayJSON(['failed' => true, 'errors' => $errors]);
			} else {
				$message = '';
				foreach ($inserts as $key => $value) {
					$message .= ucfirst($key) . ":\n" . printReady($value) . "\n\n";
				}

				$mail = getMailObj();
				$mail->addAddress("contact@gamersplane.com");
				$mail->Subject = 'Gamers Plane Contact: ' . printReady($inserts['subject']);
				$mail->Body = $message;
				// Send from our own address (a visitor's address fails SPF/DMARC from this server); replies go to them.
				$mail->addReplyTo($inserts['email'], $inserts['name']);
				$mail->send();

				displayJSON(['success' => true]);
			}
		}
	}
?>
