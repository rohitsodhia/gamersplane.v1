<!DOCTYPE html>
<html>
	<style>
		html { margin: 0; padding: 10px 0; }
		body { background: #111; font-size: 13px; font-family: 'Lucida Grande', Verdana, Arial, Sans-Serif; color: #FFF; }
		#content { width: 578px; border: 1px solid #333; margin: 0 auto; padding: 10px; background-color: #000; }
		h1 { font-size: 1.3em; font-weight: bold; text-decoration: none; text-align: center; }
		p { margin: .75em 0; }
		a, a:visited, a:active { text-decoration: none; color: #A00; }
		a:hover { color: #06D; }
		a.username, a.username:visited, a.username:active { color: #06F; }
		a.username:hover { text-decoration: underline; }
	</style>
<head>
</head>

<body><div id="content">
	<div id="header"><img src="http://gamersplane.com/images/logo.png" height="100"></div>
	<div id="contentBody">
		<h1>New <?=$details['system']?> Game</h1>
		<div style="text-align: center"><img src="http://gamersplane.com/images/logos/<?=$details['systemShort']?>.jpg" style="max-width: 200px;"></div>
		<p>A new <?=$details['system']?> game has been started on Gamers Plane!</p>
		<p><a href="http://gamersplane.com/ucp/<?=$userID?>" class="username"><?=$_SESSION['username']?></a> has started up a game for <?=$details['numPlayers']?> players called "<?=$details['title']?>". Check out the <a href="http://gamersplane.com/games/<?=$gameID?>">game's details</a> and submit a character before the slots fill!</p>
		<p style="text-align: right;">- The Gamers Plane Team</p>
		<div style="font-size: .8em; margin-top: 2em;">If you'd like to stop recieving emails about new games, please head to your <a href="http://gamersplane.com/ucp/cp">user control panel</a> and change the option labeled as "Recieve new game emails?" to "No".</div>
	</div>
</div></body>
</html>