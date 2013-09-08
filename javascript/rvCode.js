/*
	rvCode: This is just a modified bbCode, based off research off the internet
	Code snippeted from: http://www.quirksmode.org/dom/range_intro.html
						 http://triaslama.wordpress.com/2008/06/06/cursor-positions-selected-text-and-a-simple-html-editor/
	Created by Rohit Sodhia
*/

function rvCode(divAreaID) {
	this.textAreaDiv = document.getElementById(divAreaID);
	this.textbox = this.textAreaDiv.getElementsByTagName("textarea")[0];
	
	var allDivs = this.textAreaDiv.getElementsByTagName("div");
	for (var divCount = 0; divCount < allDivs.length; divCount++) {
		if (allDivs[divCount].className == "buttonArea") {
			var allSubDivs = allDivs[divCount].getElementsByTagName("div");
			for (var subDivCount = 0; subDivCount <allSubDivs.length; subDivCount++) {
				if (allSubDivs[subDivCount].className == "buttonArea_text") { this.buttonTextArea = allSubDivs[subDivCount]; }
			}
			var self = this;
			
			var allTagButtons = allDivs[divCount].getElementsByTagName("input");
			for (var inputCount = 0; inputCount < allTagButtons.length; inputCount++) {
				if (allTagButtons[inputCount].name.split("_")[1] == "bold") {
					allTagButtons[inputCount].onclick = function () { self.addTag_bold(this); }
					allTagButtons[inputCount].onmouseover = function () { self.buttonTextArea.innerHTML = "[b]text[/b]"; }
					allTagButtons[inputCount].onmouseout = function () { self.buttonTextArea.innerHTML = "&nbsp;"; }
				}
				else if (allTagButtons[inputCount].name.split("_")[1] == "italics") {
					allTagButtons[inputCount].onclick = function () { self.addTag_italics(this); }
					allTagButtons[inputCount].onmouseover = function () { self.buttonTextArea.innerHTML = "[i]text[/i]"; }
					allTagButtons[inputCount].onmouseout = function () { self.buttonTextArea.innerHTML = "&nbsp;"; }
				}
				else if (allTagButtons[inputCount].name.split("_")[1] == "underline") {
					allTagButtons[inputCount].onclick = function () { self.addTag_underline(this); }
					allTagButtons[inputCount].onmouseover = function () { self.buttonTextArea.innerHTML = "[u]text[/u]"; }
					allTagButtons[inputCount].onmouseout = function () { self.buttonTextArea.innerHTML = "&nbsp;"; }
				}
				else if (allTagButtons[inputCount].name.split("_")[1] == "image") {
					allTagButtons[inputCount].onclick = function () { self.addTag_image(this); }
					allTagButtons[inputCount].onmouseover = function () { self.buttonTextArea.innerHTML = "[img]http://img_url[/img]"; }
					allTagButtons[inputCount].onmouseout = function () { self.buttonTextArea.innerHTML = "&nbsp;"; }
				}
				else if (allTagButtons[inputCount].name.split("_")[1] == "url") {
					allTagButtons[inputCount].onclick = function () { self.addTag_url(this); }
					allTagButtons[inputCount].onmouseover = function () { self.buttonTextArea.innerHTML = "[url]http://url[/url] or [url=http://url]text[/url]"; }
					allTagButtons[inputCount].onmouseout = function () { self.buttonTextArea.innerHTML = "&nbsp;"; }
				}
			}
		}
	}
}

if (rvCode.prototype.findPositions == null) {
	rvCode.prototype.findPositions = function () {
		if (window.getSelection) {
			this.startPosition = this.textbox.selectionStart;
			this.endPosition = this.textbox.selectionEnd;
		} else if (document.selection) {
			var range = document.selection.createRange();
			var rangeDuplicate = range.duplicate();
			if (range.text.length > 0) {
				rangeDuplicate.moveToElementText(this.textbox);
				rangeDuplicate.setEndPoint("EndToEnd", range);
				this.startPosition = rangeDuplicate.text.length - range.text.length;
				this.endPosition = this.startPosition + range.text.length;
			}
		}
	}
}

if (rvCode.prototype.formatString == null) {
	rvCode.prototype.formatString = function (taggedString) {
		var preString = this.textbox.value.substring(0, this.startPosition);
		var postString = this.textbox.value.substring(this.endPosition, this.textbox.value.length);
		
		formattedString = preString + taggedString + postString;
		return formattedString;
	}
}

if (rvCode.prototype.addTag_bold == null) {
	rvCode.prototype.addTag_bold = function (button) {
		this.findPositions();
		
		if (this.startPosition != this.endPosition) {
			var subStr = this.textbox.value.substring(this.startPosition, this.endPosition);
			subStr = "[b]" + subStr + "[/b]";
	
			this.textbox.value = this.formatString(subStr);
		} else {
//			var inputtedText = prompt("Enter your text to bold:");
//			var formattedText = "[b]" + inputtedText + "[/b]"
			
//			this.textbox.value += formattedText;
			
			if (this.tagStatus_bold == 0 || this.tagStatus_bold == null) {
				this.textbox.value += "[b]";
				this.tagStatus_bold = 1;
				button.value = "B*";
			} else {
				this.textbox.value += "[/b]";
				this.tagStatus_bold = 0;
				button.value = "B";
			}
		}
	}
}

if (rvCode.prototype.addTag_italics == null) {
	rvCode.prototype.addTag_italics = function (button) {
		this.findPositions();
		
		if (this.startPosition != this.endPosition) {
			var subStr = this.textbox.value.substring(this.startPosition, this.endPosition);
			subStr = "[i]" + subStr + "[/i]";
	
			this.textbox.value = this.formatString(subStr);
		} else {
//			var inputtedText = prompt("Enter your text to italicize:");
//			var formattedText = "[i]" + inputtedText + "[/i]"
			
//			this.textbox.value += formattedText;
			
			if (this.tagStatus_italics == 0 || this.tagStatus_italics == null) {
				this.textbox.value += "[i]";
				this.tagStatus_italics = 1;
				button.value = "i*";
			} else {
				this.textbox.value += "[/i]";
				this.tagStatus_italics = 0;
				button.value = "i";
			}
		}
	}
}

if (rvCode.prototype.addTag_underline == null) {
	rvCode.prototype.addTag_underline = function (button) {
		this.findPositions();
		
		if (this.startPosition != this.endPosition) {
			var subStr = this.textbox.value.substring(this.startPosition, this.endPosition);
			subStr = "[u]" + subStr + "[/u]";
	
			this.textbox.value = this.formatString(subStr);
		} else {
//			var inputtedText = prompt("Enter your text to italicize:");
//			var formattedText = "[u]" + inputtedText + "[/u]"
			
//			this.textbox.value += formattedText;
			
			if (this.tagStatus_underline == 0 || this.tagStatus_underline == null) {
				this.textbox.value += "[u]";
				this.tagStatus_underline = 1;
				button.value = "u*";
			} else {
				this.textbox.value += "[/u]";
				this.tagStatus_underline = 0;
				button.value = "u";
			}
		}
	}
}

if (rvCode.prototype.addTag_image == null) {
	rvCode.prototype.addTag_image = function (button) {
		this.findPositions();
		
		if (this.startPosition != this.endPosition) {
			var subStr = this.textbox.value.substring(this.startPosition, this.endPosition);
			subStr = "[img]" + subStr + "[/img]";
	
			this.textbox.value = this.formatString(subStr);
		} else {
//			var inputtedText = prompt("Enter the image url:", "http://");
//			var formattedText = "[img]" + inputtedText + "[/img]"
			
//			this.textbox.value += formattedText;
			
			if (this.tagStatus_image == 0 || this.tagStatus_image == null) {
				this.textbox.value += "[img]";
				this.tagStatus_image = 1;
				button.value = "img*";
			} else {
				this.textbox.value += "[/img]";
				this.tagStatus_underline = 0;
				button.value = "img";
			}
		}
	}
}

if (rvCode.prototype.addTag_url == null) {
	rvCode.prototype.addTag_url = function (button) {
		this.findPositions();
		
		if (this.startPosition != this.endPosition) {
			var subStr = this.textbox.value.substring(this.startPosition, this.endPosition);
			subStr = "[url]" + subStr + "[/url]";
	
			this.textbox.value = this.formatString(subStr);
		} else {
//			var inputtedText = prompt("Enter website url:", "http://");
//			var formattedText = "[url]" + inputtedText + "[/url]"
			
//			this.textbox.value += formattedText;
			
			if (this.tagStatus_url == 0 || this.tagStatus_url == null) {
				this.textbox.value += "[url]";
				this.tagStatus_url = 1;
				button.value = "url*";
			} else {
				this.textbox.value += "[/url]";
				this.tagStatus_url = 0;
				button.value = "url";
			}
		}
	}
}