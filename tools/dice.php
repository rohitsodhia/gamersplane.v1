<?
	$loggedIn = checkLogin(0);
?>
<? require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Dice Roller</h1>
		
		<div id="roller">
			<div class="clearfix"><div id="controls" class="wingDiv sectionControls" data-ratio=".8">
				<div class="wingDivContent">
					<select class="prettySelect">
						<option value="basic">Basic Dice</option>
						<option value="sweote">Star Wars: Edge of the Empire</option>
					</select>
				</div>
				<div class="wing dlWing"></div>
				<div class="wing drWing"></div>
			</div></div>
			<h2 class="headerbar hbDark hb_hasButton">
				<span class="dice_basic">Basic Dice</span>
				<span class="dice_sweote hideDiv">Star Wars: Edge of the Empire</span>
			</h2>
			<div class="dice_basic">
				<p>Dice should be in the format<br>(number of dice)d(dice type)(modifier)</p>
				<p>Separate rolls should be separated by commas or on new lines.</p>
				<p><i>Example: 2d4, 3d6+4</i></p>
				<form id="basic_customDice" method="post" action="/tools/process/dice">
					<div class="clearfix">
						<input type="hidden" name="rollType" value="basic">
						<textarea id="basic_dice" name="dice"><?=$_POST['dice']?></textarea>
						<button id="basic_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button>
						<div class="cbWrapper">
							<input id="basic_rerollAces" type="checkbox" name="options[rerollAces]"<? if ($_POST['rerollAces']) echo ' checked="checked"'; ?>><label for="basic_rerollAces">Reroll Aces</label>
						</div>
					</div>
					<div id="basic_indivDice">
						<div>
							<button type="submit" name="dice" value="d4" class="diceButton fancyButton">d4</button>
							<button type="submit" name="dice" value="d6" class="diceButton fancyButton">d6</button>
							<button type="submit" name="dice" value="d8" class="diceButton fancyButton">d8</button>
							<button type="submit" name="dice" value="d10" class="diceButton fancyButton">d10</button>
						</div>
						<div>
							<button type="submit" name="dice" value="d12" class="diceButton fancyButton">d12</button>
							<button type="submit" name="dice" value="d20" class="diceButton fancyButton">d20</button>
							<button type="submit" name="dice" value="d100" class="diceButton fancyButton">d100</button>
						</div>
					</div>
				</form>
			</div>
			<div class="dice_sweote hideDiv">
				<div class="clearfix">
					<div class="dicePool"></div>
					<button id="sweote_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button>
					<a id="sweote_clear" href="">Clear</a>
				</div>
				<p>Click on a dice above to remove it from the dice pool.<br>Click on a dice below to add it to the dice pool.</p>
				<div class="clearfix">
					<a href="" id="sweote_ability" class="addDiceLink borderBox floatLeft">
						<div class="sweote_dice ability"><div></div></div>
						<span>Ability</span>
					</a>
					<a href="" id="sweote_difficulty" class="addDiceLink borderBox floatRight">
						<div class="sweote_dice difficulty"><div></div></div>
						<span>Difficulty</span>
					</a>
				</div>
				<div class="clearfix">
					<a href="" id="sweote_proficiency" class="addDiceLink borderBox floatLeft">
						<div class="sweote_dice proficiency"><div></div></div>
						<span>Proficiency</span>
					</a>
					<a href="" id="sweote_challenge" class="addDiceLink borderBox floatRight">
						<div class="sweote_dice challenge"><div></div></div>
						<span>Challenge</span>
					</a>
				</div>
				<div class="clearfix">
					<a href="" id="sweote_boost" class="addDiceLink borderBox floatLeft">
						<div class="sweote_dice boost"><div></div></div>
						<span>Boost</span>
					</a>
					<a href="" id="sweote_setback" class="addDiceLink borderBox floatRight">
						<div class="sweote_dice setback"><div></div></div>
						<span>Setback</span>
					</a>
				</div>
				<div class="alignCenter">
					<a href="" id="sweote_force" class="addDiceLink borderBox alignLeft">
						<div class="sweote_dice force"><div></div></div>
						<span>Force</span>
					</a>
				</div>
			</div>
		</div>

		<div id="diceSpace">
<?
	$rerollAces = $_POST['rerollAces']?1:0;
	if (sizeof($rolls) && is_array($rolls)) { foreach($rolls as $roll) {
		$results = rollDice($roll, $rerollAces);
		echo "\t\t\t<div><p>".$roll."<br>\n\t\t\t".displayIndivDice($results['indivRolls']).' = '.$results['result']."</p></div>\n";
	} }
?>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>