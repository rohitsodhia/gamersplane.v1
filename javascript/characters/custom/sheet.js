$(function () {
    $('body').on('click','.formText',function(){
        var pThis=$(this);
        var curText=pThis.text();
        var curWidth=pThis.width();
        if(parseInt(curText)!=curText){
            curWidth=Math.max(curWidth,100);
            curWidth=Math.min(curWidth,200);
        }
        pThis.text('');
        var input=$('<input type="text"/>').val(curText).css({"width":curWidth+20}).appendTo(pThis);
        input.focus();
    });


    $('body').on('blur','.formText input',function(){
        var pThis=$(this);
        var pFormValue=$(this).closest('.formVal');
        var val=pThis.val();
        var charSheet=$(this).closest('.customChar');
        pThis.remove();
        pFormValue.text(val);

        updateField(pFormValue.data('formfieldidx'),val,isDiceRoll(val)?function(){charSheet.trigger('gp.sheetUpdated');}:null);
        charSheet.updateCalculations();
    });

    $('body').on('keyup keypress','.formText input',function(e){
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            $(this).blur();
        }
    });

    $('body').on('click','.formBlock h2 .ra-quill-ink',function(){
        var pThis=$(this);
        var block=pThis.closest('.formBlock');
        var rendered=$('.formBlockRendered',block);
        var bbcode=$('.formBlockBBCode',block);
        var textArea=$('<textarea></textarea>').width(rendered.width()).height(Math.max(rendered.height(),200)).val($.trim(bbcode.text())).appendTo(block).focus();
        rendered.hide();
    });

    $('body').on('blur','.formBlock textarea',function(){
        var pThis=$(this);
        var pFormBlock=$(this).closest('.formBlock');
        var val=pThis.val();
        var charSheet=$(this).closest('.customChar');
        pThis.remove();
        $('.formBlockRendered',pFormBlock).show();

        updateBlock(pFormBlock.data('blockfieldidx'),val,function(data){charSheet.html(data.notes); charSheet.updateCalculations(); charSheet.trigger('gp.sheetUpdated');});
    });


    $('body').on('click','.formCheck input',function(){
        var checkArea=$(this).closest('.formCheck');
        var val=$('input:checked',checkArea).length+'/'+$('input',checkArea).length;

        updateField(checkArea.data('formfieldidx'),val);
        $(this).closest('.customChar').updateCalculations();
    });

    jQuery.fn.updateCalculations = function (){
        var variables={};
        var calcInit=$(this).hasClass('calculationsInitialised');
        $(this).addClass('calculationsInitialised');
        var requiresRefresh=false;
        $('.formVar,.formCalc').each(function(){
            var pThis=$(this);
            if(pThis.hasClass('formCalc')) {
                var formula=pThis.data('varcalc');
                var isModifier=formula.startsWith('+');
                var variableNames=Object.getOwnPropertyNames(variables).sort(function(a, b){return b.length - a.length;});
                for(var i=0;i<variableNames.length;i++){
                    formula=formula.replace(variableNames[i],variables[variableNames[i]]);
                }
                var newVal=expressionParse(formula);
                var curText=pThis.text();
                var newText=((newVal>=0 && isModifier)?"+":"")+newVal;
                pThis.text(newText);
                if(calcInit && newText!=curText){
                    if(pThis.hasClass('updatedCalc1')){
                        pThis.removeClass('updatedCalc1').addClass('updatedCalc2')
                    }else{
                        pThis.removeClass('updatedCalc2').addClass('updatedCalc1')
                    }

                    if(isDiceRoll(newText)){
                        requiresRefresh=true;
                    }
                }
            }

            if(pThis.hasClass('formVar')){
                if(pThis.hasClass('formCheck')){
                    variables[pThis.data('varname')]=$('input:checked',pThis).length;
                } else {
                    variables[pThis.data('varname')]=parseInt(pThis.text());
                }
            }
        });

        if(requiresRefresh){
            OnQueueEmpty(function(){$('div.customChar').trigger('gp.sheetUpdated');});
        }
    };

    $('#charDetails div.customChar').updateCalculations();

    ////helpers
    function expressionParse(expression) {
        return Function(`"use strict";
                        var d20bonus=function(val){return Math.floor((val-10)/2);};
                        var ceil=function(val){return Math.ceil(val);};
                        var floor=function(val){return Math.floor(val);};
                        return (`
                         + expression
                         + ');')();
    }

    //queue the ajax requests so we don't end up in a mess
    var ajaxQueue=(function(){
        var queue=[];
        var blockCalls=false;

        var addToQueue=function(item){
            if(!blockCalls){
                queue.push(item);
                if(queue.length==1){
                    $('#headerBG').css({"backgroundColor":'#fdd'});
                    processQueue();
                    if(item.blocking){
                        blockCalls=true;
                        $('<div id="blockingCalls"></div>').appendTo('body');
                    }
                }
            }
        };

        var processQueue=function(){
            var obj=queue[0].obj;
            $.ajax({type: 'post', url: API_HOST +queue[0].api, xhrFields: {withCredentials: true}, data:obj,
                complete:function (data)
                {
                    var fromQueue=queue.shift();
                    if(queue.length>0){
                        processQueue();
                    }else{
                        $('#headerBG').css({"backgroundColor":'#ddd'});
                        $('#blockingCalls').remove();
                        blockCalls=false;
                        if(fromQueue.onComplete){
                            fromQueue.onComplete(data.responseJSON);
                        }
                        if(onQueueEmpty){
                            onQueueEmpty();
                            onQueueEmpty=null;
                        }
                    }
                }
            });
        };

        var onQueueEmpty=null;
        var addQueueComplete=function(onEmpty){
            if(queue.length==0 && onEmpty){
                onEmpty();
            } else {
                onQueueEmpty=onEmpty;
            }
        };

        return {addToQueue:addToQueue,addQueueComplete:addQueueComplete};
    })();

    function updateField(fieldIdx, value, onComplete){
        ajaxQueue.addToQueue({api: '/characters/bbformUpdateVal', obj:{ charID: $('#characterID').val(), fieldIdx:fieldIdx, fieldValue:value, onComplete:onComplete}});
    }

    function updateBlock(blockIdx, value, onComplete){
        ajaxQueue.addToQueue({api: '/characters/bbformUpdateBlock', obj:{ charID: $('#characterID').val(), blockIdx:blockIdx, fieldValue:value}, blocking:true, onComplete:onComplete});
    }

    function OnQueueEmpty(onComplete){
        ajaxQueue.addQueueComplete(onComplete);
    }


    function isDiceRoll(val){
        return (/^[\+\-](\d)+$/.test(val))||/(\d*)[dD](\d+)([+-]\d+)?/g.test(val);
    }

});