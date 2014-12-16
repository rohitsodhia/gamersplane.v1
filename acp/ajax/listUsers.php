<?
	if (!isset($_POST['show']) || $_POST['show'] == 'active') $show = 'suspendedUntil IS NULL AND banned = 0';
	elseif ($_POST['show'] == 'suspended') $show = 'suspendedUntil IS NOT NULL';
	elseif ($_POST['show'] == 'bannedUsers') $show = 'banned = 1';
	else die();
	$users = $mysql->query("SELECT * FROM users WHERE {$show} ORDER BY username");
	foreach ($users as $user) {
?>
				<li<?=$user['activatedOn'] == null?' class="not_activated"':''?> data-id="<?=$user['userID']?>">
					<div class="info">
						<a href="/user/<?=$user['userID']?>/" class="username"><?=$user['username']?></a>
						<div class="actions">
							<a href="/ucp/<?=$user['userID']?>/">Edit</a>
							<a href="" class="delete">Delete</a>
							<a href="?suspend=1" class="suspend"><?=$user['suspended']?'Uns':'S'?>uspend</a>
<?	if (!$user['banned']) { ?>
							<a href="?ban=1" class="ban">Ban</a>
<?	} ?>
						</div>
					</div>
				</li>
<?	} ?>
