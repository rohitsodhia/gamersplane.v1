<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['actionName'])));
			$actionID = $mysql->query('SELECT actionID FROM marvel_actionsList WHERE name = "'.$name.'"');
			if ($actionID->rowCount()) $actionID = $actionID->fetchColumn();
			else {
				$mysql->query('INSERT INTO marvel_actionsList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$actionID = $mysql->lastInsertId();
			}
			$addAction = $mysql->query("INSERT INTO marvel_actions (characterID, actionID) VALUES ($characterID, $actionID)");
			$numActions = intval($_POST['numActions']) + 1;
			if ($addAction->getResult()) {
?>
				<div class="action<?=$numActions % 3 == 0?' third':''?>">
					<div class="tr labelTR">
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="actionName"><?=mb_convert_case($name, MB_CASE_TITLE)?></span>
						<input type="text" name="action[<?=$actionID?>][cost]" value="0" class="cost">
						<input type="text" name="action[<?=$actionID?>][level]" value="0" class="level">
					</div>
					<textarea name="action[<?=$actionID?>][details]"></textarea>
				</div>
<?
			}
		}
	}
?>