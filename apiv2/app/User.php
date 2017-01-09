<?php

namespace App;

use \Exception;

use Illuminate\Support\Facades\DB;

class User
{
	protected $userID;
	protected $username;
	protected $password;
	protected $salt;
	protected $email;
	protected $joinDate;
	protected $activatedOn;
	protected $lastActivity;
	protected $timezone;
	protected $usermeta = [];
	protected $acpPermissions = null;

	protected $hiddenVars = ['password', 'salt'];

	public function __construct($userDetail = null) {
		if (
			$userDetail == null ||
			(
				!isset($userDetail['email']) &&
				!isset($userDetail['username']) &&
				!isset($userDetail['userID'])
			)
		) {
			return false;
		}

		$userInfo = DB::connection('mysql')->table('users')->select('userID', 'username', 'password', 'salt', 'email', 'joinDate', 'activatedOn', 'lastActivity');
		if (isset($userDetail['email'])) {
			$userInfo->where('email', $userDetail);
		} elseif (isset($userDetail['username'])) {
			$userInfo->where('username', $userDetail);
		} elseif (isset($userDetail['userID'])) {
			$userInfo->where('userID', $userDetail);
		}
		$userInfo = $userInfo->limit(1)->first();
		if ($userInfo) {
			foreach ($userInfo as $key => $value) {
				$this->$key = $value;
			}
			$this->userID = (int) $this->userID;

			$usermeta = DB::connection('mysql')->table('usermeta')->select('metaKey', 'metaValue')->where('userID', $this->userID)->where('autoload', 1)->get();
			foreach ($usermeta as $eMeta) {
				$this->usermeta[$eMeta->metaKey] = $eMeta->metaValue;
			}
			$this->acpPermissions = DB::connection('mongo')->collection('users')->where('userID', $this->userID)->select('acpPermissions')->first()['acpPermissions'];
		} else {
			throw new Exception('No user found');
		}
	}

	public function getLoginHash() {
		return substr(hash('sha256', getenv('PVAR') . $this->email . $this->joinDate), 7, 32);
	}

	public function generateLoginCookie() {
		setcookie('loginHash', '', time() - 30, '/', getenv('APP_COOKIE_DOMAIN'));
		setcookie('loginHash', $this->username . '|' . $this->getLoginHash(), time() + (60 * 60 * 24 * 7), '/', getenv('APP_COOKIE_DOMAIN'));
	}

	public static function logout($resetSession = false) {
		if ($resetSession) {
			session_unset();
			// unset($_COOKIE[session_name()]);

			session_regenerate_id(TRUE);
			session_destroy();
			setcookie(session_name(), '', time() - 30, '/');
			$_SESSION = [];
		}

		setcookie('loginHash', '', time() - 30, '/', getenv('APP_COOKIE_DOMAIN'));
		// session_destroy();
	}

	public function __get($var) {
		if (!in_array($var, $this->hiddenVars) && isset($this->$var)) {
			return $this->$var;
		} elseif (array_key_exists($var, $this->usermeta)) {
			return $this->usermeta[$var];
		} elseif ($var == 'userID') {
			return 0;
		} else {
			return null;
		}
	}
}
