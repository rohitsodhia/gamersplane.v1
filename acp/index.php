<?
	$currentUser->checkACP();
	
	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Administrative Control Panel</h1>
			<ul class="hbMargined">
<?	if ($currentUser->checkACP('users', false)) { ?>
				<li><a href="/acp/users/">Manage Users</a></li>
<?
	}
	if ($currentUser->checkACP('music', false)) {
?>
				<li><a href="/acp/music/">Manage Music</a></li>
<?
	}
	if ($currentUser->checkACP('autocomplete', false)) {
?>
				<li><a href="/acp/autocomplete/">Manage Autocomplete</a></li>
<?
	}
	if ($currentUser->checkACP('faqs', false)) {
?>
				<li><a href="/acp/faqs/">Manage FAQs</a></li>
<?
	}
	if ($currentUser->checkACP('links', false)) {
?>
				<li><a href="/acp/links/">Manage Links</a></li>
<?	} ?>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>