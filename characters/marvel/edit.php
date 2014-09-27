			<div class="tr basicInfo">
				<label class="name">Normal Name:</label>
				<input type="text" name="normName" maxlength="50" value="<?=$this->getName()?>" class="medText">
			</div>
			<div class="tr basicInfo">
				<label class="name">Super Name:</label>
				<input type="text" name="superName" maxlength="50" value="<?=$this->getSuperName()?>" class="medText">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Health:</label>
				<input type="text" name="health" maxlength="2" value="<?=$this->getHealth()?>" class="shortNum alignCenter">
			</div>
			<div class="tr basicInfo he">
				<label class="name">Energy:</label>
				<input type="text" name="energy" maxlength="2" value="<?=$this->getEnergy()?>" class="shortNum alignCenter">
			</div>
			
			<div id="remainingStones" class="tr basicInfo">
				<label class="name">Remaining Stones:</label>
				<input type="text" name="unusedStones[white]" maxlength="2" value="<?=$this->getUnusedStones('white')?>" class="stones shortNum alignCenter"> <span>White</span> stones <input type="text" id="remainingRedStones" name="unusedStones[red]" maxlength="2" value="<?=$this->getUnusedStones('red')?>" class="stones shortNum alignCenter"> <span class="redStones">Red</span> stones
			</div>
			
			<div id="stats" class="tr clearfix">
				<label class="first textLabel">Intelligence:</label>
				<input type="text" name="stats[int]" maxlength="2" value="<?=$this->getStat('int')?>" class="shortNum alignCenter">
				<label class="textLabel">Strength:</label>
				<input type="text" name="stats[str]" maxlength="2" value="<?=$this->getStat('str')?>" class="shortNum alignCenter">
				<label class="textLabel">Agility:</label>
				<input type="text" name="stats[agi]" maxlength="2" value="<?=$this->getStat('agi')?>" class="shortNum alignCenter">
				<label class="textLabel">Speed:</label>
				<input type="text" name="stats[spd]" maxlength="2" value="<?=$this->getStat('spd')?>" class="shortNum alignCenter">
				<label class="textLabel">Durability:</label>
				<input type="text" name="stats[dur]" maxlength="2" value="<?=$this->getStat('dur')?>" class="last shortNum alignCenter">
			</div>
			
			<div id="actions" class="clearfix">
				<h2 class="headerbar hbDark">Actions <a id="addAction" href="">[ Add Action ]</a></h2>
				<div class="hbdMargined">
					<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?	$this->showActionsEdit(); ?>
				</div>
			</div>
			
			<div id="modifiers" class="clearfix">
				<h2 class="headerbar hbDark">Modifiers <a href="addModifier" href="">[ Add Modifier ]</a></h2>
				<div class="hbdMargined">
					<p class="note">For costs of less then one white stone, use .3 for 1 red stone and .6 for 2 red stones</p>
<?	$this->showModifiersEdit(); ?>
				</div>
			</div>
			
			<div id="challenges" class="clearfix">
				<h2 class="headerbar hbDark">Challenges <a id="addChallenge" href="">[ Add Challenge ]</a></h2>
				<div class="hbdMargined">
					<div class="tr labelTR">
						<label class="leftLabel name shiftRight borderBox">Name</label>
						<label class="leftLabel stones">Stones</label>
					</div>
<?	$this->showChallengesEdit(1); ?>
				</div>
			</div>
			
			<div id="notes">
				<h2 class="headerbar hbDark">Notes</h2>
				<textarea name="notes" class="hbdMargined"><?=$challengeInfo['notes']?></textarea>
			</div>
