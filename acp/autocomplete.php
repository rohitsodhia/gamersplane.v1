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
	$newItems = $mysql->query('SELECT ni.newItemID, ni.itemType, ni.name, ni.addedBy, u.username FROM newItemized ni INNER JOIN users u ON u.userID = ni.addedBy WHERE name IS NOT NULL ORDER BY ni.itemType, u.username');
	foreach ($newItems as $newItem) {
?>
					<div id="newItem_<?=$newItem['newItemID']?>" class="tr newItem">
						<div class="type"><?=ucwords($newItem['itemType'])?></div>
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
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>