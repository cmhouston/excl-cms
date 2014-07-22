
var wptCallbacks = {};
wptCallbacks.validationInit = jQuery.Callbacks('unique');
wptCallbacks.addRepetitive = jQuery.Callbacks('unique');
wptCallbacks.removeRepetitive = jQuery.Callbacks('unique');
wptCallbacks.conditionalCheck = jQuery.Callbacks('unique');
wptCallbacks.reset = jQuery.Callbacks('unique');

jQuery(document).ready(function() {
    if (typeof wptValidation !== 'undefined') {
        wptCallbacks.validationInit.add(function() {
            wptValidation.init();
        });
    }
    if (typeof wptCond !== 'undefined') {
        wptCond.init();
    } else {
        wptCallbacks.validationInit.fire();
    }
});


var wptFilters = {};
function add_filter(name, callback, priority, args_num) {
    var args = _.defaults(arguments, ['', '', 10, 2]);
    if (typeof wptFilters[name] === 'undefined')
        wptFilters[name] = {};
    if (typeof wptFilters[name][args[2]] === 'undefined')
        wptFilters[name][args[2]] = [];
    wptFilters[name][args[2]].push([callback, args[3]]);
}
function apply_filters(name, val) {
    if (typeof wptFilters[name] === 'undefined')
        return val;
    var args = _.rest(_.toArray(arguments));
    _.each(wptFilters[name], function(funcs, priority) {
        _.each(funcs, function($callback) {
            var _args = args.slice(0, $callback[1]);
            args[0] = $callback[0].apply(null, _args);
        });
    });
    return args[0];
}
function add_action(name, callback, priority, args_num) {
    add_filter.apply(null, arguments);
}
function do_action(name) {
    if (typeof wptFilters[name] === 'undefined')
        return false;
    var args = _.rest(_.toArray(arguments));
    _.each(wptFilters[name], function(funcs, priority) {
        _.each(funcs, function($callback) {
            var _args = args.slice(0, $callback[1]);
            $callback[0].apply(null, _args);
        });
    });
    return true;
}