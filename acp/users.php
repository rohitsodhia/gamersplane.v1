<?
	$currentUser->checkACP('users');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Users</h1>
			<div id="controls">
				<a id="controls_active" href="">Active</a>
				<a id="controls_suspended" href="">Suspended</a>
				<a id="controls_bannedUsers" href="">Banned Users</a>
				<a id="controls_bannedIPs" href="">Banned IPs</a>
			</div>
			<ul class="prettyList">
<?	include('ajax/listUsers.php') ?>
			</ul>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>