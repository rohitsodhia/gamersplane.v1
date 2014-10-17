<?
	class FormErrors {
		private static $instance;
		private $errorTimer = 300;
		protected $errors = array();
		protected $errorChecked = false;

		private function __construct() {
		}

		public static function getInstance() {
			if (empty(self::$instance)) self::$instance = new FormErrors();
			return self::$instance;
		}

		public function addError($type) {
			$this->errors[] = $type;
		}

		public function errorsExist() {
			if (sizeof($this->errors)) return true;
			else return false;
		}

		public function setErrors($for) {
			if (strlen($for) && sizeof($this->errors)) {
				unset($_SESSION['errors'][$for]);
				$_SESSION['errors']['for'] = $for;
				$_SESSION['errors']['errorCodes'] = $this->errors;
				$_SESSION['errors']['errorTimer'] = time() + $this->errorTimer;
			}
		}

		public function clearErrors($check = false) {
			if (($check && !$this->errorChecked) || !$check) unset($_SESSION['errors']);
		}

		public function getErrors($for) {
			if (isset($_SESSION['errors']['for']) && $_SESSION['errors']['for'] == $for && time() <= $_SESSION['errors']['errorTimer']) {
				$this->errors = $_SESSION['errors']['errorCodes'];
				$this->errorChecked = true;
				return true;
			} else return false;
		}

		public function checkError($error) {
			return in_array($error, $this->errors);
		}
	}
?>