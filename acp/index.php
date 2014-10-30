<?
	$acpPermissions = $mysql->query("SELECT permission FROM acpPermissions WHERE userID = {$currentUser->userID}");
	if ($acpPermissions->rowCount() == 0) { header('Location: /'); exit; }
	else $acpPermissions = $acpPermissions->fetchAll(PDO::FETCH_COLUMN);

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Administrative Control Panel</h1>
			<ul class="hbMargined">
				<li><a href="/acp/autocomplete/">Manage Autocomplete</a></li>
				<li><a href="/acp/faqs/">Manage FAQs</a></li>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>