<?
	$loggedIn = checkLogin(0);
	$userID = $_SESSION['userID'];

	$acpPermissions = $mysql->query('SELECT permission FROM acpPermissions WHERE userID = '.$userID);
	if ($acpPermissions->rowCount() == 0) { header('Location: /'); exit; }
	else $acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Administrative Control Panel</h1>
			<div class="hbMargined">
				<div class="acpOption">
					<div class="title"><a href="/acp/autocomplete/">Manage Autocomplete</a></div>
				</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>