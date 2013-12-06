<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Dice Roller</h1>
		
		<div id="roller">
			<div class="clearfix"><div id="controls" class="wingDiv sectionControls" data-ratio=".8">
				<div class="wingDivContent">
					<select class="prettySelect">
						<option value="basic">Info</option>
						<option value="sweote">Star Wars: Edge of the Empire</option>
					</select>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div></div>
			<h2 class="headerbar hbDark hb_hasButton">
				<span span="dice_basic">Basic Dice</span>
			</h2>
			<div class="dice_basic">
				<p>Dice should be in the format<br>(number of dice)d(dice type)(modifier)</p>
				<p>Separate rolls should be separated by commas or on new lines.</p>
				<p><i>Example: 2d4, 3d6+4</i></p>
				<form id="customDice" method="post">
					<textarea id="dice" name="dice"><?=$_POST['dice']?></textarea>
					<button id="roll" type="submit" name="roll" class="fancyButton">Roll</button>
					<div class="tr">
						<input id="rerollAces" type="checkbox" name="rerollAces"<? if ($_POST['rerollAces']) echo ' checked="checked"'; ?>>Reroll Aces
					</div>
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