<?
	class ForumPermissions {
		protected $permissions;

		public static function getPermissions($userID, $forumIDs, $types = null, $forumsData = null) {
			global $mysql;

			$userID = intval($userID);
			if (!is_array($forumIDs)) $forumIDs = array($forumIDs);
			$queryColumn = array('permissions' => '', 'permissionSums' => '', 'general' => '', 'group' => '');
			$allTypes = array('read', 'write', 'editPost', 'deletePost', 'createThread', 'deleteThread', 'addRolls', 'addDraws', 'moderate');
			if ($types == null) $types = $allTypes;
			elseif (is_string($types)) $types = preg_split('/\s*,\s*/', $types);

			foreach ($types as $type) {
				$queryColumn['permissions'] .= "`$type`, ";
				$queryColumn['permissionSums'] .= "SUM(`$type`) `$type`, ";
				$bTemplate[$type] = 0;
				$aTemplate[$type] = 1;
			}
			$queryColumn['permissions'] = substr($queryColumn['permissions'], 0, -2);
			$queryColumn['permissionSums'] = substr($queryColumn['permissionSums'], 0, -2);
			
			$allForumIDs = $forumIDs;
			$heritages = array();
			if (sizeof($forumsData)) {
				foreach ($allForumIDs as $forumID) {
					$heritages[$forumID] = explode('-', $forumsData[$forumID]['heritage']);
					array_walk($heritages[$forumID], function (&$value, $key) { $value = intval($value); });
					$allForumIDs = array_merge($allForumIDs, $heritages[$forumID]);
				}
			} else {
				$forumInfos = $mysql->query('SELECT forumID, heritage FROM forums WHERE forumID IN ('.implode(', ', $allForumIDs).')');
				while (list($forumID, $heritage) = $forumInfos->fetch(PDO::FETCH_NUM)) {
					$heritages[$forumID] = explode('-', $heritage);
					array_walk($heritages[$forumID], function (&$value, $key) { $value = intval($value); });
					$allForumIDs = array_merge($allForumIDs, $heritages[$forumID]);
				}
			}
			$allForumIDs = array_unique($allForumIDs);
			sort($allForumIDs);

			if ($userID) {
				$adminForums = array();
				$adminIn = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $userID AND forumID IN (0, ".implode(', ', $allForumIDs).')');
				$adminForums = $adminIn->fetchAll(PDO::FETCH_COLUMN);
				array_walk($adminForums, function (&$value, $key) { $value = intval($value); });
				$getPermissionsFor = array();
				$superFAdmin = array_search(0, $adminForums) !== false?true:false;
				foreach ($forumIDs as $forumID) {
					if (sizeof(array_intersect($heritages[$forumID], $adminForums)) || $superFAdmin) $permissions[$forumID] = array_merge($aTemplate, array('admin' => 1));
					else $getPermissionsFor[] = $forumID;
				}
				foreach ($getPermissionsFor as $forumID) 
					$getPermissionsFor = array_merge($getPermissionsFor, $heritages[$forumID]);
				$getPermissionsFor = array_unique($getPermissionsFor);
				sort($getPermissionsFor);
			} else 
				$getPermissionsFor = $allForumIDs;

			if (sizeof($getPermissionsFor)) {
				if (sizeof($getPermissionsFor) == 1) 
					$forumString = '= '.$getPermissionsFor[0];
				else {
					$forumString = implode(', ', $getPermissionsFor);
					$forumString = 'IN ('.$forumString.')';
				}
				$permissionsInfos = $mysql->query("SELECT forumID, 'general' pType, {$queryColumn['permissions']} FROM forums_permissions_general WHERE forumID {$forumString} UNION SELECT forumID, 'group' pType, {$queryColumn['permissions']} FROM forums_permissions_groups_c WHERE userID = {$userID} AND forumID {$forumString} UNION SELECT forumID, 'user' pType, {$queryColumn['permissions']} FROM forums_permissions_users WHERE userID = {$userID} AND forumID {$forumString}");
				$rawPermissions = array();
				foreach ($permissionsInfos->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC) as $key => $rawPermission) {
					if (sizeof($rawPermission) == 1) {
						unset($rawPermission[0]['pType']);
						$rawPermissions[$key] = $rawPermission[0];
					} else {
						$toStore = 0;
						foreach ($rawPermission as $sKey => $indivPermission) {
							if ($indivPermission['pType'] == 'user') {
								$toStore = $sKey;
								break;
							} elseif ($indivPermission['pType'] == 'group') 
								$toStore = $sKey;
						}
						unset($rawPermission[$toStore]['pType']);
						$rawPermissions[$key] = $rawPermission[$toStore];
					}
				}

				foreach ($forumIDs as $forumID) {
					if (isset($rawPermissions[$forumID])) 
						$permissions[$forumID] = $rawPermissions[$forumID];
					else 
						$permissions[$forumID] = $bTemplate;
					foreach (array_reverse($heritages[$forumID]) as $heritage) {
						if ($heritage == $forumID) continue;
						if (isset($rawPermissions[$heritage])) 
							foreach ($types as $type) 
								if (abs($rawPermissions[$heritage][$type]) > abs($permissions[$forumID][$type])) 
									$permissions[$forumID][$type] = $rawPermissions[$heritage][$type];
					}
				}
			}
			global $loggedIn;
			foreach ($forumIDs as $forumID) {
				foreach ($permissions[$forumID] as $type => $value) {
					if ($value < 1 || (!$loggedIn && $type != 'read')) $permissions[$forumID][$type] = false;
					else $permissions[$forumID][$type] = true;
				}
				if (!isset($permissions[$forumID]['admin']) || $permissions[$forumID]['admin'] != true) $permissions[$forumID]['admin'] = false;
			}

			return $permissions;
		}
	}
?>