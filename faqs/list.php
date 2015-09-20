<?	require_once(FILEROOT.'/header.php'); ?>
		<h1 class="headerbar">FAQs</h1>
		<div class="sideWidget left"><ul>
			<li ng-repeat="(slug, category) in catMap"><a href="#{{slug}}">{{category}}</a></li>
		</ul></div>
		<div class="mainColumn right">
			<div ng-repeat="(slug, category) in catMap" ng-if="aFAQs[slug].length">
				<a name="{{slug}}"></a>
				<h2 class="headerbar hbDark" skew-element>{{category}}</h2>
				<div class="faqs" hb-margined>
					<div ng-repeat="faq in aFAQs[slug] | orderBy: 'order'" class="faq">
						<div class="question">{{faq.question}}</div>
						<div class="answer" ng-bind-html="faq.answer.encoded | trustHTML"></div>
					</div>
				</div>
			</div>
		</div>
<? require_once(FILEROOT.'/footer.php'); ?>