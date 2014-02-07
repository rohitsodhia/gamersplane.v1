<?
	class RollFactory {
		private static $systemMap = array('basic' => 'Basic', 'sweote' => 'SWEOTE');

		private function __construct() { }

		public static function getRoll($type) {
			if (class_exists($type.'Roll')) $classname = $type.'Roll';
			elseif (class_exists(self::$systemMap[$type].'Roll')) $classname = $this->systemMap[$type].'Roll';
			else throw new Exception('Invalid type');
			return new $classname();
		}
	}
?>