<?
	class FormErrors {
		private static $instance;
		private $errorTimer = 300;
		protected $errors = array();
		protected $errorChecked = false;

		private function __construct() {
			if (isset($_SESSION['errors']['errorTimer']) < time()) $this->clearErrors();
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

		public function setErrors($for, $errorVals = null) {
			if (strlen($for) && sizeof($this->errors)) {
				unset($_SESSION['errors'][$for]);
				$_SESSION['errors']['for'] = $for;
				$_SESSION['errors']['errorCodes'] = $this->errors;
				$_SESSION['errors']['errorTimer'] = time() + $this->errorTimer;
				if ($errorVals != null) $_SESSION['errors']['errorVals'] = $errorVals;
			}
		}

		public function clearErrors($check = false) {
			if ((($check && !$this->errorChecked) || !$check) && isset($_SESSION['errors'])) unset($_SESSION['errors']);
		}

		public function getErrors($for) {
			if (isset($_SESSION['errors']['for']) && $_SESSION['errors']['for'] == $for && time() <= $_SESSION['errors']['errorTimer']) {
				$this->errors = $_SESSION['errors']['errorCodes'];
				$this->errorChecked = true;
				if (isset($_SESSION['errors']['errorVals'])) 
					return $_SESSION['errors']['errorVals'];
				else return true;
			} else return false;
		}

		public function checkError($error) {
			return in_array($error, $this->errors);
		}
	}
?>