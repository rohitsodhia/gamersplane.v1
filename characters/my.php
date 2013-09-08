<?
	$loggedIn = checkLogin();
	
	if (intval($pathOptions[0]) > 0) {
		$characterID = intval($pathOptions[0]);
		$charSystem = $mysql->query('SELECT s.shortName FROM systems s INNER JOIN characters c USING (systemID) WHERE c.characterID = '.$characterID);
		$charSystem = $charSystem->fetchColumn();
		header('Location: '.SITEROOT.'/characters/'.$charSystem.'/'.$characterID); exit;
	}
	
	unset($_SESSION['characterID'], $_SESSION['stepDone']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">My Characters</h1>
		
<? if (isset($_GET['invalidType']) || isset($_GET['invalidLabel'])) { ?>
		<div class="alertBox_error"><ul>
<?
	if (isset($_GET['invalidLabel'])) echo "\t\t\t<li>You must enter a unique and valid label. No profanity!</li>\n";
	if (isset($_GET['invalidType'])) echo "\t\t\t<li>You have to select a system if you want to make a new char!</li>\n";
?>
		</ul></div>
<? } elseif (isset($_GET['delete']) || isset($_GET['label'])) { ?>
		<div class="alertBox_success"><ul>
<?
	if (isset($_GET['delete'])) echo "\t\t\t<li>Your character was successfully deleted.</li>\n";
	if (isset($_GET['label'])) echo "\t\t\t<li>Label successfully edited.</li>\n";
?>
		</ul></div>
<? } ?>
		<div id="characterList">
			<h2 class="headerbar hbDark">My Characters</h2>
<?
	$characters = $mysql->query('SELECT characters.*, systems.shortName, systems.fullName FROM characters, systems WHERE characters.systemID = systems.systemID AND characters.userID = '.intval($_SESSION['userID']).' ORDER BY systems.fullName, characters.label');
	
	$currentSystem = '';
	$noItems = FALSE;
	if ($characters->rowCount()) { foreach ($characters as $info) {
		echo "\t\t\t<div id=\"char_{$info['characterID']}\" class=\"tr\">\n";
		echo "\t\t\t\t".'<a href="'.SITEROOT.'/characters/'.$info['shortName'].'/'.$info['characterID'].'" class="charLabel">'.$info['label']."</a>\n";
		echo "\t\t\t\t".'<div class="systemType">'.$info['fullName']."</div>\n";
		echo "\t\t\t\t".'<div class="charLinks">';
		echo '<a href="'.SITEROOT.'/characters/editLabel/'.$info['characterID'].'" class="editLabel">Edit Label</a>';
		echo '<a href="'.SITEROOT.'/characters/delete/'.$info['characterID'].'" class="deleteChar">Delete Character</a>';
		echo "</div>\n";
		echo "\t\t\t</div>\n";
	} } else $noItems = TRUE;
	echo "\t\t\t".'<div class="noItems'.($noItems == FALSE?' hideDiv':'').'">It seems you don\'t have any characters yet. You might wanna get started!</div>'."\n";
?>
		</div>
		
		<form id="newChar" action="<?=SITEROOT?>/characters/process/new/" method="post">
			<h2 class="headerbar hbDark">Create New Character</h2>
			<div class="tr">
				<label class="textLabel">Label</label>
				<input type="text" name="label" maxlength="50">
<!--				<div>Labels must unique</div>-->
			</div>
			<div class="tr">
				<label class="textLabel">System</label>
				<select name="system">
					<option value="">Select One</option>
<?
	$systems = $mysql->query('SELECT systemID, shortName, fullName FROM systems WHERE enabled = 1 AND systemID != 1 ORDER BY fullName');
	foreach ($systems as $info) echo "\t\t\t\t\t".'<option value="'.$info['systemID'].'">'.printReady($info['fullName'])."</option>\n";
//	foreach ($systemNames as $value => $name) { echo "\t\t\t\t\t".'<option value="'.$value.'">'.$name."</option>\n"; }
?>
					<option value="1">Custom</option>
				</select>
			</div>
			<div class="tr"><div class="fancyButton"><button type="submit" name="create">Create</button></div></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>