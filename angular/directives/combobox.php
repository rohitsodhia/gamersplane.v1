<div ng-click="$event.stopPropagation()" class="combobox" ng-class="{ 'autocomplete': usingAutocomplete, 'focused': hasFocus }">
	<input type="text" ng-model="search" ng-class="{ resultsOpen: showDropdown && filterData().length }" ng-change="showDropdown = true" ng-focus="showDropdown = true" ng-blur="showDropdown = false" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)" ng-hide="usingAutocomplete"></a>
	<div class="results" ng-show="showDropdown && filterData().length">
		<a ng-repeat="set in options | filter: (!bypassFilter || '') && { display: search }" ng-mousedown="setBox(set)" ng-mouseover="setSelected($index)" ng-class="{ 'selected': curSelected == $index }" ng-bind-html="set.display | trustHTML"></a>
	</div>
</div>