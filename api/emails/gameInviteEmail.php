<?
	$pathBase = 'https://'.getenv('APP_URL');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equip="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Game Invite</title>
</head>

<body style="margin:0;font-family:Arial;font-size:14px;line-height:1.3em"><table style="width:100%"><tr><td>
	<table align="center" style="width:560px;border-collapse:collapse;">
		<tr><td>
			<p>If you're having trouble seeing this email, just head straight to <a href="<?=$pathBase?>/" target="_blank">Gamers' Plane</a>!</p>
		</td></tr>
		<tr><td style="height:100px;padding:10px;background-color:#777;border:1px solid #111"><img src="<?=$pathBase?>/images/bodyComponents/logo.png" height="100" alt="Header image"></td></tr>
		<tr><td style="padding:0 10px;border-color:#111;border-style:solid;border-width:0 1px 0 1px">
			<h1 style="background-color:#C60;color:#FFF;margin:.3em auto; width: 538px;"><img src="<?=$pathBase?>/images/emails/text/Game_Invite.jpg" alt="Game Invite"></h1>
<?	if ($gameInfo['system'] != 'custom') { ?>
			<p style="text-align: center"><img src="<?=$pathBase?>/images/logos/<?=$gameInfo['system']?>.png" style="max-width: 300px;"></p>
<?	} ?>
			<p><a href="<?=$pathBase?>/user/<?=$currentUser->userID?>/" class="username"><?=$currentUser->username?></a> has invited you to their <?=$systems->getFullName($gameInfo['system'])?> game, "<a href="<?=$pathBase?>/games/<?=$gameID?>/"><?=$gameInfo['title']?></a>"!</p>
			<p style="text-align: right;">- The Gamers Plane Team</p>
			<p style="font-size: .8em; margin-top: 2em;">If you'd like to stop recieving emails about pms, please head to your <a href="<?=$pathBase?>/ucp/">user control panel</a> and change the option labeled as "Recieve new pm emails?" to "No".</p>
		</td></tr>
		<tr><td style="padding:10px;background:#444;border:1px solid #444;color:#FFF">
			&copy; Gamers' Plane
		</td></tr>
	</table>
</td></tr></table></body>
</html>
