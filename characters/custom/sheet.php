	<div class="tr">
		<h1 class="customChar"><?= $this->getName() ?></h2>
	</div>

	<div class="tr">
		<div class="customChar"><?= printReady(BBCode2Html($this->getNotes(false)),['nl2br']) ?></div>
	</div>
