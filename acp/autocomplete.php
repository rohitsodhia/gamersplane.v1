<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }
	elseif (!in_array('autocomplete', $acpPermissions) && !in_array('all', $acpPermissions)) { header('Location: /acp/'); exit; }

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Autocomplete</h1>
			<h2 class="headerbar hbDark">Skills</h2>
			<div class="hbdMargined">
				<div id="newItems">
					<h3>New Items</h3>
					<div class="tr headerTR">
						<div class="type">Type</div>
						<div class="name">Name</div>
						<div class="addedBy">Added By</div>
					</div>
<?
	$newItems = $mysql->query('SELECT ua.uItemID, ua.itemType, ua.name, ua.addedBy, u.username FROM userAddedItems ua INNER JOIN users u ON u.userID = ua.addedBy WHERE name IS NOT NULL AND action IS NULL ORDER BY ua.itemType, u.username');
	foreach ($newItems as $newItem) {
?>
					<div id="newItem_<?=$newItem['uItemID']?>" class="tr newItem">
						<div class="type"><?=$newItem['itemType']?></div>
						<input type="text" value="<?=$newItem['name']?>" class="name">
						<div class="addedBy"><a href="/ucp/<?=$newItem['addedBy']?>/" class="username"><?=$newItem['username']?></a></div>
						<div class="actions">
							<a href="" class="sprite check"></a>
							<a href="" class="sprite cross"></a>
						</div>
					</div>
<?
	}
?>
				</div>
				<div id="addToSystem">
					<h3>Add to System</h3>
					<div class="tr headerTR">
						<div class="name">Name</div>
						<div class="system">System</div>
						<div class="addedBy">Added By</div>
					</div>
<?
	$addToSystem = $mysql->query('SELECT ua.uItemID, ua.itemType, ua.itemID, il.name, ua.addedBy, u.username, ua.systemID FROM userAddedItems ua INNER JOIN users u ON u.userID = ua.addedBy INNER JOIN charAutocomplete il ON ua.itemType = il.type AND ua.itemID = il.itemID WHERE ua.name IS NULL AND ua.itemID IS NOT NULL AND action IS NULL ORDER BY ua.itemType, il.name');
	$currentType = '';
	foreach ($addToSystem as $item) {
		if ($item['itemType'] != $currentType) {
			$currentType = $item['itemType'];
			echo "					<div class=\"typeHeader\">{$item['itemType']}</div>\n";
		}
?>
					<div id="item_<?=$item['uItemID']?>" class="tr item">
						<div class="name"><?=$item['name']?></div>
						<div class="system"><?=$systems->getFullName($systems->getShortName($item['systemID']))?></div>
						<div class="addedBy"><a href="/ucp/<?=$item['addedBy']?>/" class="username"><?=$item['username']?></a></div>
						<div class="actions">
							<a href="" class="sprite check"></a>
							<a href="" class="sprite cross"></a>
						</div>
					</div>
<?
	}
?>
				</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>