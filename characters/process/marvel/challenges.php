<?
	checkLogin();
	
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	if (isset($_POST['save'])) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$stoneInfo = $mysql->query('SELECT unusedStones, totalStones FROM marvel_characters WHERE characterID = '.$characterID);
		list($unusedStones, $totalStones) = $stoneInfo->fetch();
		
		foreach ($_POST as $key => $value) {
			$keyParts = explode('_', $key);
			$challengeID = intval($keyParts[1]);
			
			if (($value > 0 || $value == 'on') && $keyParts[0] == 'challengeID' && $challengeID && $keyParts[2] != 'added') {
				$stones = intval($_POST['challengeStones_'.$challengeID]);
				
				$mysql->query('INSERT INTO marvel_playerChallenges '.setupInserts(array('characterID' => $characterID, 'challengeID' => $challengeID, 'stones' => $stones));
				
				$unusedStones += $stones;
			} elseif ($keyParts[0] == 'challengeStones' && $challengeID && $keyParts[2] == 'added' && !isset($_POST['challengeID_'.$challengeID.'_added'])) {
				$stones = $mysql->query('SELECT stones FROM marvel_playerChallenges WHERE characterID = '.$characterID.' AND challengeID = '.$challengeID);
				$stones = $stones->fetchColumn();
				$mysql->query('DELETE FROM marvel_playerChallenges WHERE characterID = '.$characterID.' AND challengeID = '.$challengeID);
				
				$unusedStones -= $stones;
			}
			$mysql->query('UPDATE marvel_characters SET unusedStones = '.$unusedStones.' WHERE characterID = '.$characterID);
		}
		$mysql->query("INSERT INTO characterHistory (characterID, enactedBy, enactedOn, action) VALUES ($characterID, $userID, NOW(), 'editedChar')");;

		header('Location: '.SITEROOT.'/characters/marvel/sheet/'.$characterID);
	} else { header('Location: '.SITEROOT.'/403'); }
?>