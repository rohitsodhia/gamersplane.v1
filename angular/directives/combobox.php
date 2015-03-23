<div class="combobox" ng-click="$event.stopPropagation()">
	<input type="text" ng-model="search" ng-class="{ resultsOpen: showDropdown }" ng-change="revealDropdown()" ng-focus="revealDropdown()" ng-blur="hideDropdown()" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)"></a>
	<div class="results" ng-show="showDropdown">
		<a ng-repeat="set in data | filter: { value: search }" data-id="{{set.id}}" ng-mousedown="setBox(set)" ng-mouseover="setSelected(set, $event)" ng-bind-html="set.value | trustHTML"></a>
	</div>
</div>