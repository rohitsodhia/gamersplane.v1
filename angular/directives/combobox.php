<div class="combobox" ng-click="$event.stopPropagation()" ng-class="{ 'focused': hasFocus }">
	<input type="text" ng-model="search" ng-class="{ resultsOpen: showDropdown }" ng-change="showDropdown = true" ng-focus="showDropdown = true" ng-blur="showDropdown = false" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)"></a>
	<div class="results" ng-show="showDropdown && filterData().length">
		<a ng-repeat="set in data | filter: (!bypassFilter || '') && { display: search }" ng-mousedown="setBox(set)" ng-mouseover="setSelected($index)" ng-class="{ 'selected': curSelected == $index }" ng-bind-html="set.display | trustHTML"></a>
	</div>
</div>