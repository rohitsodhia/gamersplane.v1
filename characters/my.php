<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">My Characters</h1>
		
<?	if (isset($_GET['invalidType']) || isset($_GET['invalidLabel'])) { ?>
		<div class="alertBox_error"><ul>
<?
	if (isset($_GET['invalidLabel'])) echo "\t\t\t<li>You must enter a unique and valid label. No profanity!</li>\n";
	if (isset($_GET['invalidType'])) echo "\t\t\t<li>You have to select a system if you want to make a new char!</li>\n";
?>
		</ul></div>
<?	} elseif (isset($_GET['delete']) || isset($_GET['label'])) { ?>
		<div class="alertBox_success"><ul>
<?
	if (isset($_GET['delete'])) echo "\t\t\t<li>Your character was successfully deleted.</li>\n";
	if (isset($_GET['label'])) echo "\t\t\t<li>Label successfully edited.</li>\n";
?>
		</ul></div>
<?	} ?>
		<div id="characterList">
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Characters</h2>
<?
	$characters = $mysql->query("SELECT c.*, IF(l.characterID IS NOT NULL AND l.inLibrary = 1, 1, 0) inLibrary FROM characters c INNER JOIN systems s ON c.system = s.system LEFT JOIN characterLibrary l ON c.characterID = l.characterID WHERE c.userID = {$currentUser->userID} ORDER BY s.fullName, c.charType, c.label");
	
	$noItems = false;
	if ($characters->rowCount()) {
		echo "\t\t\t<ul id=\"userChars\" class=\"hbdMargined hbAttachedList\">\n";
		foreach ($characters as $info) {
?>
				<li id="char_<?=$info['characterID']?>" class="clearfix character">
					<a href="/characters/<?=$info['system']?>/<?=$info['characterID']?>" class="label"><?=$info['label']?></a
					><div class="charType"><?=$info['charType']?></div
					><div class="systemType"><?=$systems->getFullName($info['system'])?></div
					><div class="links">
						<a href="/characters/editBasic/<?=$info['characterID']?>/" class="editBasic sprite editWheel" title="Edit Label/Type" alt="Edit Label/Type"></a>
						<a href="/characters/<?=$info['system']?>/<?=$info['characterID']?>/edit/" class="editChar sprite pencil" title="Edit Character" alt="Edit Character"></a>
						<a href="/characters/process/libraryToggle/<?=$info['characterID']?>/" class="libraryToggle sprite book<?=$info['inLibrary']?'':' off'?>" title="<?=$info['inLibrary']?'Remove from':'Add to'?> Library" alt="<?=$info['inLibrary']?'Remove from':'Add to'?> Library"></a>
						<a href="/characters/delete/<?=$info['characterID']?>/" class="delete sprite cross" title="Delete Character" alt="Delete Character"></a>
					</div>
				</li>
<?
		}
		echo "\t\t\t</ul>\n";
	} else 
		$noItems = true;
	echo "\t\t\t".'<div class="noItems'.($noItems == false?' hideDiv':'').'">It seems you don\'t have any characters yet. You might wanna get started!</div>'."\n";
?>
		</div>

		<div id="libraryFavorites">
			<div class="clearfix hbdTopper"><a href="/characters/library/" class="fancyButton">Character Library</a></div>
			<h2 class="headerbar hbDark hb_hasButton hb_hasList">Library Favorites</h2>
<?
	$libraryItems = $mysql->query("SELECT c.*, u.username FROM characterLibrary_favorites f, characters c, systems s, users u WHERE c.system = s.system AND c.userID = u.userID AND f.userID = {$currentUser->userID} AND f.characterID = c.characterID ORDER BY s.fullName, c.charType, c.label");
	$noItems = false;
	if ($libraryItems->rowCount()) {
		echo "\t\t\t<ul id=\"libraryChars\" class=\"hbdMargined hbAttachedList\">\n";
		foreach ($libraryItems as $info) {
?>
				<li id="char_<?=$info['characterID']?>" class="clearfix character">
					<a href="/characters/library/unfavorite/<?=$info['characterID']?>" class="unfavorite sprite tassel" title="Unfavorite Character" alt="Unfavorite Character"></a
					><a href="/characters/<?=$info['system']?>/<?=$info['characterID']?>" class="label"><?=$info['label']?></a
					><div class="charType"><?=$info['charType']?></div
					><div class="systemType"><?=$systems->getFullName($info['system'])?></div
					><div class="owner"><a href="/ucp/<?=$info['userID']?>" class="username"><?=$info['username']?></a></div>
				</li>
<?
		}
		echo "\t\t\t</ul>\n";
	} else 
		$noItems = true;
	echo "\t\t\t".'<div class="noItems'.($noItems == false?' hideDiv':'').'">You don\'t have anything from the library favorited. Check out what you\'re missing!</div>'."\n";
?>
		</div>

		<form id="newChar" action="/characters/process/new/" method="post">
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
	$allSystems = $systems->getAllSystems(true);
	foreach ($allSystems as $slug => $name) {
?>
					<option value="<?=$slug?>"><?=printReady($name)?></option>
<?	} ?>
					<option value="custom">Custom</option>
				</select>
			</div>
			<div class="tr">
				<label class="textLabel">Type</label>
				<select name="charType">
<?	foreach ($charTypes as $charType) { ?>
					<option value="<?=$charType?>"><?=$charType?></option>
<?	} ?>
				</select>
			</div>
			<div class="tr buttonPanel"><button type="submit" name="create" class="fancyButton">Create</button></div>
		</form>
<?	require_once(FILEROOT.'/footer.php'); ?>