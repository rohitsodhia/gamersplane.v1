<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$mob = $pathOptions[1] == 'mob'?1:0;
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">New <?=$mob?'Mob':'Character'?></h1>
		
		<form id="newChar" action="<?=SITEROOT?>/characters/process/new/" method="post">
			<div class="tr">
				<label class="textLabel">Label</label>
				<input type="text" name="label" maxlength="50">
			</div>
			<div class="tr">
				<label class="textLabel">System</label>
				<select name="system">
					<option value="">Select One</option>
<?
	$systems = $mysql->query('SELECT systemID, shortName, fullName FROM systems WHERE enabled = 1 AND systemID != 1 ORDER BY fullName');
	foreach ($systems as $info) echo "\t\t\t\t\t".'<option value="'.$info['systemID'].'">'.printReady($info['fullName'])."</option>\n";
?>
					<option value="1">Custom</option>
				</select>
			</div>
			<input type="hidden" name="mob" value="<?=$mob?>">
			<div class="tr alignCenter"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>