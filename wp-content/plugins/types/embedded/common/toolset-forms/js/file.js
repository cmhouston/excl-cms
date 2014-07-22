
var wptFile = (function($, w) {
    var $item, $parent, $preview;
    function init() {
        $('.js-wpt-field').on('click', 'a.js-wpt-file-upload', function() {
            height = $('body').height()-20;
            if ( 800 < height ) {
                height = 800;
            }
            width = $('body').width()-20;
            if ( 670 < width ) {
                width = 670;
            }
            $item = $(this).parents('.js-wpt-field-item');
            $parent = $item.parents('.js-wpt-field');
            $preview = $('.js-wpt-file-preview', $item);
            tb_show(wptFileData.title, wptFileData.adminurl + 'media-upload.php?' + wptFileData.for_post + 'type=file&context=wpt-fields-media-insert&wpt[id]=' + $parent.data('wpt-id') + '&wpt[type]=' + $parent.data('wpt-type') + '&TB_iframe=true&width='+width+'&height='+height);
            return false;
        });
    }
    function mediaInsert(url, type) {
        $(':input', $item).first().val(url);
        if (type == 'image') {
            $preview.html('<img src="' + url + '" />');
        } else {
            $preview.html('');
        }
        tb_remove();
    }
    function mediaInsertTrigger(guid, type) {
        window.parent.wptFile.mediaInsert(guid, type);
        window.parent.jQuery('#TB_closeWindowButton').trigger('click');
    }
    return {
        init: init,
        mediaInsert: mediaInsert,
        mediaInsertTrigger: mediaInsertTrigger
    };
})(jQuery);

jQuery(document).ready(wptFile.init);
