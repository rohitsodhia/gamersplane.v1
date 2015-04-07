<?
	class pms {
		function __construct() {
			global $loggedIn, $pathOptions;
			if (!$loggedIn) exit;

//			if ($pathOptions[0] == 'list' && in_array($_POST['box'], array('inbox', 'outbox'))) 
//				$this->displayBox($_POST['box']);
			$this->recentGames();
		}

		public function recentGames() {
			global $mysql, $currentUser;

			xmlHeaders();
?>
<rss version="2.0">
	<channel>
		<title>Games</title>
	</channel>
</rss>
<?
		}
	}
?>