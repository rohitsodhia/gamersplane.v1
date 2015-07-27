				<div id="nameDiv" class="tr">
					<label class="textLabel leftLabel">
						<div class="labelText">Name</div>
						<input id="name" type="text" maxlength="50" ng-model="character.name" class="width5 alignLeft">
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
					<div class="clearfix">
						<div id="aspects" class="floatLeft">
							<h2 class="headerbar hbDark" skew-element>Aspects <a href="" ng-click="addItem('aspects')">[ Add Aspect ]</a></h2>
							<div id="aspectList" class="hbMargined">
								<div class="aspect item tr clearfix">
									<input type="text" ng-model="character.highConcept" class="aspectName width5 alignLeft" placeholder="High Concept">
								</div>
								<div class="aspect item tr clearfix">
									<input type="text" ng-model="character.trouble" class="aspectName width5 alignLeft" placeholder="Trouble">
								</div>
								<div ng-repeat="aspect in character.aspects track by $index" class="aspect item tr clearfix">
									<input type="text" ng-model="aspect.name" class="aspectName width5 alignLeft" placeholder="Aspect Name">
									<a href="" class="remove sprite cross" ng-click="character.aspects.splice($index, 1)"></a>
								</div>
							</div>
						</div>
						<div id="approaches" class="floatLeft">
							<h2 class="headerbar hbDark" skew-element>Approaches</h2>
							<div class="hbMargined hb-margined">
								<label ng-repeat="(approach, value) in character.approaches" class="tr">
									<div class="labelText">{{approach.capitalizeFirstLetter()}}</div>
									<input type="text" ng-model="value">
								</label>
							</div>
						</div>
						<div id="stunts" class="floatLeft">
							<h2 class="headerbar hbDark" skew-element>Stunts <a href="" ng-click="addItem('stunts')">[ Add Stunt ]</a></h2>
							<div id="stuntsList" class="hbMargined" hb-margined>
								<div ng-repeat="stunt in character.stunts track by $index" class="stunt item tr clearfix">
									<input type="text" ng-model="stunt.name" class="stuntName width5 alignLeft" placeholder="Aspect Name">
									<a href="" class="remove sprite cross" ng-click="character.stunts.splice($index, 1)"></a>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix">
						<div class="sidebar">
							<div id="stress">
								<h2 class="headerbar hbDark">Stress</h2>
								<div id="<?=$stressType?>Stress" class="hbdMargined">
									<div ng-repeat="box in range(0, 3)" class="stressBox" ng-click="setStress(box)" ng-class="{ 'current': box == character.stress }">{{box}}</div>
								</div>
							</div>
							<div id="consequences">
								<h2 class="headerbar hbDark">Consequences</h2>
								<div class="hbdMargined">
									<div ng-repeat="consequence in range(2, 6, 2)" class="tr">{{consequence}}: <input type="text" ng-model="character.consequences[consequence]" class="width4"></div>
								</div>
							</div>
						</div>
						<div id="notes">
							<h2 class="headerbar hbDark">Background/Notes</h2>
							<textarea id="notes" name="notes" class="hbdMargined"><?=$this->getNotes()?></textarea>
						</div>
					</div>
				</div>
