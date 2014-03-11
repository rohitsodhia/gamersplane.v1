<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
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
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Characters</h2>
<?
	$characters = $mysql->query('SELECT c.*, s.shortName, s.fullName FROM characters c, systems s WHERE c.systemID = s.systemID AND c.userID = '.$userID.' ORDER BY s.fullName, c.type, c.label');
	
	$noItems = FALSE;
	if ($characters->rowCount()) {
		echo "\t\t\t<ul id=\"userChars\" class=\"hbdMargined hbAttachedList\">\n";
		foreach ($characters as $info) {
?>
				<li id="char_<?=$info['characterID']?>" class="clearfix character">
					<a href="<?=SITEROOT?>/characters/<?=$info['shortName']?>/<?=$info['characterID']?>" class="label"><?=$info['label']?></a
					><div class="charType"><?=$charLabelMap[$info['type']]?></div
					><div class="systemType"><?=$info['fullName']?></div
					><div class="links">
						<a href="<?=SITEROOT?>/characters/editBasic/<?=$info['characterID']?>" class="editBasic sprite pencil" title="Edit Label/Type" alt="Edit Label/Type"></a>
						<a href="<?=SITEROOT?>/characters/delete/<?=$info['characterID']?>" class="delete sprite cross" title="Delete Character" alt="Delete Character"></a>
					</div>
				</li>
<?
		}
		echo "\t\t\t</ul>\n";
	} else $noItems = TRUE;
	echo "\t\t\t".'<div class="noItems'.($noItems == FALSE?' hideDiv':'').'">It seems you don\'t have any characters yet. You might wanna get started!</div>'."\n";
?>
		</div>

		<div id="libraryFavorites">
			<div class="clearfix hbdTopper"><a href="<?=SITEROOT?>/characters/library/" class="fancyButton">Character Library</a></div>
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Library Favorites</h2>
<?
	$libraryItems = $mysql->query("SELECT c.*, s.shortName, s.fullName, u.username FROM characterLibrary_favorites f, characters c, systems s, users u WHERE c.systemID = s.systemID AND c.userID = u.userID AND f.userID = $userID AND f.characterID = c.characterID");
	$noItems = FALSE;
	if ($libraryItems->rowCount()) {
		echo "\t\t\t<ul id=\"libraryChars\" class=\"hbdMargined hbAttachedList\">\n";
		foreach ($libraryItems as $info) {
?>
				<li id="char_<?=$info['characterID']?>" class="clearfix character">
					<a href="<?=SITEROOT?>/characters/unfavorite/<?=$info['characterID']?>" class="unfavorite sprite tassel" title="Unfavorite Character" alt="Unfavorite Character"></a
					><a href="<?=SITEROOT?>/characters/<?=$info['shortName']?>/<?=$info['characterID']?>" class="label"><?=$info['label']?></a
					><div class="charType"><?=$charLabelMap[$info['type']]?></div
					><div class="systemType"><?=$info['fullName']?></div
					><div class="owner"><a href="<?=SITEROOT?>/ucp/<?=$info['userID']?>" class="username"><?=$info['username']?></a></div>
				</li>
<?
		}
		echo "\t\t\t</ul>\n";
	} else $noItems = TRUE;
	echo "\t\t\t".'<div class="noItems'.($noItems == FALSE?' hideDiv':'').'">You don\'t have anything from the library favorited. Check out what you\'re missing!</div>'."\n";
?>
		</div>

		<form id="newChar" action="<?=SITEROOT?>/characters/process/new/" method="post">
			<h2 class="headerbar hbDark">New Character</h1>
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
			<div class="tr">
				<label class="textLabel">Type</label>
				<select name="type">
<?
	foreach ($charLabelMap as $key => $type) echo "\t\t\t\t\t<option value=\"{$key}\">{$type}</option>\n";
?>
				</select>
			</div>
			<div class="tr buttonPanel"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>