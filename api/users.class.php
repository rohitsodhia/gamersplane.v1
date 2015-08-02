<?
	class users {
		const USERS_PER_PAGE = 25;

		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'gamersList') 
				$this->gamersList();
			elseif ($pathOptions[0] == 'search') 
				$this->search();
			elseif ($pathOptions[0] == 'getCurrentUser') 
				$this->getCurrentUser();
			else 
				displayJSON(array('failed' => true));
		}

		public function gamersList() {
			global $mysql;

			$page = isset($_POST['page']) && intval($_POST['page']) > 0?intval($_POST['page']):1;
			$total = $mysql->query("SELECT COUNT(userID) FROM users WHERE activatedOn IS NOT NULL".(!isset($_POST['showInactive']) || !$_POST['showInactive']?' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK':''))->fetchColumn();
			$rUsers = $mysql->query('SELECT userID, username, lastActivity, IF(lastActivity >= UTC_TIMESTAMP() - INTERVAL 15 MINUTE, 1, 0) online, joinDate FROM users WHERE activatedOn IS NOT NULL'.(!isset($_POST['showInactive']) || !$_POST['showInactive']?' AND lastActivity >= UTC_TIMESTAMP() - INTERVAL 2 WEEK':'').' ORDER BY online DESC, username LIMIT '.(($page - 1) * self::USERS_PER_PAGE).', '.self::USERS_PER_PAGE);
			$users = array();
			if (sizeof($rUsers)) {
				foreach ($rUsers as $user) {
					$user['userID'] = (int) $user['userID'];
					$user['online'] = (bool) $user['online'];
					$user['avatar'] = User::getAvatar($user['userID']);
					$user['inactive'] = User::inactive($user['lastActivity']);
					unset($user['lastActivity']);
					$users[] = $user;
				}
				displayJSON(array('users' => $users, 'totalUsers' => (int) $total));
			} else 
				displayJSON(array('noUsers' => true));
		}

		public function search() {
			global $mysql, $currentUser;

			$search = sanitizeString(preg_replace('/[^\w.]/', '', $_GET['search']));
			$searchBy = isset($_GET['searchBy']) && in_array($_GET['searchBy'], array('username', 'userID'))?$_GET['searchBy']:'username';
			if (isset($_GET['exact']) && (bool) $_GET['exact'] == true) {
				if ($searchBy == 'userID') {
					$search = intval($search);
					$user = $mysql->query("SELECT userID, username, email FROM users WHERE userID = {$search}")->fetch();
				} else 
					$user = $mysql->query("SELECT userID, username, email FROM users WHERE username = '{$search}'")->fetch();

				if ($user) 
					displayJSON(array('users' => array($user)));
				else 
					displayJSON(array('noUsers' => true));
			} else {
//				$valid = $mysql->query("SELECT userID, username, email FROM users WHERE username LIKE '%{$search}%'");
			}
		}

		public function getCurrentUser() {
			global $loggedIn, $currentUser;

			if (!$loggedIn) 
				displayJSON(array('failed' => true, 'loggedOut' => true));
			else {
				$cleanUser = array(
					'userID' => $currentUser->userID,
					'username' => $currentUser->username,
					'email' => $currentUser->email,
					'joinDate' => $currentUser->joinDate,
					'activatedOn' => $currentUser->activatedOn,
					'timezone' => $currentUser->timezone,
					'usermeta' => $currentUser->usermeta,
					'acpPermissions' => $currentUser->acpPermissions
				);
				displayJSON($cleanUser);
			}
		}
	}
?>