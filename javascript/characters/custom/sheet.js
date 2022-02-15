$(function () {
    $('.customChar').on('click','.formText',function(){
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

    $('.customChar').on('blur','.formText input',function(){
        var pThis=$(this);
        var pFormValue=$(this).closest('.formVal');
        var val=pThis.val();
        pThis.remove();
        pFormValue.text(val);

        updateField(pFormValue.data('formfieldidx'),val);
        updateCalculations();
    });

    $('.customChar').on('click','.formCheck input',function(){
        var checkArea=$(this).closest('.formCheck');
        var val=$('input:checked',checkArea).length+'/'+$('input',checkArea).length;

        updateField(checkArea.data('formfieldidx'),val);
        updateCalculations();
    });

    var calcInit=false;
    var updateCalculations=function (){
        var variables={};
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
        calcInit=true;
    };

    updateCalculations();

    ////helpers
    function expressionParse(expression) {
        return Function('"use strict";var d20bonus=function(val){return Math.floor((val-10)/2);}; return (' + expression + ');')();
    }

    function updateField(fieldIdx, value){
        $.ajax({type: 'post', url: API_HOST +'/characters/bbformUpdateVal', xhrFields: {withCredentials: true},
            data:{ charID: $('#characterID').val(), fieldIdx:fieldIdx, fieldValue:value},
            success:function (data) {}
        });
    }
});