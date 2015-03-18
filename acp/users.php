<?
	$currentUser->checkACP('users');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<form id="suspendDate" method="post" action="/acp/process/users/" class="attachForm">
				<input id="userID" type="hidden" name="userID" value="">
				<input type="hidden" name="ajax" value="1">
				<span>Suspend until:</span>
				<select name="month">
<?	for ($count = 1; $count <= 12; $count++) echo "					<option>$count</option>\n"; ?>
				</select>
				<select name="day">
<?	for ($count = 1; $count <= 31; $count++) echo "					<option>$count</option>\n"; ?>
				</select>
				<select name="year">
<?	for ($count = date('Y'); $count <= date('Y') + 2; $count++) echo "					<option>$count</option>\n"; ?>
				</select>
				<input type="text" name="hour" value="0">:<input type="text" name="minutes" value="00">
				<button type="submit" name="suspend" class="normal">Confirm</button>
			</form>
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
<?	require_once(FILEROOT.'/footer.php'); ?>