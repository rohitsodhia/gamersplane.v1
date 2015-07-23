				<div id="nameDiv" class="tr">
					<label class="textLabel leftLabel">
						<div class="labelText">Name</div>
						<input id="name" type="text" maxlength="50" ng-model="character.name" class="width5 alignLeft" placeholder="Name">
					</label>
				</div>
				<div id="fpStats" class="tr">
					<label class="textLabel leftLabel">
						<div class="labelText">Fate Points</div>
						<input type="text" maxlength="2" ng-model="character.fatePoints.current">
					</label>
					<label class="textLabel leftLabel">
						<div class="labelText">Refresh</div>
						<input type="text" maxlength="2" ng-model="character.fatePoints.refresh">
					</label>
				</div>

				<div class="clearfix">
					<div class="mainColumn">
						<div class="clearfix">
							<setup-itemized character="character" list="character.aspects" blank="''"></setup-itemized>
							<div id="stunts">
								<h2 class="headerbar hbDark">Stunts <a href="" class="addItem">[ Add Stunt ]</a></h2>
								<div id="stuntsList" class="hbdMargined">
								</div>
							</div>
						</div>
						<div class="clearfix">
						</div>
					</div>
					<div class="sidebar">
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</h2>
							<div id="<?=$stressType?>Stress" class="hbdMargined">
							</div>
						</div>
						<div id="consequences">
							<h2 class="headerbar hbDark">Consequences</h2>
						</div>
					</div>
				</div>
				
				<h2 class="headerbar hbDark">Background/Notes</h2>
				<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
