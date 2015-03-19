<div class="combobox" ng-click="$event.stopPropagation()">
	<input type="text" ng-model="search.value" search-id="search.id" ng-class="{ resultsOpen: showDropdown }" ng-change="revealDropdown()" ng-focus="revealDropdown()" ng-blur="hideDropdown()" ng-placeholder="System">
	<a class="dropdown" ng-click="toggleDropdown($event)"></a>
	<div class="results" ng-show="showDropdown">
		<div ng-repeat="set in data | filter:search"><a ng-click="setBox(set)" ng-bind-html="set.value"></a></div>
	</div>
</div>