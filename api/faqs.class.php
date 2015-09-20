<?
	class faqs {
		public $categories = array('Getting Started' => 'getting-started', 'Characters' => 'characters', 'Games' => 'games', 'Tools' => 'tools');

		function __construct() {
			global $pathOptions;

			if ($pathOptions[0] == 'get') 
				$this->getFAQs();
			elseif ($pathOptions[0] == 'changeOrder') 
				$this->changeOrder();
			elseif ($pathOptions[0] == 'save') 
				$this->save();
			elseif ($pathOptions[0] == 'delete') 
				$this->delete();
			else 
				displayJSON(array('failed' => true));
		}

		public function getFAQs($pr = true) {
			global $mongo;

			$rawFAQs = $mongo->faqs->find()->sort(array('category' => 1, 'order' => 1));
			$faqs = array();
			foreach ($rawFAQs as $faq) {
				$faq['_id'] = (string) $faq['_id'];
				$faq['order'] = (int) $faq['order'];
				$faq['answer'] = array(
					'raw' => $faq['answer'],
					'encoded' => BBCode2Html(printReady($faq['answer']))
				);
				$faqs[$faq['category']][] = $faq;
			}
			displayJSON(array('faqs' => $faqs));
		}

		public function changeOrder() {
			global $currentUser, $mongo;
			if (!$currentUser->checkACP('faqs', false)) 
				displayJSON(array('failed' => true, 'noPermissions' => true));

			$id = new MongoId($_POST['id']);
			$direction = $_POST['direction'];
			if ($direction != 'up' && $direction != 'down') 
				displayJSON(array('failed' => true));

			$faq = $mongo->faqs->findOne(array('_id' => $id), array('_id' => false, 'category' => true, 'order' => true));
			if ($direction == 'up' && $faq['order'] == 1) 
				displayJSON(array('failed' => true, 'cannotSwitch' => true));
			$switch = $mongo->faqs->findOne(array('category' => $faq['category'], 'order' => $faq['order'] + ($direction == 'up'?-1:1)), array('_id' => true));
			if ($direction == 'down' && $faq['order'] == null) 
				displayJSON(array('failed' => true, 'cannotSwitch' => true));
			$mongo->faqs->update(array('_id' => $id), array('$set' => array('order' => $faq['order'] + ($direction == 'up'?-1:1))));
			$mongo->faqs->update(array('_id' => $switch['_id']), array('$set' => array('order' => $faq['order'])));
		}

		public function save() {
			global $currentUser, $mongo;
			if (!$currentUser->checkACP('faqs', false)) 
				displayJSON(array('failed' => true, 'noPermissions' => true));

			$id = isset($_POST['id'])?new MongoId($_POST['id']):null;
			$question = $_POST['question'];
			$answer = $_POST['answer'];
			if ($id) 
				$faq = $mongo->faqs->findAndModify(array('_id' => $id), array('$set' => array('question' => $question, 'answer' => $answer)), array(), array('new' => true));
			else {
				$faq = array(
					'category' => $_POST['category'],
					'question' => $question,
					'answer' => $answer,
					'order' => 0
				);
				$order = $mongo->faqs->count(array('category' => $faq['category']));
				$faq['order'] = $order + 1;
				$mongo->faqs->insert($faq);
			}

			$faq['_id'] = (string) $faq['_id'];
			$faq['order'] = (int) $faq['order'];
			$faq['answer'] = array(
				'raw' => $faq['answer'],
				'encoded' => BBCode2Html(printReady($faq['answer']))
			);

			displayJSON(array('success' => true, 'faq' => $faq));
		}

		public function delete() {
			global $currentUser, $mongo;
			if (!$currentUser->checkACP('faqs', false)) 
				displayJSON(array('failed' => true, 'noPermissions' => true));

			$id = new MongoId($_POST['id']);
			$faq = $mongo->faqs->findAndModify(array('_id' => $id), array(), array(), array('remove' => true));
			$faqs = $mongo->faqs->update(array('category' => $faq['category'], 'order' => array('$gt' => $faq['order'])), array('$inc' => array('order' => -1)));

			displayJSON(array('success' => true));
		}
	}
?>