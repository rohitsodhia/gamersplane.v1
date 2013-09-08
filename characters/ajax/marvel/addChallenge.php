<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', $_POST['challengeName']));
			$stones = intval($_POST['stones']);
			$addChallenge = $mysql->query("INSERT INTO marvel_challenges (characterID, challenge, stones) VALUES ($characterID, '$name', $stones)");
			if ($addChallenge->getResult()) {
				$challengeID = $mysql->lastInsertId();
?>
				<div class="challenge clearfix">
					<span class="challengeName"><?=$name?></span>
					<input type="text" name="challenge[<?=$challengeID?>]" value="<?=$stones?>" class="challengeStones">
				</div>
<?
			}
		}
	}
?>