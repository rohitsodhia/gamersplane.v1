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
							<div id="aspects">
								<h2 class="headerbar hbDark" skew-element>Aspects <a href="" ng-click="character.aspects.push(blanks.aspects)">[ Add Aspect ]</a></h2>
								<div id="aspectList" class="hbMargined">
									<div class="aspect item tr clearfix">
										<input type="text" ng-model="character.highConcept" class="aspectName width5 alignLeft" placeholder="High Concept">
									</div>
									<div class="aspect item tr clearfix">
										<input type="text" ng-model="character.trouble" class="aspectName width5 alignLeft" placeholder="Trouble">
									</div>
									<div ng-repeat="aspect in character.aspects track by $index" class="aspect item tr clearfix">
										<input type="text" ng-model="aspect.value" class="aspectName width5 alignLeft" placeholder="Aspect Name">
										<a href="" class="remove sprite cross" ng-click="character.aspects.splice($index, 1)"></a>
									</div>
								</div>
							</div>
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
