/* 
 * Repetitive JS.
 */
var wptRep = (function($) {
    var count = {};
    function init() {
        // Reorder elements if repetitive
        $('.js-wpt-repetitive').each(function() {
            var $this = $(this), title = $('label', $this).first().clone();
            var description = $('.description', $this).first().clone();
            $('.js-wpt-field-item', $this).each(function() {
                $('label', $this).first().remove();
                $('.description', $this).first().remove();
            });
            $(this).prepend(description).prepend(title);
            _toggleCtl($this);
        });
        // Add field
        $('.js-wpt-repadd').on('click', function() {
            var $this = $(this), $parent = $this.parents('.js-wpt-repetitive');
            var tpl = $('<div>' + $('#tpl-wpt-field-' + $parent.data('wpt-id')).html() + '</div>');
            $('[id]', tpl).each(function() {
                var $this = $(this), uniqueId = _.uniqueId('wpt-form-el');
                tpl.find('label[for="' + $this.attr('id') + '"]').attr('for', uniqueId);
                $this.attr('id', uniqueId);
            });
            $('label', tpl).first().remove();
            $('.description', tpl).first().remove();
            var _count = tpl.html().match(/\[%%(\d+)%%\]/);
            if (_count != null) {
                _count = _countIt(_count[1], $parent.data('wpt-id'));
            } else {
                _count = '';
            }
            $('.js-wpt-field-items', $parent).append(tpl.html().replace(/\[%%(\d+)%%\]/g, '[' + _count + ']'));
            wptCallbacks.addRepetitive.fire($parent);
            _toggleCtl($parent);

            return false;
        });
        // Delete field
        $('.js-wpt-field').on('click', '.js-wpt-repdelete', function() {
            var $this = $(this), $parent = $this.parents('.js-wpt-field');
            var value = $this.parent().parent().find('input').val();
            // Allow deleting if more than one field item
            if ($('.js-wpt-field-item', $parent).length > 1) {
                var formID = $this.parents('form').attr('id');
                $this.parents('.js-wpt-field-item').remove();
                wptCallbacks.removeRepetitive.fire(formID);
            }
            _toggleCtl($parent);
            /**
             * if image, try delete images
             */
            if ( 'image' == $parent.data('wpt-type') ) {
                $parent.parent().append(
                    '<input type="hidden" name="wpcf[delete-image][]" value="'
                    + value
                    + '"/>'
                    );
            }
            return false;
        });
    }
    function _toggleCtl($parent) {
        var $sortable = $('.js-wpt-field-items', $parent);
        if ($('.js-wpt-field-item', $parent).length > 1) {
            $('.js-wpt-repdelete', $parent).removeAttr('disabled');
            $('.js-wpt-repdrag', $parent).css({opacity: 1, cursor: 'move'});
            if (!$sortable.hasClass('ui-sortable')) {
                $sortable.sortable({
                    revert: true,
                    handle: '.js-wpt-repdrag',
                    axis: 'y',
                    cursor: 'move'
                });
            }
        } else {
            $('.js-wpt-repdelete', $parent).attr('disabled', 'disabled');
            $('.js-wpt-repdrag', $parent).css({opacity: 0.5, cursor: 'default'});
            if ($sortable.hasClass('ui-sortable')) {
                $sortable.sortable('destroy');
            }
        }
    }
    function _countIt(_count, id) {
        if (typeof count[id] == 'undefined') {
            count[id] = _count;
            return _count;
        }
        return ++count[id];
    }
    return {
        init: init
    };
})(jQuery);

jQuery(document).ready(wptRep.init);
