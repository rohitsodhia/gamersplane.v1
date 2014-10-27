<?
	$userID = intval($_SESSION['userID']);
	$characterID = intval($pathOptions[1]);
	$basicInfo = $mysql->query('SELECT label, type FROM characters WHERE userID = '.$userID.' AND characterID = '.$characterID);
	if ($basicInfo->rowCount() == 0) { header('Location: /403'); }
	$basicInfo = $basicInfo->fetch();

?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Label/Type</h1>
<? if (isset($_GET['invalidLabel'])) { ?>
		<div class="alertBox_error"><ul>
			<li>Label cannot be blank</li>
		</ul></div>
		
<? } ?>
		
		<form method="post" action="/characters/process/editBasic/">
			<p><label for="label" class="leftLabel">Label:</label> <input id="label" type="text" name="label" maxlength="50" value="<?=$basicInfo['label']?>" class="medText"></p>
			<p><label class="leftLabel">Type:</label> <select id="type" name="type">
<?
	foreach ($charTypes as $type) echo "\t\t\t\t<option value=\"{$type}\"".($basicInfo['type'] == $type?' selected="selected"':'').">{$type}</option>\n";
?>
				</select></p>
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="alignCenter"><button type="submit" name="save" class="fancyButton">Save</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>