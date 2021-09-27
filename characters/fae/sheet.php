			<div id="nameDiv" class="tr">
				<label for="name" class="textLabel leftLabel">Name</label>
				<div>{{character.name | trustHTML}}</div>
			</div>
			<div id="fpStats" class="tr">
				<label class="textLabel leftLabel">Fate Points</label>
				<div>{{character.fatePoints.current}}</div>
				<label class="textLabel leftLabel">Refresh</label>
				<div>{{character.fatePoints.refresh}}</div>
			</div>

			<div class="clearfix">
				<div class="clearfix">
					<div id="aspects" class="itemizedList floatLeft">
						<h2 class="headerbar hbDark">Aspects</h2>
						<div id="aspectList" class="hbdMargined">
							<div class="aspect withLabel tr clearfix">
								<div><label>High Aspect</label></div>
								<div>{{character.highConcept}}</div>
							</div>
							<div class="aspect withLabel tr clearfix">
								<div><label>Trouble</label></div>
								<div>{{character.trouble}}</div>
							</div>
							<div ng-repeat="aspect in character.aspects" class="aspect item tr clearfix">{{aspect.name}}</div>
						</div>
					</div>
					<div id="approaches" class="floatLeft">
						<h2 class="headerbar hbDark">Approaches</h2>
						<div class="hbMargined hb-margined">
							<label ng-repeat="(approach, value) in character.approaches" class="tr">
								<div class="labelText">{{approach.capitalizeFirstLetter()}}</div>
								<div class="value">{{value}}</div>
							</label>
						</div>
					</div>
					<div id="stunts" class="itemizedList floatLeft">
						<h2 class="headerbar hbDark">Stunts</h2>
						<div id="stuntsList" class="hbMargined" hb-margined>
							<div ng-repeat="stunt in character.stunts" class="stunt item tr clearfix">{{stunt.name}}</div>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<div class="sidebar">
						<div id="stress">
							<h2 class="headerbar hbDark">Stress</h2>
							<div class="hbdMargined">
								<div ng-repeat="box in range(0, 3)" class="stressBox" ng-click="setStress(box)" ng-class="{ 'current': box == character.stress }">{{box}}</div>
							</div>
						</div>
						<div id="consequences">
							<h2 class="headerbar hbDark">Consequences</h2>
							<div class="hbdMargined">
								<div ng-repeat="consequence in range(2, 6, 2)" class="tr">{{consequence}}: {{character.consequences[consequence]}}</div>
							</div>
						</div>
					</div>
					<div id="notes">
						<h2 class="headerbar hbDark">Background/Notes</h2>
						<div class="hbdMargined">{{character.notes}}</div>
					</div>
				</div>
			</div>
