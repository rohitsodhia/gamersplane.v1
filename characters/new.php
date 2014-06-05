<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mob = $pathOptions[1] == 'mob'?1:0;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">New <?=$mob?'Mob':'Character'?></h1>
		
		<form id="newChar" action="/characters/process/new/" method="post">
			<div class="tr">
				<label class="textLabel">Label</label>
				<input type="text" name="label" maxlength="50">
			</div>
			<div class="tr">
				<label class="textLabel">System</label>
				<select name="system">
					<option value="">Select One</option>
<?
	$allSystems = $systems->getAllSystems(TRUE);
	foreach ($allSystems as $systemID => $systemInfo) echo "\t\t\t\t\t".'<option value="'.$systemID.'">'.printReady($systemInfo['fullName'])."</option>\n";
?>
					<option value="1">Custom</option>
				</select>
			</div>
			<input type="hidden" name="mob" value="<?=$mob?>">
			<div class="tr alignCenter"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>