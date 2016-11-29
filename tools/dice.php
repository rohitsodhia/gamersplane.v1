<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">Dice Roller</h1>

		<div id="roller">
			<div class="clearfix"><div class="sectionControls" data-ratio=".8">
				<div class="trapezoid">
					<select class="prettySelect">
						<option value="basic">Basic Dice</option>
						<option value="starwarsffg">Star Wars FFG</option>
						<option value="fate">Fate Dice</option>
						<option value="fengshui">Feng Shui</option>
					</select>
				</div>
			</div></div>
			<h2 class="headerbar hbDark hb_hasButton">
				<span class="dice_basic">Basic Dice</span>
				<span class="dice_starwarsffg hideDiv">Star Wars FFG</span>
				<span class="dice_fate hideDiv">Fate Dice</span>
				<span class="dice_fengshui hideDiv">Feng Shui Dice</span>
			</h2>
			<div class="dice_basic">
				<p>Dice should be in the format<br>(number of dice)d(die type)(modifier)</p>
				<p>Separate rolls should be separated by commas or on new lines.</p>
				<p><i>Example: 2d4, 3d6+4</i></p>
				<form id="basic_customDice" method="post" action="/tools/process/dice/">
					<div class="clearfix">
						<input type="hidden" name="rollType" value="basic">
						<textarea id="basic_dice" name="dice"><?=$_POST['dice']?></textarea>
						<div class="rollWrapper"><button id="basic_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button></div>
						<div class="cbWrapper">
							<input id="basic_rerollAces" type="checkbox" name="options[rerollAces]"<?	if ($_POST['rerollAces']) echo ' checked="checked"'; ?>><label for="basic_rerollAces">Reroll Aces</label>
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
			<div class="dice_starwarsffg hideDiv">
				<div class="clearfix">
					<div class="dicePool"></div>
					<div class="rollWrapper"><button id="starwarsffg_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button></div>
					<a id="starwarsffg_clear" href="">Clear</a>
				</div>
				<p>Click on a die above to remove it from the dice pool.<br>Click on a die below to add it to the dice pool.</p>
				<div class="clearfix">
					<a href="" id="starwarsffg_ability" class="addDiceLink borderBox floatLeft">
						<div class="starwarsffg_dice ability"><div></div></div>
						<span>Ability</span>
					</a>
					<a href="" id="starwarsffg_difficulty" class="addDiceLink borderBox floatRight">
						<div class="starwarsffg_dice difficulty"><div></div></div>
						<span>Difficulty</span>
					</a>
				</div>
				<div class="clearfix">
					<a href="" id="starwarsffg_proficiency" class="addDiceLink borderBox floatLeft">
						<div class="starwarsffg_dice proficiency"><div></div></div>
						<span>Proficiency</span>
					</a>
					<a href="" id="starwarsffg_challenge" class="addDiceLink borderBox floatRight">
						<div class="starwarsffg_dice challenge"><div></div></div>
						<span>Challenge</span>
					</a>
				</div>
				<div class="clearfix">
					<a href="" id="starwarsffg_boost" class="addDiceLink borderBox floatLeft">
						<div class="starwarsffg_dice boost"><div></div></div>
						<span>Boost</span>
					</a>
					<a href="" id="starwarsffg_setback" class="addDiceLink borderBox floatRight">
						<div class="starwarsffg_dice setback"><div></div></div>
						<span>Setback</span>
					</a>
				</div>
				<div class="alignCenter">
					<a href="" id="starwarsffg_force" class="addDiceLink borderBox alignLeft">
						<div class="starwarsffg_dice force"><div></div></div>
						<span>Force</span>
					</a>
				</div>
			</div>
			<div class="dice_fate hideDiv hbdMargined">
				<div class="clearfix">
					<label for="fate_count">Number of dice: </label>
					<input id="fate_count" type="text" value="4">
					<div class="rollWrapper"><button id="fate_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button></div>
				</div>
			</div>
			<div class="dice_fengshui hideDiv hbdMargined">
				<div class="clearfix">
					<label for="fengshui_av">Action Value: </label>
					<input id="fengshui_av" type="number" value="0" min="0" step="1">
					<select id="fengshui_type" name="fengshui_type">
						<option value="standard">Standard</option>
						<option value="fortune">Fortune</option>
						<option value="closed">Closed</option>
					</select>
					<div class="rollWrapper"><button id="fengshui_roll" type="submit" name="roll" class="fancyButton rollBtn">Roll</button></div>
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
<?	require_once(FILEROOT.'/footer.php'); ?>
