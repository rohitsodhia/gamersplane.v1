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
			$mysql = DB::conn('mysql');

			$rawFAQs = $mysql->query("SELECT * FROM faqs ORDER BY category, `order`");
			$faqs = [];
			foreach ($rawFAQs as $faq) {
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
			$mysql = DB::conn('mysql');

			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = (int) $_POST['id'];
			$direction = $_POST['direction'];
			if ($direction != 'up' && $direction != 'down') {
				displayJSON(['failed' => true]);
			}

			$faq = $mysql->query("SELECT category, order FROM faqs WHERE id = {$id}")->fetch();
			if ($direction == 'up' && $faq['order'] == 1) {
				displayJSON(['failed' => true, 'cannotSwitch' => true]);
			}
			if ($direction == 'down' && $faq['order'] == null) {
				displayJSON(['failed' => true, 'cannotSwitch' => true]);
			}
			$switch = $mysql->prepare("SELECT id FROM faqs WHERE category = :category AND order = :order");
			$switch->execute([':category' => $faq['category'], ':order' => $faq['order'] + ($direction == 'up' ? -1 : 1)]);
			$switch = $switch->fetch();
			$updateOrder = $mysql->prepare("UPDATE faqs SET order = :order WHERE id = :id");
			$updateOrder->execute([':id' => $id, ':order' => $faq['order'] + ($direction == 'up' ? -1 : 1)]);
			$updateOrder->execute([':id' => $switch['id'], ':order' => $faq['order']]);
		}

		public function save() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = isset($_POST['id']) ? (int) $_POST['id'] : null;
			$question = $_POST['question'];
			$answer = $_POST['answer'];
			if ($id) {
				$updateFAQ = $mysql->prepare("UPDATE faqs SET question = :question, answer = :answer WHERE id = :id");
				$updateFAQ->execute([':id' => $id, ':question' => $question, ':answer' => $answer]);
				$faq = $mysql->query("SELECT * FROM faqs WHERE id = {$id}")->fetch();
			} else {
				$faq = [
					'category' => $_POST['category'],
					'question' => $question,
					'answer' => $answer,
					'order' => 0
				];
				$getCatCount = $mysql->prepare("SELECT `order` FROM faqs WHERE category = ?");
				$getCatCount->execute($faq['category']);
				$faq['order'] = $getCatCount->rowCount() + 1;
				$addFAQ = $mysql->prepare("INSERT INTO faqs SET question = :question, answer = :answer, `order` = :order");
				$addFAQ->execute([':question' => $question, ':answer' => $answer, ':order' => $faq['order']]);
				$faq['id'] = $mysql->lastInsertId();
			}

			$faq['answer'] = [
				'raw' => $faq['answer'],
				'encoded' => BBCode2Html(printReady($faq['answer']))
			];

			displayJSON(['success' => true, 'faq' => $faq]);
		}

		public function delete() {
			global $currentUser;
			$mysql = DB::conn('mysql');

			if (!$currentUser->checkACP('faqs', false)) {
				displayJSON(['failed' => true, 'noPermissions' => true]);
			}

			$id = (int) $_POST['id'];
			$faq = $mysql->query("SELECT category, order FROM faqs WHERE id = {$id}")->fetch();
			$mysql->query("DELETE FROM faqs WHERE id = {$id}");
			$mysql->query("UPDATE faqs SET order = order - 1 WHERE category = '{$faqs['category']}' AND order > {$faqs['order']}");

			displayJSON(['success' => true]);
		}
	}
?>
