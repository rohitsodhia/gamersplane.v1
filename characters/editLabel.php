<?
	$loggedIn = checkLogin();
	
	$characterID = intval($pathOptions[1]);
	$label = $mysql->query('SELECT label FROM characters WHERE userID = '.intval($_SESSION['userID']).' AND characterID = '.$characterID);
	if ($label->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
	$label = $label->fetchColumn();
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Edit Label</h1>
<? if (isset($_GET['invalidLabel'])) { ?>
		<div class="alertBox_error"><ul>
			<li>Label cannot be blank</li>
		</ul></div>
		
<? } ?>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/editLabel/" class="buttonPanel">
			<p><b>New label:</b> <input id="newLabel" type="text" name="label" maxlength="50" value="<?=$label?>"></p>
			<input id="characterID" type="hidden" name="characterID" value="<?=$characterID?>">
			<div class="fancyButton"><button type="submit" name="save">Save</button></div>
<!--			<button type="submit" name="cancel" class="btn_cancel"></button>-->
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>