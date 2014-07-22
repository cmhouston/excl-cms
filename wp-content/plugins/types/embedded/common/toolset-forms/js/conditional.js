/* 
 * @see WPToolset_Forms_Conditional (classes/conditional.php)
 * 
 */
var wptCondTriggers = {};
var wptCondFields = {};
var wptCondCustomTriggers = {};
var wptCondCustomFields = {};
var wptCond = (function($) {

    function init() {
        console.log(wptCondTriggers);
        _.each(wptCondTriggers, function(triggers, formID) {
            console.log(triggers);
            _.each(triggers, function(fields, trigger) {
                var $trigger = _getTrigger(trigger, formID);
                console.log($trigger);
                _bindChange(formID, $trigger, function(e) {
                    _check(formID, fields);
                });
                _check(formID, fields);
            });
        });
        _.each(wptCondCustomTriggers, function(triggers, formID) {
            _.each(triggers, function(fields, trigger) {
                var $trigger = _getTrigger(trigger, formID);
                _bindChange(formID, $trigger, function(e) {
                    _custom(formID, fields);
                });
            });
        });
        // Fire validation after init conditional
        wptCallbacks.validationInit.fire();
    }

    function _getTrigger(trigger, formID) {
        var $container = $('[data-wpt-id="' + formID.replace(/^#/, '') + '_' + trigger + '"]', formID);
        if ( $('body').hasClass('wp-admin') ) {
            $container = $('[data-wpt-id="' + trigger + '"]', formID);
        }
        var $trigger = $('.js-wpt-cond-trigger', $container);
        if ($trigger.length < 1) {
            $trigger = $(':input', $container).first();
        }
        $trigger._wptType = $container.data('wpt-type');
        return $trigger;
    }

    function _getTriggerValue($trigger, formID) {
        // Do not add specific filtering for fields here
        // Use add_filter() to apply filters from /js/$type.js
        var val = $trigger.val();
        switch( $trigger._wptType ) {
            case 'radio':
            case 'radios':
                radio = $('[name="' + $trigger.attr('name') + '"]:checked', formID);
                if ( 'undefined' == typeof( radio.data('types-value' ) ) ) {
                    val = radio.val();
                } else {
                    val = radio.data('types-value');
                }
                break;
            case 'select':
                option = $('[name="' + $trigger.attr('name') + '"] option:selected', formID);
                if ( 'undefined' == typeof( option.data('types-value' ) ) ) {
                    val = option.val();
                } else {
                    val = option.data('types-value');
                }
                break;
            case 'checkbox':
                val = $('[name="' + $trigger.attr('name') + '"]:checked', formID).val();
                break;
        }
        return val;
    }

    function _getAffected(affected, formID) {
        var $el = $('[data-wpt-id="' + affected + '"]', formID);
        if ($el.length < 1) {
            $el = $('#' + affected, formID);
        }
        return $el;
    }

    function _check(formID, fields) {
        _.each(fields, function(field) {
            var __ignore = false;
            var c = wptCondFields[formID][field];
            var passedOne = false, passedAll = true, passed = false;            
            _.each(c.conditions, function(data) {
                if (__ignore) {
                    return;
                }
                var $trigger = _getTrigger(data.id, formID);
                var val = _getTriggerValue($trigger, formID);
                val = apply_filters('conditional_value_' + $trigger._wptType, val, $trigger);

                do_action('conditional_check_' + data.type, formID, c, field);
                var operator = data.operator, _val = data.args[0];

                /**
                 * handle types
                 */
                switch(data.type) {
                    case 'date':
                        if ( _val ) {
                            _val = Date.parse(_val);
                        }
                        break;
                }


                if ('__ignore' == val ) {
                    __ignore = true;
                    return;
                }
                /**
                 * for __ignore_negative set some dummy operator
                 */
                if ( 0 && '__ignore_negative' == val ) {
                    operator = '__ignore';
                }
                switch (operator) {
                    case '===':
                    case '==':
                    case '=':
                        passed = val == _val;
                        break;
                    case '!==':
                    case '!=':
                        passed = val != _val;
                        break;
                    case '>':
                        passed = parseInt(val) > parseInt(_val);
                        break;
                    case '<':
                        passed = parseInt(val) < parseInt(_val);
                        break;
                    case '>=':
                        passed = parseInt(val) >= parseInt(_val);
                        break;
                    case '<=':
                        passed = parseInt(val) <= parseInt(_val);
                        break;
                    case 'between':
                        passed = parseInt(val) > parseInt(_val) && parseInt(val) < parseInt(data.args[1]);
                        break;
                    default:
                        passed = false;
                        break;
                }
                if (!passed) {
                    passedAll = false;
                } else {
                    passedOne = true;
                }
            });

            if (c.relation === 'AND' && passedAll) {
                passed = true;
            }
            if (c.relation === 'OR' && passedOne) {
                passed = true;
            }

            if (!__ignore) {
                _showHide(passed, _getAffected(field, formID));
            }
        });
/**
 * 2014-03-31 marcin.p
 *
 * I commended line below because this "fire" on jQuery.callback breaks other
 * scripts. See more: 
 * https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/181923121/comments
 */
//        wptCallbacks.conditionalCheck.fire(formID);
    }

    function _bindChange(formID, $trigger, func) {
        // Do not add specific binding for fields here
        // Use add_action() to bind change trigger from /js/$type.js
        // if not provided - default binding will be performed
        var binded = do_action('conditional_trigger_bind_' + $trigger._wptType, $trigger, func, formID);
        if (!binded) {
            switch( $trigger._wptType ) {
                case 'checkbox':
                    $trigger.on('click', func);
                    break;
                case 'radio':
                case 'radios':
                    $('[name="' + $trigger.attr('name') + '"]').on('click', func);
                    break;
                case 'select':
                    $trigger.on('change', func);
                    break;
                default:
                    $($trigger).on('blur', func);
            }
        }
    }

    function _custom(formID, fields) {
        var data = {action: 'wptoolset_custom_conditional', 'conditions': {}, 'values': {}, 'field_types': {}};
        _.each(fields, function(field) {
            var c = wptCondCustomFields[formID][field];
            data.conditions[field] = c.custom;
            _.each(c.triggers, function(t) {
                var $trigger = _getTrigger(t);
                data.values[t] = _getTriggerValue($trigger);
                data.field_types[t] = $trigger._wptType;
            });
        });
        $.post(ajaxurl, data, function(res) {
            _.each(res.passed, function(affected) {
                _showHide(true, _getAffected(affected, formID));
            });
            _.each(res.failed, function(affected) {
                _showHide(false, _getAffected(affected, formID));
            });
            wptCallbacks.conditionalCheck.fire(formID);
        }, 'json').fail(function(data) {
            alert(data.responseText);
        });
    }

    function _showHide(show, $el) {
        if (show) {
            $el.slideDown().removeClass('js-wpt-remove-on-submit js-wpt-validation-ignore');
        } else {
            $el.slideUp().addClass('js-wpt-remove-on-submit js-wpt-validation-ignore');
        }
    }

    function ajaxCheck(formID, field, conditions) {
        var values = {};
        _.each(conditions.conditions, function(c) {
            var $trigger = _getTrigger(c.id, formID);
            values[c.id] = _getTriggerValue($trigger);
        });
        var data = {
            'action': 'wptoolset_conditional',
            'conditions': conditions,
            'values': values
        };
        $.post(ajaxurl, data, function(passed) {
            _showHide(passed, _getAffected(field, formID));
            wptCallbacks.conditionalCheck.fire(formID);
        }).fail(function(data) {
            alert(data);
        });
    }

    function addConditionals(data) {
        _.each(data, function(c, formID) {
            if (typeof c.triggers != 'undefined'
                    && typeof wptCondTriggers[formID] != 'undefined') {
                _.each(c.triggers, function(fields, trigger) {
                    wptCondTriggers[formID][trigger] = fields;
                    var $trigger = _getTrigger(trigger, formID);
                    _bindChange(formID, $trigger, function() {
                        _check(formID, fields);
                    });
                });
            }
            if (typeof c.fields != 'undefined'
                    && typeof wptCondFields[formID] != 'undefined') {
                _.each(c.fields, function(conditionals, field) {
                    wptCondFields[formID][field] = conditionals;
                });
            }
            if (typeof c.custom_triggers != 'undefined'
                    && typeof wptCondCustomTriggers[formID] != 'undefined') {
                _.each(c.custom_triggers, function(fields, trigger) {
                    wptCondCustomTriggers[formID][trigger] = fields;
                    var $trigger = _getTrigger(trigger, formID);
                    _bindChange(formID, $trigger, function() {
                        _custom(formID, fields);
                    });
                });
            }
            if (typeof c.custom_fields != 'undefined'
                    && typeof wptCondCustomFields[formID] != 'undefined') {
                _.each(c.custom_fields, function(conditionals, field) {
                    wptCondCustomFields[formID][field] = conditionals;
                });
            }
        });
    }

    return {
        init: init,
        ajaxCheck: ajaxCheck,
        addConditionals: addConditionals
    };

})(jQuery);

