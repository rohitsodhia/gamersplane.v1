<?
	$currentUser->checkACP('faqs');

	require_once(FILEROOT.'/header.php');
	require_once(FILEROOT.'/acp/acpSidebar.php');
?>

		<div class="mainColumn right">
			<h1 class="headerbar">Manage FAQs</h1>
<?	if ($formErrors->getErrors('addFAQ')) { ?>
			<div class="alertBox_error"><ul>
<?
		if ($formErrors->checkError('noCategory')) 
			echo "				<li>No category selected.</li>\n";
		if ($formErrors->checkError('noQuestion')) 
			echo "				<li>No question asked.</li>\n";
		if ($formErrors->checkError('noAnswer')) 
			echo "				<li>No answer given.</li>\n";
?>
			</ul></div>
<?	} ?>
			<form ng-submit="createFAQ()">
				<div class="pRow">
					<label>Category</label>
					<combobox data="categories" value="newFAQ.category" select></combobox>
				</div>
				<div class="pRow">
					<label for="question">Question</label>
					<input id="question" type="text" ng-model="newFAQ.question">
				</div>
				<textarea ng-model="newFAQ.answer"></textarea>
				<div class="pRow"><button type="submit" name="add" class="fancyButton">Add FAQ</button></div>
			</form>
			<hr>

			<div ng-repeat="(slug, category) in catMap" ng-if="aFAQs[slug].length">
				<h2 class="headerbar hbDark" skew-element>{{catMap[category]}}</h2>
				<div class="faqs" hb-margined>
					<div ng-repeat="faq in aFAQs[slug] | orderBy: 'order'" class="faq" ng-class="{ 'editing': faq._id == editing }">
						<div class="controls">
							<a href="" ng-click="moveUp(faq, faqs)" class="sprite upArrow" ng-if="!$first"></a>
							<a href="" ng-click="moveDown(faq, faqs)" class="sprite downArrow" ng-if="!$last"></a>
						</div>
						<div class="display">
							<div class="question">{{faq.question}}</div>
							<div class="answer" ng-bind-html="faq.answer.encoded | trustHTML"></div>
							<div><a href="" ng-click="editFAQ(faq)" class="edit">[ Edit ]</a> <a href="" ng-click="deleteFAQ(faq._id, faqs, $index)" class="delete">[ Delete ]</a></div>
						</div>
						<div class="inputs">
							<div class="question"><input type="text" ng-model="faq.question"></div>
							<div class="answer"><textarea ng-model="faq.answer.raw"></textarea></div>
							<div class="tr"><a href="" ng-click="saveFAQ(faq)" class="save">[ Save ]</a> <a href="" ng-click="cancelSave()" class="cancel">[ Cancel ]</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
<?	require_once(FILEROOT.'/footer.php'); ?>