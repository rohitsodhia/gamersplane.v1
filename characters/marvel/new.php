<?
	require_once(FILEROOT.'/includes/systemInfo/marvel.php');
	
	$loggedIn = checkLogin();
?>
<? require_once(FILEROOT.'/header.php'); ?>
<? if ($_GET['noStones']) { ?>
		<div class="alertBox_error"><ul>
<?
		if ($_GET['noStones']) { echo "\t\t\t<li>You have to start your character with a positive number of stones.</li>\n"; }
?>
		</ul></div>
<? } ?>
		<h1>Character Creation</h1>
		<h2><img src="<?=SITEROOT?>/images/logos/marvel.jpg"></h2>
		
		<form id="startingStonesForm" method="post" action="<?=SITEROOT?>/characters/process/marvel/new/">
			<div class="tr">Number of starting stones: <input id="startingStones" type="text" name="startingStones" maxlength="2"></div>
			<div id="charNames" class="alignCenter">
				Name: <input id="normName" type="text" name="normName">
				Super Name: <input type="text" name="superName">
			</div>
			<input type="hidden" name="characterID" value="<?=intval($pathOptions[2])?>">
			<button type="submit" name="submit" class="btn_submit"></button>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>