<?
	$loggedIn = checkLogin();
	$userID = intval($_SESSION['userID']);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Looking for Game</h1>
		
		<form id="myLFG" method="post" action="/games/process/lfg">
			<p>Looking for a game, but no one's running? Maybe the ones running right now just aren't right? Check off the games you want to play in,<!-- and list details or custom games you're interested in below,--> and GMs can make a game that matches!</p>
			<div id="systems" class="clearfix">
<?
	$lfgs = $mysql->query('SELECT systemID FROM lfg WHERE userID = '.$userID);
	$lfgVals = array();
	while ($game = $lfgs->fetchColumn()) $lfgVals[] = $game;
	$allSystems = $systems->getAllSystems(TRUE);
	foreach ($allSystems as $systemID => $systemInfo) echo "\t\t\t\t<div class=\"game\"><input id=\"cb_{$systemInfo['shortName']}\" type=\"checkbox\" name=\"lfg[{$systemID}]\" value=\"1\"".(in_array($systemID, $lfgVals)? ' checked="checked"':'')."> <label for=\"cb_{$systemInfo['shortName']}\">{$systemInfo['fullName']}</label></div>\n";
?>
			</div>
			<div id="submitDiv"><button type="submit" name="update" class="fancyButton">Update</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>