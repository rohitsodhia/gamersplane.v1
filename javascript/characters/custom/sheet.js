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

        updateField(pFormValue.data('formfieldidx'),val);
        charSheet.updateCalculations();
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

        var addToQueue=function(obj){
            queue.push(obj);
            if(queue.length==1){
                processQueue();
            }
        };

        var processQueue=function(){
            var obj=queue[0];
            $.ajax({type: 'post', url: API_HOST +'/characters/bbformUpdateVal', xhrFields: {withCredentials: true}, data:obj,
                complete:function (data)
                {
                    queue.shift();
                    if(queue.length>0){
                        processQueue();
                    }
                }
            });
        };

        return {addToQueue:addToQueue};
    })();

    function updateField(fieldIdx, value){
        ajaxQueue.addToQueue({ charID: $('#characterID').val(), fieldIdx:fieldIdx, fieldValue:value});
    }
});