				<form method="post" enctype="multipart/form-data" ng-class="{ editing: 'editing' }">
					<input type="hidden" name="mongoID" ng-bind="data.mongoID">
					<div class="preview">
						<img src="/images/links/{{data.image}}">
						<img src="/images/spacer.gif">
						<button type="submit" name="action" value="deleteImage" class="action_deleteImage sprite cross small"></button>
					</div>
					<div class="link">
						<input type="text" ng-model="data.title" ng-disabled="data.length != 0" class="title placeholder" data-placeholder="Title">
						<input type="text" ng-model="data.url" ng-disabled="data.length != 0" class="url placeholder" data-placeholder="URL">
						<input type="file" ng-model="data.image" ng-disabled="data.length != 0" class="image">
					</div>
					<div class="level">
						<div ng-if="!new" class="display">{{data.level}}</div>
						<combobox data="levels" value="data.level" search="search" default="Link" strict></combobox>
					</div>
					<div class="actions">
						<div>
							<button type="submit" name="action" value="edit" class="action_edit sprite pencil"></button>
							<div class="confirmEdit hideDiv">
								<button type="submit" name="action" value="save" class="action_edit_save sprite check green"></button>
								<button type="submit" name="action" value="cancelEdit" class="action_edit_cancel sprite cross"></button>
							</div>
						</div>
						<div>
							<button type="submit" name="action" value="deleteCheck" class="action_delete sprite cross"></button>
							<div class="confirmDelete hideDiv">
								<button type="submit" name="action" value="delete" class="action_delete_confirm sprite check"></button>
								<button type="submit" name="action" value="cancelDelete" class="action_delete_cancel sprite cross"></button>
							</div>
						</div>
					</div>
				</form>
