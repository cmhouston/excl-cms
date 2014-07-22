
var wptDate = (function($) {
    var _tempConditions, _tempField;
    function init(parent) {
        if ($.isFunction($.fn.datepicker)) {
            $('input.js-wpt-date, #cred-post-expiration-datepicker', $(parent)).each(function(index) {
                if (!$(this).is(':disabled') && !$(this).hasClass('hasDatepicker')) {
                    $(this).datepicker({
                        showOn: "button",
                        buttonImage: wptDateData.buttonImage,
                        buttonImageOnly: true,
                        buttonText: wptDateData.buttonText,
                        dateFormat: wptDateData.dateFormat,
                        altFormat: wptDateData.dateFormat,
                        changeMonth: true,
                        changeYear: true,
                        yearRange: wptDateData.yearMin+':'+wptDateData.yearMax,
                        onSelect: function(dateText, inst) {
                            $(this).trigger('wptDateSelect');
                        }
                    }).next().after('<span style="margin-left:10px"><i>' + wptDateData.dateFormatNote + '</i></span>').data( 'dateFormatNote', true );
                }
            });
        }
    }
    function ajaxConditional(formID, conditions, field) {
        _tempConditions = conditions;
        _tempField = field;
        wptCallbacks.conditionalCheck.add(wptDate.ajaxCheck);
    }
    function ajaxCheck(formID) {
        wptCallbacks.conditionalCheck.remove(wptDate.ajaxCheck);
        wptCond.ajaxCheck(formID, _tempField, _tempConditions);
    }
    function ignoreConditional(val) {
        if ( '' == val ) {
            return '__ignore_negative';
        }
        return Date.parse(val);
    }
    function bindConditionalChange($trigger, func, formID) {
        $trigger.on('wptDateSelect', func);
        var lazy = _.debounce(func, 1000);
        $trigger.on('keyup', lazy);
        return false;
    }
    function triggerAjax(func){
        if ($(this).val().length >= wptDateData.dateFormatPhp.length) func();
    }
    return {
        init: init,
        ajaxConditional: ajaxConditional,
        ajaxCheck: ajaxCheck,
        ignoreConditional: ignoreConditional,
        bindConditionalChange: bindConditionalChange,
        triggerAjax: triggerAjax
    };
})(jQuery);

jQuery(document).ready(function() {
    wptDate.init('body');
    //fixing unknown Srdjan error
    jQuery('.ui-datepicker-inline').hide();
});

if ( 'undefined' != typeof(wptCallbacks) ) {
    wptCallbacks.reset.add(function(parent) {
        wptDate.init(parent);
    });
    wptCallbacks.addRepetitive.add(wptDate.init);
}

//add_action('conditional_check_date', wptDate.ajaxConditional, 10, 3);
if ( 'function' == typeof(add_filter) ) {
    add_filter('conditional_value_date', wptDate.ignoreConditional, 10, 1);
}
if ( 'function' == typeof(add_action) ) {
    add_action('conditional_trigger_bind_date', wptDate.bindConditionalChange, 10, 3);
}

