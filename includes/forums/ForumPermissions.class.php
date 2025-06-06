<?
	class ForumPermissions {
		protected $permissions;

		public static function getPermissions($userID, $forumIDs, $types = null, $forumsData = null) {
			global $mysql;

			if(is_array($forumIDs) && !sizeof($forumIDs)){
				return null;  //forum does not exit
			}

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
				$aTemplate[$type] = 4;
			}
			$queryColumn['permissions'] = substr($queryColumn['permissions'], 0, -2);
			$queryColumn['permissionSums'] = substr($queryColumn['permissionSums'], 0, -2);

			$allForumIDs = $forumIDs;
			$heritages = array();
			if (is_array($forumsData) && sizeof($forumsData)) {
				$count = 0;
				foreach ($allForumIDs as $forumID) {
					$heritages[$forumID] = [$forumID];
					$parentID = $forumsData[$forumID]['parentID'];
					while ($parentID != NULL) {
						array_unshift($heritages[$forumID], (int) $parentID);
						$parentID = $forumsData[$parentID]['parentID'];
					}
					$allForumIDs = array_merge($allForumIDs, $heritages[$forumID]);
				}
			} else {
				$forumIDsStr = implode(', ', $allForumIDs);
				$forumInfos = $mysql->query(
					"WITH RECURSIVE forum_with_parents (forumID, parentID) AS (
						SELECT
							forumID, parentID
						FROM
							forums
						WHERE
							forumID IN ($forumIDsStr)
						UNION
						SELECT
							f.forumID, f.parentID
						FROM
							forums f
						INNER JOIN forum_with_parents p ON f.forumID = p.parentID
					) SELECT * from forum_with_parents ORDER BY depth"
				);
				while (list($forumID, $parentID) = $forumInfos->fetch(PDO::FETCH_NUM)) {
					$heritages[$forumID] = [];
					if (array_key_exists($parentID, $heritages)) {
						$heritages[$forumID] = array_merge($heritages[$parentID], [$forumID]);
					} else {
						$heritages[$forumID] = [$parentID];
					}
					$allForumIDs = array_merge($allForumIDs, $heritages[$forumID]);
				}
			}
			$allForumIDs = array_unique($allForumIDs);
			sort($allForumIDs);

			if ($userID) {
				$adminForums = array();
				$adminIn = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = {$userID} AND forumID IN (".implode(', ', $allForumIDs).')');
				$adminForums = $adminIn->fetchAll(PDO::FETCH_COLUMN);
				array_walk($adminForums, function (&$value, $key) { $value = intval($value); });
				$getPermissionsFor = array();
				$superFAdmin = array_search(0, $adminForums) !== false?true:false;
				foreach ($forumIDs as $forumID) {
					if (sizeof(array_intersect($heritages[$forumID], $adminForums)) || $superFAdmin) $permissions[$forumID] = array_merge($aTemplate, array('admin' => 4));
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
				$permissionsInfos = $mysql->query(
					"SELECT forumID, 'general' pType, {$queryColumn['permissions']}
					FROM forums_permissions_general
					WHERE forumID {$forumString}
					UNION
					SELECT forumID, 'group' pType, {$queryColumn['permissions']}
					FROM forums_permissions_groups_c WHERE userID = {$userID} AND forumID {$forumString}
					UNION
					SELECT forumID, 'user' pType, {$queryColumn['permissions']}
					FROM forums_permissions_users
					WHERE userID = {$userID} AND forumID {$forumString}
				");
				$groupPermissionsDenied = $mysql->query("SELECT forumID FROM forums_permissions_groups WHERE `read`=-2 AND forumID {$forumString}")->fetchAll(PDO::FETCH_COLUMN);
				$rawPermissions = array();
				foreach ($permissionsInfos->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC) as $key => $rawPermission) {
					if (sizeof($rawPermission) == 1) {
						unset($rawPermission[0]['pType']);
						$rawPermissions[$key] = $rawPermission[0];
					} else {
						$rawPermissions[$key] = $bTemplate;
						foreach ($rawPermission as $sKey => $indivPermission) {
							foreach ($indivPermission as $permission => $setAt) {
								$setAt = (int) $setAt;
								if ($permission != 'pType' && abs($setAt) > abs($rawPermissions[$key][$permission]))
									$rawPermissions[$key][$permission] = $setAt;
							}
						}
					}
				}

				foreach ($forumIDs as $forumID) {
					if (isset($rawPermissions[$forumID]))
						$permissions[$forumID] = $rawPermissions[$forumID];
					elseif (!isset($permissions[$forumID]))
						$permissions[$forumID] = $bTemplate;
					foreach (array_reverse($heritages[$forumID]) as $heritage) {
						if ($heritage == $forumID) continue;
						if (isset($rawPermissions[$heritage]))
							foreach ($types as $type)
								if (abs($rawPermissions[$heritage][$type]) > abs($permissions[$forumID][$type]))
								if($type!='read' || !in_array(strval($forumID),$groupPermissionsDenied)){
									$permissions[$forumID][$type] = $rawPermissions[$heritage][$type];
								}
								else{
									//group is denied read by default (browsing private subforums in public games when unauthenticated)
									$permissions[$forumID][$type] = -2;
								}
					}
				}
			}

			global $loggedIn;
			foreach ($forumIDs as $forumID) {
				foreach ($permissions[$forumID] as $type => $value) {
					if ($value < 1 || (!$loggedIn && $type != 'read'))
						$permissions[$forumID][$type] = false;
					else
						$permissions[$forumID][$type] = true;
				}
				if (!isset($permissions[$forumID]['admin']) || $permissions[$forumID]['admin'] != true)
					$permissions[$forumID]['admin'] = false;
			}

			return $permissions;
		}
	}
?>
