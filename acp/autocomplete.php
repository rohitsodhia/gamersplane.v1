<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = $userID");
	$acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);
	if (sizeof($acpPermissions) == 0) { header('Location: /'); exit; }

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Autocomplete</h1>
			<h2 class="headerbar hbDark">Skills</h2>
			<div class="hbdMargined">
				<h3>New Items</h3>
<?
	$newItems = $mysql->query('SELECT ni.newItemID, ni.itemType, ni.name, ni.addedBy, u.username FROM newItemized ni INNER JOIN users u ON u.userID = ni.addedBy WHERE name IS NOT NULL ORDER BY ni.itemType, u.username');
	foreach ($newItems as $newItem) {
		var_dump($newItem);
	}
?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>