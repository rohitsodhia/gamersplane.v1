<?php
	class faqs {
		public $categories = [
			'Getting Started' => 'getting-started',
			'Characters' => 'characters',
			'Games' => 'games',
			'Tools' => 'tools'
		];

		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'get') {
				$this->getFAQs();
			} elseif ($pathOptions[0] == 'changeOrder') {
				$this->changeOrder();
			} elseif ($pathOptions[0] == 'save') {
				$this->save();
			} elseif ($pathOptions[0] == 'delete') {
				$this->delete();
			} else {
				displayJSON(['failed' => true]);
			}
		}

		public function getFAQs($pr = true) {
			$mongo = DB::conn('mongo');

			$rawFAQs = $mongo->faqs->find(
				[],
				['sort' => ['category' => 1, 'order' => 1]]
			);
			$faqs = [];
			foreach ($rawFAQs as $faq) {
				$faq['_id'] = (string) $faq['_id'];
				$faq['order'] = (int) $faq['order'];
				$faq['answer'] = [
					'raw' => $faq['answer'],
					'encoded' => BBCode2Html(printReady($faq['answer']))
				];
				$faqs[$faq['category']][] = $faq;
			}
			displayJSON(['faqs' => $faqs]);
		}

		public function changeOrder() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = genMongoId($_POST['id']);
			$direction = $_POST['direction'];
			if ($direction != 'up' && $direction != 'down') {
				displayJSON(['failed' => true]);
			}

			$faq = $mongo->faqs->findOne(
				['_id' => $id],
				['_id' => false, 'category' => true, 'order' => true]
			);
			if ($direction == 'up' && $faq['order'] == 1) {
				displayJSON(['failed' => true, 'cannotSwitch' => true]);
			}
			$switch = $mongo->faqs->findOne(
				[
					'category' => $faq['category'],
					'order' => $faq['order'] + ($direction == 'up' ? -1 : 1 )
				],
				['_id' => true]
			);
			if ($direction == 'down' && $faq['order'] == null) {
				displayJSON(['failed' => true, 'cannotSwitch' => true]);
			}
			$mongo->faqs->updateOne(
				['_id' => $id],
				['$set' => ['order' => $faq['order'] + ($direction == 'up' ? -1 : 1)]]
			);
			$mongo->faqs->updateOne(
				['_id' => $switch['_id']],
				['$set' => ['order' => $faq['order']]]
			);
		}

		public function save() {
			global $currentUser;
			$mongo = DB::conn('mongo');

			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = isset($_POST['id']) ? genMongoId($_POST['id']) : null;
			$question = $_POST['question'];
			$answer = $_POST['answer'];
			if ($id) {
				$faq = $mongo->faqs->findAndUpdate(
					['_id' => $id],
					['$set' => [
						'question' => $question,
						'answer' => $answer
					]],
					['returnDocument' => MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER]);
			} else {
				$faq = [
					'category' => $_POST['category'],
					'question' => $question,
					'answer' => $answer,
					'order' => 0
				];
				$order = $mongo->faqs->count(['category' => $faq['category']]);
				$faq['order'] = $order + 1;
				$mongo->faqs->insertOne($faq);
			}

			$faq['_id'] = (string) $faq['_id'];
			$faq['order'] = (int) $faq['order'];
			$faq['answer'] = [
				'raw' => $faq['answer'],
				'encoded' => BBCode2Html(printReady($faq['answer']))
			];

			displayJSON(['success' => true, 'faq' => $faq]);
		}

		public function delete() {
			global $currentUser;
			$mongo = DB::conn('mongo');
			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = genMongoId($_POST['id']);
			$faq = $mongo->faqs->findAndDelete(['_id' => $id]);
			$faqs = $mongo->faqs->updateMany(
				[
					'category' => $faq['category'],
					'order' => ['$gt' => $faq['order']]
				],
				['$inc' => ['order' => -1]]
			);

			displayJSON(['success' => true]);
		}
	}
?>
