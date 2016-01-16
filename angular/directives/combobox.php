<div ng-click="$event.stopPropagation()" class="combobox" ng-class="{ 'autocomplete': usingAutocomplete, 'focused': hasFocus, 'resultsOpen': showDropdown && filterData().length }">
	<input type="text" ng-model="search" ng-change="showDropdown = true" ng-focus="inputFocused()" ng-blur="inputBlurred()" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)" ng-hide="usingAutocomplete"></a>
	<div class="results" ng-show="showDropdown && filterData().length">
		<a ng-repeat="set in options | filter: (!bypassFilter || '') && { display: search }" ng-mousedown="setBox(set)" ng-class="set.class.concat({ 'selected': curSelected == $index })" ng-bind-html="set.display | trustHTML"></a>
	</div>
</div>