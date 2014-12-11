<?
	if (!isset($_POST['show']) || $_POST['show'] == 'active') $show = 'suspended = 0 AND banned = 0';
	elseif ($_POST['show'] == 'suspended') $show = 'suspended = 1';
	elseif ($_POST['show'] == 'banned') $show = 'banned = 1';
	$users = $mysql->query("SELECT * FROM users WHERE {$show} ORDER BY username");
	foreach ($users as $user) {
?>
				<li<?=$user['activatedOn'] == null?' class="not_activated"':''?>>
					<div class="info">
						<a href="/user/<?=$user['userID']?>/" class="username"><?=$user['username']?></a>
						<div class="actions">
							<a href="/ucp/<?=$user['userID']?>/">Edit</a>
							<a href="" class="delete">Delete</a>
							<a href="?suspend=1" class="suspend"><?=$user['suspended']?'Uns':'S'?>uspend</a>
						</div>
					</div>
				</li>
<?	} ?>
