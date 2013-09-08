<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$name = sanatizeString(preg_replace('/\s+/', ' ', strtolower($_POST['modifierName'])));
			$modifierID = $mysql->query('SELECT modifierID FROM marvel_modifiersList WHERE name = "'.$name.'"');
			if ($modifierID->rowCount()) $modifierID = $modifierID->fetchColumn();
			else {
				$mysql->query('INSERT INTO marvel_modifiersList (name, userDefined) VALUES ("'.$name.'", '.intval($_SESSION['userID']).')');
				$modifierID = $mysql->lastInsertId();
			}
			$addModifier = $mysql->query("INSERT INTO marvel_modifiers (characterID, modifierID) VALUES ($characterID, $modifierID)");
			$numModifiers = intval($_POST['numModifiers']) + 1;
			if ($addModifier->getResult()) {
?>
				<div class="modifier<?=$numModifiers % 3 == 0?' third':''?>">
					<div class="tr labelTR">
						<label class="level">Level</label>
						<label class="cost">Cost</label>
					</div>
					<div class="clearfix">
						<span class="modifierName"><?=mb_convert_case($name, MB_CASE_TITLE)?></span>
						<input type="text" name="modifier[<?=$modifierID?>][cost]" value="0" class="cost">
						<input type="text" name="modifier[<?=$modifierID?>][level]" value="0" class="level">
					</div>
					<textarea name="modifier[<?=$modifierID?>][details]"></textarea>
				</div>
<?
			}
		}
	}
?>