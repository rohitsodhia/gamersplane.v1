<div class="combobox" ng-click="$event.stopPropagation()">
	<input type="text" ng-model="search.value" search-id="search.id" placeholder="System" ng-class="{ resultsOpen: showDropdown }" ng-change="revealDropdown()" ng-focus="revealDropdown()" ng-blur="hideDropdown()">
	<a class="dropdown" ng-click="toggleDropdown($event)"></a>
	<div class="results" ng-show="showDropdown">
		<a ng-repeat="set in data | filter:search" ng-mousedown="setBox($event, set)" ng-bind-html="set.value"></a>
	</div>
</div>