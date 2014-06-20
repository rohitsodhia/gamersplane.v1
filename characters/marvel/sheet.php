		<div class="tr basicInfo">
			<label class="name">Secret Identity:</label>
			<div><?=$this->getName()?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Super Name:</label>
			<div><?=$this->getSuperName()?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Health:</label>
			<div><?=$this->getHealth()?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Energy:</label>
			<div><?=$this->getEnergy()?></div>
		</div>
		<div class="tr basicInfo">
			<label class="name">Remaining Stones:</label>
			<div><?=$this->getUnusedStones('white').' <b>White</b> stone'.($this->getUnusedStones('white') == 1?'':'s').', '.$this->getUnusedStones('red').' <b class="redStones">Red</b> stone'.($this->getUnusedStones('red')?'':'s')?></div>
		</div>
		<div id="stats" class="tr basicInfo clearfix">
			<label class="first">Intelligence:</label>
			<div><?=$this->getStat('int')?></div>
			<label>Strength:</label>
			<div><?=$this->getStat('str')?></div>
			<label>Agility:</label>
			<div><?=$this->getStat('agi')?></div>
			<label>Speed:</label>
			<div><?=$this->getStat('spd')?></div>
			<label>Durability:</label>
			<div><?=$this->getStat('dur')?></div>
		</div>
		
		<div id="actions" class="clearfix">
			<h2 class="headerbar hbDark">Actions</h2>
			<div class="hbdMargined clearfix">
<?	$this->displayActions(); ?>
			</div>
		</div>
		
		<div id="modifiers" class="clearfix">
			<h2 class="headerbar hbDark">Modifiers</h2>
			<div class="hbdMargined clearfix">
<?	$this->displayModifiers(); ?>
			</div>
		</div>
		
		<div id="challenges" class="clearfix">
			<h2 class="headerbar hbDark">Challenges</h2>
			<div class="hbdMargined">
<?	$this->displayChallenges(); ?>
			</div>
		</div>
		
		<div id="notes">
			<h2 class="headerbar hbDark">Character Notes</h2>
			<div class="hbdMargined">
<? echo $this->getNotes(); ?>
			</div>
		</div>
