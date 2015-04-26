				<form enctype="multipart/form-data" ng-class="{ editing: 'editing' }">
					<input type="hidden" name="mongoID" ng-bind="data._id">
					<div class="preview">
						<img ng-if="!new && data.image" src="/images/links/{{data._id}}.{{data.image}}">
						<button type="submit" name="action" value="deleteImage" class="action_deleteImage sprite cross small" ng-if="!new && data.image" ng-click="deleteImage()"></button>
					</div>
					<div class="link">
						<input type="text" ng-model="data.title" ng-disabled="!new && !editing" class="title placeholder" data-placeholder="Title">
						<input type="text" ng-model="data.url" ng-disabled="!new && !editing" class="url placeholder" data-placeholder="URL">
						<div class="image"><input type="file" ng-file-select ng-model="data.newImage" ng-disabled="!new && !editing"></div>
					</div>
					<div class="type">
						<div ng-show="!new && !editing">{{data.level}}</div>
						<combobox ng-show="new || editing" data="levels" value="cb_value" search="data.level" strict></combobox>

						<div class="tr" ng-show="data.networks.rpga || editing"><pretty-checkbox eleid="rpga_{{data._id}}" checkbox="data.networks.rpga" ng-show="editing"></pretty-checkbox> <label for="rpga_{{data._id}}">The RPG Academy Network</label></div>

						<div class="tr" ng-show="data.categories.blog || editing"><pretty-checkbox eleid="blog_{{data._id}}" checkbox="data.categories.blog" ng-show="editing"></pretty-checkbox> <label for="blog_{{data._id}}">Blog</label></div>

						<div class="tr" ng-show="data.categories.podcast || editing"><pretty-checkbox eleid="podcast_{{data._id}}" checkbox="data.categories.podcast" ng-show="editing"></pretty-checkbox> <label for="podcast_{{data._id}}">Podcast</label></div>
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
