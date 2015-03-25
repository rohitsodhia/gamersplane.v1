<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Looking for Game</h1>
		
		<form id="myLFG" method="post" action="/games/process/lfg">
			<p>Looking for a game, but no one's running? Maybe the ones running right now just aren't right? Check off the games you want to play in,<!-- and list details or custom games you're interested in below,--> and GMs can make a game that matches!</p>
			<div id="systems" class="clearfix">
<?
	$lfgs = $mysql->query("SELECT system FROM lfg WHERE userID = {$currentUser->userID}");
	$lfgVals = array();
	while ($game = $lfgs->fetchColumn()) 
		$lfgVals[] = $game;
	foreach ($systems->getAllSystems(true) as $slug => $system) {
?>
				<div class="game"><input id="cb_<?=$slug?>" type="checkbox" name="lfg[]" value="<?=$slug?>"<?=in_array($slug, $lfgVals)? ' checked="checked"':''?>> <label for="cb_<?=$slug?>"><?=$system?></label></div>
<?	} ?>
			</div>
			<div id="submitDiv"><button type="submit" name="update" class="fancyButton">Update</button></div>
		</form>
<? require_once(FILEROOT.'/footer.php'); ?>