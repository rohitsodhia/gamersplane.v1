<div class="combobox" ng-click="$event.stopPropagation()">
	<input type="text" ng-model="search.value" ng-class="{ resultsOpen: showDropdown }" ng-change="revealDropdown()" ng-focus="revealDropdown()" ng-blur="hideDropdown()" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)"></a>
	<div class="results" ng-show="showDropdown">
		<a ng-repeat="set in data | filter: (!bypassFilter || '') && { value: search.value }" data-id="{{set.id}}" ng-mousedown="setBox(set)" ng-mouseover="setSelected(set, $event)" ng-bind-html="set.value | trustHTML" combobox-option="oWidth"></a>
	</div>
</div>