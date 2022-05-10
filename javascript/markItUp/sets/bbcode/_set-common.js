var imgurUpload = function (miu) {
	$('#miuImageUpload').remove();
	var imgFileSelector = $('<input type="file" id="miuImageUpload" accept="image/png, image/gif, image/jpeg" style="display:none;"/>').appendTo($(document.body));

	imgFileSelector.on('change', function () {
		var pThis = $(this);
		var files = pThis.get(0).files;
		ImgurHelper.uploadFiles(files[0],function(link){$.markItUp({ replaceWith: '[img]' + link+ '[/img]' });});
	});

	imgFileSelector.click();

};

$(function () {
	$('textarea#messageTextArea,textarea.markItUp').on('paste',function(ev){
		ImgurHelper.uploadFromClipboard(ev, function(link){$.markItUp({ replaceWith: '[img]' + link+ '[/img]' });});
	});

	$('#content').on('click','.miuDlg-flags span',function(){$(this).toggleClass('sel');});
	$('#content').on('click','.miuDlg-select span',function(){ $(this).siblings().removeClass('sel'); $(this).toggleClass('sel');});
	$('#content').on('click','.miuDlgOptions-format .miuAttributeSelector span',function(){
		var previewClass='';
		var previewStyle='';
		$('.miuAttributeSelector .sel').each(function(){
			var attr=$(this).data('attrval');
			(attr.indexOf(':')==-1)?(previewClass+=(' gpFormat-'+attr)):(previewStyle+=(';'+attr));
		});
		$('.miuDlgPreviewContent').attr('class','miuDlgPreviewContent userColor '+previewClass);
		$('.miuDlgPreviewContent').attr('style',previewStyle);
		$('.miuDlgPreview').darkModeColorize();
	});
	$('#content').on('click','.miuDlgOptions-table .miuAttributeSelector span',function(){
		var contentPreviewTable=$('.miuDlgPreviewContent').attr('class','miuDlgPreviewContent bbTable '+$('.miuAttributeSelector .sel').map(function() { return $(this).data('previewclass'); }).get().join(' '));
		$('td:eq(7)',contentPreviewTable).html('1d4+2');
		$('td:eq(8)',contentPreviewTable).html('+4');
		if(contentPreviewTable.hasClass('bbTableRolls'))
		{
			$('td:eq(7)',contentPreviewTable).html('<span class="rollDice">1d4+2</span>');
			if(contentPreviewTable.hasClass('bbTableD20')){
				if(contentPreviewTable.hasClass('bbTableDnd5e')){
					$('td:eq(8)',contentPreviewTable).html('<span class="rolld20-5e"><span class="rollDice rollDiceD20">+4</span><span class="rollDice rollDiceD20">A</span><span class="rollDice rollDiceD20">D</span></span>');
				} else {
					$('td:eq(8)',contentPreviewTable).html('<span class="rollDice">+4</span>');
				}
			}
		}
	});

	$('#content').on('click','.miuDlgCancel',function(){$('#markitUpBlocker,#markitUpDlg').remove();});
	$('body').on('click','#markitUpBlocker',function(){$('#markitUpBlocker,#markitUpDlg').remove();});
	$('#content').on('click','.miuDlgOK',function(){
		var dlg=$('#markitUpDlg');
		var openTag='['+dlg.data('opentag');
		openTag+=$('.miuAttributeSelector .sel').map(function() { return $(this).data('attrval'); }).get().join(' ');
		openTag+=']';
		var closeTag='[/'+dlg.data('closetag')+']';
		if(dlg.data('replmode')){
			$.markItUp({ replaceWith: openTag+dlg.data('sel')+closeTag });
		}
		else{
			$.markItUp({ openWith: openTag , closeWith: closeTag });
		}

		$('#markitUpBlocker,#markitUpDlg').remove();}
	);

	var showMiuDlg=function(dlgTitle,pThis,opt){
		$('<div id="markitUpBlocker"></div>').appendTo("body");
		var dlg=$('<div id="markitUpDlg"><h2></h2><div class="miuDlgOptions"></div><div class="miuDlgPreview" style="display:none;"></div><div class="miuDlgButtons"><button class="fancyButton miuDlgOK" type="button">OK</button><button class="fancyButton miuDlgCancel" type="button">Cancel</button></div></div>').appendTo(pThis.closest('.markItUpHeader'));
		$('h2',dlg).text(dlgTitle);
		dlg.data('opentag',opt.openTag);
		dlg.data('closetag',opt.closeTag);
		dlg.data('replmode',opt.replMode);
		dlg.data('sel',opt.sel);
		dlg.addClass('miuDlgOptions-'+opt.dlgType);
		if(opt.previewHtml){
			$('.miuDlgPreview').html(opt.previewHtml).show();
		}
		for(var i=0;i<opt.options.length;i++){
			var optionSet=opt.options[i];
			var optSetDiv=$('<div class="miuDlgOptSet"><label></label></div>').appendTo($('.miuDlgOptions',dlg));
			$('label',optSetDiv).text(optionSet.label+':');
			var flagsDiv=$('<div></div>"').addClass('miuAttributeSelector').addClass('miuDlg-'+optionSet.type).appendTo(optSetDiv);
			if(optionSet.subtype=='color' || optionSet.subtype=='backgroundcolor'){
				flagsDiv.addClass('miuColorWell');
			}

			for(var j=0;j<optionSet.options.length;j++){
				if(optionSet.subtype=='color'){
					$('<span></span>').css('background-color',optionSet.options[j]).data('attrval','color:'+optionSet.options[j]).appendTo(flagsDiv);
				} else if(optionSet.subtype=='backgroundcolor'){
					$('<span></span>').css('background-color',optionSet.options[j]).data('attrval','background-color:'+optionSet.options[j]).appendTo(flagsDiv);
				} else if(optionSet.subtype=='font'){
					$('<span></span>').text(optionSet.options[j]).addClass('gpFormat-font-'+optionSet.options[j]).data('attrval','font-'+optionSet.options[j]).appendTo(flagsDiv);
				} else {
					if(optionSet.options[j].label){
						var spanSelector=$('<span></span>').text(optionSet.options[j].label).attr('title',optionSet.options[j].title).data('attrval',optionSet.options[j].label).appendTo(flagsDiv);
						if(optionSet.options[j].previewClass){
							spanSelector.data('previewclass',optionSet.options[j].previewClass);
						}
					} else {
						$('<span></span>').text(optionSet.options[j]).data('attrval',optionSet.options[j]).appendTo(flagsDiv);
					}
				}
			}
		}
		$('.miuColorWell span',dlg).addClass('userColor');
		dlg.darkModeColorize();


		if(opt.selectOptions){
			$('.miuAttributeSelector span').each(function(){
				var pSpan=$(this);
				if(opt.selectOptions.includes(pSpan.data('attrval'))){
					pSpan.click();
				}
			});
		}
	};

	var getExistingSelection=function(pThis,startRegEx,endRegEx){
		var ret={selectOptions:null,selectOptions2:null,sel:null,existingSel:false};
		var miuTextarea=$('textarea',pThis.closest('.markItUpContainer'));
		var start = miuTextarea[0].selectionStart;
		var finish = miuTextarea[0].selectionEnd;
		var sel = $.trim(miuTextarea.val().substring(start, finish));

		var foundStartFormatting =startRegEx.exec(sel);
		var foundEndFormatting = endRegEx.exec(sel);

		if(foundStartFormatting && foundEndFormatting){
			ret.selectOptions=foundStartFormatting[1].split(' ');
			if(foundStartFormatting.length>2){
				ret.selectOptions2=foundStartFormatting[2].split(' ');
			}
			ret.selectOptions=foundStartFormatting[1].split(' ');
			ret.sel=sel.substring(foundStartFormatting[0].length,sel.length-foundEndFormatting[0].length);
			ret.existingSel=true;
		}

		return ret;
	}

	$('#content').on('click','.miuBtnFormat',function(){
		var pThis=$(this);
		var sel=getExistingSelection(pThis,(/^\s*\[f\=?\"?([^\"\]]*)\"?\]/gm),(/\[\/f\]\s*$/gm));

		var showFormatDlg=function(previewHtml)
		{
			if($.trim(previewHtml).length==0){
				previewHtml='Lorem ipsum dolor sit amet, consectetur adipiscing elit.<br/>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
			}

			showMiuDlg('Formatting options',pThis,{
				dlgType:'format',
				openTag:'f=',
				closeTag:'f',
				selectOptions:sel.selectOptions,
				replMode:sel.existingSel,
				sel:sel.sel,
				previewHtml:'<span class="miuDlgPreviewContent">'+previewHtml+'</span>',
				options:[{type:'select',subtype:'font',label:'Font',options:['opensans','ostrich','cursive','agency','neuropol','kelt','dumbledor','aniron','aladin','blackops','cabin','eagle','goudy','gugi','iceland','fell','nightshade','start','quint','elite','stalinist']},
					{type:'select',label:'Size',options:['tiny','small','large','huge','gargantuan']},
					{type:'select',subtype:'color',label:'Color',options:['black','silver','gray','white','maroon','red','purple','fuchsia','green','lime','olive','yellow','navy','blue','teal','aqua']},
					{type:'select',subtype:'backgroundcolor',label:'Back',options:['black','silver','gray','white','maroon','red','purple','fuchsia','green','lime','olive','yellow','navy','blue','teal','aqua']},
					{type:'select',label:'Spacing',options:['extra-space','double-space']},
					{type:'flags',label:'Style',options:['italic','bold','border','strikethrough']},
					{type:'select',label:'Alignment',options:['centre','right']},
					{type:'select',label:'Floating',options:['float-left','float-right']},
					{type:'select',label:'Heading',options:['h1','h2','h3']},
					{type:'select',label:'Fancy',options:['fancy-vdu','fancy-paper','fancy-parchment']}
				]
			});
		}

		if(sel.sel && sel.sel.length>0){
			$.ajax({type: 'post',url: API_HOST +'/forums/getPostPreview',xhrFields: {withCredentials: true},
			data:{ postText: sel.sel, postAsId: 0, postAsName: ''},
			success:function (data) {
				showFormatDlg(data.post);
			}
			});
		}
		else {
			showFormatDlg('');
		}

	});
	$('#content').on('click','.miuBtnPoll',function(){
		var pThis=$(this);
		var sel=getExistingSelection(pThis,(/^\s*\[poll=\"?(.*?)?\"([^\]]*)\]/gm),(/\[\/poll\]\s*$/gm));

		showMiuDlg('Poll options',$(this),{
			dlgType:'poll',
			openTag:'poll="'+(sel.selectOptions?(sel.selectOptions.join(' ')):'Your question here...')+'" ',
			closeTag:'poll',
			selectOptions:sel.selectOptions2,
			replMode:sel.existingSel,
			sel:sel.sel,
			options:[{type:'flags',label:'Poll options',options:[{label:'show',title:'Show the results before voting'},{label:'multi',title:'Allow multiple votes'},{label:'public',title:'Show avatar of voter'}]}]
		});
	});
	$('#content').on('click','.miuBtnTable',function(){
		var pThis=$(this);
		var sel=getExistingSelection(pThis,(/^\s*\[table\=?\"?([^\"\]]*)\"?\]/gm),(/\[\/table\]\s*$/gm));
		showMiuDlg('Table options',$(this),{
			dlgType:'table',
			openTag:'table=',
			closeTag:'table',
			selectOptions:sel.selectOptions,
			replMode:sel.existingSel,
			sel:sel.sel,
			previewHtml:'<table class="miuDlgPreviewContent bbTable"><tr><td>Lorem</td><td>ipsum</td><td>dolor</td></tr><tr><td>sit</td><td>amet</td><td>consectetur</td></tr><tr><td>adipiscing</td><td>1d4+2</td><td>+4</td></tr></table>',
			options:[{type:'select',label:'Layout',options:[{label:'center',title:'', previewClass:'bbTableCenter'},{label:'right',title:'', previewClass:'bbTableRight'},{label:'stats',title:'', previewClass:'bbTableStats'},{label:'htl',title:'', previewClass:'bbTable-htl'},{label:'ht',title:'', previewClass:'bbTable-ht'},{label:'hl',title:'', previewClass:'bbTable-hl'}]},
					{type:'flags',label:'Rollers',options:[{label:'rolls',title:'', previewClass:'bbTableRolls'},{label:'d20',title:'', previewClass:'bbTableD20'},{label:'dnd5e',title:'', previewClass:'bbTableDnd5e'},{label:'pool',title:'', previewClass:''},{label:'pool-add',title:'', previewClass:''}]},
					{type:'flags',label:'Style',options:[{label:'grid',title:'', previewClass:'bbTableGrid'},{label:'zebra',title:'', previewClass:'bbTableZebra'},{label:'compact',title:'', previewClass:'bbTableCompact'}]},
			]
		});
	});

});

var mobileifyMenus=function(settings){
	if($('#mainMenuTools:visible').length==0){
		settings.isMobile=true;
		for(var i=0;i<settings.markupSet.length;i++){
			var menuOpt=settings.markupSet[i];
			if(menuOpt.name=='Color'){
				menuOpt.dropMenu.push({name:'Color...',openWith: '[color="[![Text color]!]"]', closeWith: '[/color]'});
				menuOpt.openWith=null;
				menuOpt.closeWith=null;
			}
			else if(menuOpt.name=='Image'){
				menuOpt.replaceWith=null;
			}
			else if(menuOpt.name=='Note' && menuOpt.dropMenu){
				menuOpt.dropMenu.push({name:'Note...',openWith:'[note="[![User(s)]!]"]', closeWith:'[/note]', className: 'otherNoteAdd'});
				menuOpt.openWith=null;
				menuOpt.closeWith=null;
			}
		}
	}

};