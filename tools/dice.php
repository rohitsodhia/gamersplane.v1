<?
	$loggedIn = checkLogin(0);
	
	$rolls = array();
	$dice = array();
	if (isset($_POST['roll'])) {
		$rolls = parseRolls($_POST['dice']);
	} elseif (isset($_POST['d4']) || isset($_POST['d6']) || isset($_POST['d8']) || isset($_POST['d10']) || isset($_POST['d12']) || isset($_POST['d20']) || isset($_POST['d100'])) {
		foreach ($_POST as $key => $value) if (in_array($key, array('d4', 'd6', 'd8', 'd10', 'd12', 'd20', 'd100'))) $rolls = parseRolls('1'.$key);
	}
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Dice Roller</h1>
		
		<div id="roller">
			<p>Dice should be in the format<br>(number of dice)d(dice type)(modifier)</p>
			<p>Separate rolls should be separated by commas or on new lines.</p>
			<p><i>Example: 2d4, 3d6+4</i></p>
			<form id="customDice" method="post">
				<textarea id="dice" name="dice"><?=$_POST['dice']?></textarea>
				<button id="roll" type="submit" name="roll" class="fancyButton">Roll</button>
				<br class="clear">
				<input id="rerollAces" type="checkbox" name="rerollAces"<? if ($_POST['rerollAces']) echo ' checked="checked"'; ?>>Reroll Aces
			</form>
			
			<form id="indivDice" method="post">
				<div>
					<button type="submit" name="d4" class="diceButton fancyButton">d4</button>
					<button type="submit" name="d6" class="diceButton fancyButton">d6</button>
					<button type="submit" name="d8" class="diceButton fancyButton">d8</button>
					<button type="submit" name="d10" class="diceButton fancyButton">d10</button>
				</div>
				<div>
					<button type="submit" name="d12" class="diceButton fancyButton">d12</button>
					<button type="submit" name="d20" class="diceButton fancyButton">d20</button>
					<button type="submit" name="d100" class="diceButton fancyButton">d100</button>
				</div>
			</form>
		</div>

		<div id="diceSpace">
<?
	$rerollAces = $_POST['rerollAces']?1:0;
	if (sizeof($rolls) && is_array($rolls)) { foreach($rolls as $roll) {
		$results = rollDice($roll, $rerollAces);
		echo "\t\t\t<div><p>".$roll."<br>\n\t\t\t".$results['indivRolls'].' = '.$results['total']."</p></div>\n";
	} }
?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>