$(function () {

    var gameOptions=null;
    try{
        gameOptions=JSON.parse($('#gameOptions').html());
    }catch(e){}

    if(gameOptions){
        //jquery helper pseudoselectors
        $.expr[':'].equals =  $.expr[':'].equals || $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text()==arg;
            };
        });

        $.expr[':'].paired =  $.expr[':'].paired || $.expr.createPseudo(function() {
            return function( elem ) {
                var pThis=$(elem);
                var thisVal=pThis.text();
                return $('i:equals("'+thisVal+'")',pThis.closest('.parsedRolls')).length>1;
            };
        });

        $.expr[':'].d100double =  $.expr[':'].d100double || $.expr.createPseudo(function() {
            return function( elem ) {
                var thisVal=parseInt($(elem).text());
                return ((thisVal%11)==0)||(thisVal==100);
            };
        });

        //toggle ordering between original and numeric
        var orderRolls=function(parsedRolls){
            if(parsedRolls.hasClass('rollsOrderedByVal')){
                $('i', parsedRolls).sort(function (a, b) { return parseInt($(a).data('rollorder'))-parseInt($(b).data('rollorder')); }).appendTo(parsedRolls);
            }else{
                $('i', parsedRolls).sort(function (a, b) { return parseInt($(a).text())-parseInt($(b).text()); }).appendTo(parsedRolls);
            }
            parsedRolls.toggleClass('rollsOrderedByVal');
        };

        //apply dice rules
        var applyDiceRules=function(parsedRolls){
            var rollstring=parsedRolls.data('rollstring');

            for (var count = 0; count < gameOptions.diceRules.length; count++) {
                var rule = gameOptions.diceRules[count];
                if(rollstring && rule.rolled &&  rollstring.toLowerCase().indexOf(rule.rolled.toLowerCase())!=-1){
                    //rules highlighting
                    if(rule.highlight){
                        var highlightClass=rule.highlight.split(" ").map(function(item) {
                            return 'rollVal-'+item.trim().toLowerCase();
                        }).join(' ');

                        var selectorSuffix='';

                        //last die
                        if(rule.lastDie) {
                            selectorSuffix+=':last';
                        }

                        //natural values
                        if(rule.natural) {
                            selectorSuffix+=':equals('+rule.natural+')';
                        }

                        //check for paired dice
                        if(rule.paired) {
                            selectorSuffix+=':paired';
                        }

                        //d100 doubles (11,22,..,99,100)
                        if(rule.d100double) {
                            selectorSuffix+=':d100double';
                        }

                        $('i'+selectorSuffix,parsedRolls).addClass(highlightClass);
                    }

                    //other rule options
                    if(rule.autoSort){
                        orderRolls(parsedRolls);
                    }
                }
            }
        };

        //convert the text into spans and apply the rules
        $('.rollValues').each(function(){
            var pThis=$(this);
            var rolledNumbers=pThis.text().split(",").map(function(item) { return item.trim(); });

            pThis.html('');
            var natOrder=1;
            for (var count = 0; count < rolledNumbers.length; count++){
                var roll = rolledNumbers[count];
                $('<i></i>').text(roll).addClass('rval'+roll).data('rollorder',natOrder++).appendTo(pThis);
            }

            pThis.addClass('parsedRolls').on('click',function(){orderRolls($(this));});

            if(gameOptions.diceRules){
                applyDiceRules(pThis);
            }
        });
    }
});