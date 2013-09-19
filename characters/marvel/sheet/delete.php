<?
	$loggedIn = checkLogin();
	
	$characterID = intval($pathOptions[1]);
	if ($_GET['type'] == 'action') {
		$type = 'action';
		$mysql->query('SELECT actions.name FROM marvel_playerActions playerActions, marvel_actions actions WHERE playerActions.actionID = actions.actionID AND playerActions.characterID = '.$characterID.' AND playerActions.actionID = '.intval($_POST['actionID']));
	} else {
		$type = 'modifier';
		$mysql->query('SELECT modifiers.name FROM marvel_playerModifiers playerModifiers, marvel_modifiers modifiers WHERE playerModifiers.modifierID = modifiers.modifierID AND playerModifiers.characterID = '.$characterID.' AND playerModifiers.modifierID = '.intval($_POST['modifierID']));
	}
	list($name) = $mysql->getList();
	$name = printReady($name);
	
//	if ($mysql->rowCount() == 0) { header('Location: '.SITEROOT.'/403'); }
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1>Delete Character</h1>
		
		<p class="alignCenter">Are you sure you wanna delete <b><?=$name?></b>?</p>
		<p class="alignCenter">This cannot be reversed!</p>
		
		<form method="post" action="<?=SITEROOT?>/characters/process/marvel/edit/delete/<?=$type?>" class="alignCenter">
			<input type="hidden" name="characterID" value="<?=$characterID?>">
			<input type="hidden" name="<?=$type?>ID" value="<?=intval($_POST[$type.'ID'])?>">
			<div class="tr alignCenter"><input type="checkbox" name="alterStones" checked="checked"> Add/Subtract from <b>Remaining Stones</b></div>
			<button type="submit" name="delete<?=ucwords($type)?>" class="btn_delete"></button>
			<button type="submit" name="cancel" class="btn_cancel"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>