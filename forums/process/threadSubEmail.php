<?
	$pathBase = 'http://gamersplane.com';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equip="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Thread Update</title>
</head>

<body style="margin:0;font-family:Arial;font-size:14px;line-height:1.3em"><table style="width:100%"><tr><td>
	<table align="center" style="width:560px;border-collapse:collapse;">
		<tr><td style="height:100px;padding:10px;background:url(<?=$pathBase?>/images/emails/header.jpg) top left repeat-x #111;border:1px solid #111"><img src="<?=$pathBase?>/images/bodyComponents/logo.png" height="100" alt="Header image"></td></tr>
		<tr><td style="padding:0 10px;border-color:#111;border-style:solid;border-width:0 1px 0 1px">
			<h1 style="background-color:#C60;color:#FFF;margin:.3em 0;"><img src="<?=$pathBase?>/images/emails/text/Thread_Update.jpg" alt="Thread Update"></h1>
			<p>There's a new post in the thread "<a href="<?=$pathBase?>/forums/thread/<?=$threadManager->getThreadID()?>/?view=newPost#newPost"><?=$threadManager->getThreadProperty('title')?></a>" in the <a href="<?=$pathBase?>/forums/<?=$threadManager->getForumProperty('forumID')?>/"><?=$threadManager->getForumProperty('title')?></a> forum.</p>
			<p style="text-align: right;">- The Gamers Plane Team</p>
			<p style="font-size: .8em; margin-top: 2em;">If you'd like to stop recieving emails about new posts, please head to the thread or forum in question and unsubscribe via the link to the upper right.</p>
		</td></tr>
		<tr><td style="padding:10px;background:#444;border:1px solid #444;color:#FFF">
			&copy; Gamers' Plane
		</td></tr>
	</table>
</td></tr></table></body>
</html>