				<form enctype="multipart/form-data" ng-class="{ editing: 'editing' }">
					<input type="hidden" name="mongoID" ng-bind="data._id">
					<div class="preview" ng-if="!new">
						<img ng-if="data.image" src="/images/links/{{data._id}}.{{data.image}}">
						<button type="submit" name="action" value="deleteImage" class="action_deleteImage sprite cross small" ng-if="data.image" ng-click="deleteImage()"></button>
					</div>
					<div class="details">
						<div class="link">
							<input type="text" ng-model="data.title" ng-disabled="!new && !editing" class="title" placeholder="Title">
							<input type="text" ng-model="data.url" ng-disabled="!new && !editing" class="url" placeholder="URL">
							<div class="image" ng-hide="!new && !editing"><input type="file" ngf-select ng-model="data.newImage"></div>
						</div>
						<div class="type">
							<div ng-show="!new && !editing">{{data.level.display}}</div>
							<combobox ng-show="new || editing" data="levels" value="data.level" select></combobox>

							<div class="tr" ng-show="data.networks.rpga || editing"><pretty-checkbox eleid="rpga_{{data._id}}" checkbox="data.networks" value="'rpga'" ng-show="editing"></pretty-checkbox> <label for="rpga_{{data._id}}">The RPG Academy Network</label></div>
						</div>
						<div class="categories">
							<div ng-repeat="category in categories" ng-show="data.categories.indexOf(category) != -1 || editing"><pretty-checkbox eleid="{{category}}_{{data._id}}" checkbox="data.categories" value="category" ng-show="editing"></pretty-checkbox> <label for="{{category}}_{{data._id}}">{{category}}</label></div>
						</div>
					</div>
					<div class="actions">
						<div ng-if="!new">
							<button type="submit" name="action" value="edit" class="action_edit sprite pencil" ng-show="!showEdit" ng-click="toggleEditing()"></button>
							<div ng-show="showEdit" class="confirmEdit">
								<button type="submit" name="action" value="save" class="action_edit_save sprite check green" ng-click="saveLink()"></button>
								<button type="submit" name="action" value="cancelEdit" class="action_edit_cancel sprite cross" ng-click="toggleEditing()"></button>
							</div>
						</div>
						<div ng-if="!new">
							<button type="submit" name="action" value="deleteCheck" class="action_delete sprite cross" ng-show="!showDelete" ng-click="showDelete = !showDelete"></button>
							<div ng-show="showDelete" class="confirmDelete">
								<button type="submit" name="action" value="delete" class="action_delete_confirm sprite check" ng-click="deleteLink()"></button>
								<button type="submit" name="action" value="cancelDelete" class="action_delete_cancel sprite cross" ng-click="showDelete = !showDelete"></button>
							</div>
						</div>
						<button ng-if="new" type="submit" name="action" value="save" class="action_save sprite check green" ng-click="saveLink()"></button>
					</div>
				</form>
