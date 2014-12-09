<?
	$currentUser->checkACP('users');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage Users</h1>
			<form id="editMusicMaster" method="post" action="/acp/process/manageUsers/">
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<ul class="prettyList">
<?
	$users = $mysql->query('SELECT * FROM users ORDER BY username');
	foreach ($users as $user) {
?>
				<li<?=$user['activatedOn'] == null?' class="not_activated"':''?>>
					<a href="/user/<?=$user['userID']?>/" class="username"><?=$user['username']?></a>
					<div class="actions">
						<a href="/ucp/<?=$user['userID']?>/">Edit</a>
						<a href="" class="delete">Delete</a>
					</div>
				</li>
<?	} ?>
			</ul>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>