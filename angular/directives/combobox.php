<div ng-click="$event.stopPropagation()" class="combobox" ng-class="{ 'autocomplete': usingAutocomplete, 'focused': hasFocus, 'resultsOpen': showDropdown && filterData().length }">
	<input type="text" ng-model="search" ng-change="showDropdown = true" ng-focus="inputFocused()" ng-blur="inputBlurred()" ng-keydown="navigateResults($event)">
	<a class="dropdown" ng-click="toggleDropdown($event)" ng-hide="usingAutocomplete">
		<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 14 10" enable-background="new 0 0 14 10" xml:space="preserve">
			 <path fill-rule="evenodd" clip-rule="evenodd" d="M14,4.6L7,10L0,4.6V1l7,5.4L14,1V4.6z"/>
		</svg>
	</a>
	<div class="results" ng-show="showDropdown && filterData().length">
		<a ng-repeat="set in options | filter: (!bypassFilter || '') && { display: search }" ng-mousedown="setBox(set)" ng-class="set.class.concat({ 'selected': curSelected == $index })" ng-bind-html="set.display | trustHTML"></a>
	</div>
</div>
