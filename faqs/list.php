<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">FAQs</h1>
		<div class="sideWidget left"><ul>
			<li ng-repeat="(slug, category) in catMap"><a href="#{{slug}}">{{category}}</a></li>
		</ul></div>
		<div class="mainColumn right">
			<div ng-repeat="(category, faqs) in aFAQs">
				<a name="{{category}}"></a>
				<h2 class="headerbar hbDark" skew-element>{{catMap[category]}}</h2>
				<div class="faqs" hb-margined>
					<div ng-repeat="faq in faqs | orderBy: 'order'" class="faq">
						<div class="question">{{faq.question}}</div>
						<div class="answer" ng-bind-html="faq.answer.encoded | trustHTML"></div>
					</div>
				</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>