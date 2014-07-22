
//var wptCredfile = (function($) {
//    function init(selector) {
//        $('.js-wpt-credfile .js-wpt-field-item', $(selector)).each(function() {
//            var $item = $(this);
//            var $file = $('input[type="file"]', $item);
//            var $hidden = $('input[type="hidden"]', $item);
//            alert($item.attr("name"));
//            $('.js-wpt-credfile-delete-button', $item).on('click', function() {
//                $file.show().removeAttr('disabled');
//                $hidden.attr('disabled', 'disabled');
//                $('.js-wpt-credfile-preview', $item).hide();
//                $(this).remove();
//                return false;
//            });
//            $('.js-wpt-credfile-upload-file').on('change', function() {
//                var file = $file[0].files[0];
//                if (typeof file != 'undefined' && $file.is(':enabled')) {
//                    var wptFReader = new FileReader();
//                    wptFReader.readAsDataURL(file);
//                    wptFReader.onload = function(wptFREvent) {
//                        $('.js-wpt-credfile-preview', $item).attr('src', wptFREvent.target.result).show();
//                    };
//                }
//            });
//        });
//    }
//    return {
//        init: init
//    };
//})(jQuery);
//
//jQuery(document).ready(function() {
//    wptCredfile.init('body');
//});
 
function init() {}
//calling this function means that image is set so on click
//1. disable hidden 
//2. hide image
//3. enable file
//4. show file
function _cred_switch($name) {
    $idfile = $name+"_file";
    $idhidden = $name+"_hidden";
    $idimage = $name+"_image";
    $idbutton = $name+"_button";
    if (!jQuery("#"+$idfile).is(":visible")) {
        jQuery("#"+$idhidden).attr('disabled','disabled');
        jQuery("#"+$idimage).hide();
        jQuery("#"+$idfile).removeAttr('disabled');
        jQuery("#"+$idfile).show();
        jQuery("#"+$idfile).val('');
        jQuery("#"+$idbutton).val('Undo');
    } else {
        jQuery("#"+$idbutton).val('Delete');
        jQuery("#"+$idfile).val(jQuery("#"+$idfile).val());
        jQuery("#"+$idhidden).removeAttr('disabled');
        jQuery("#"+$idimage).show();
        jQuery("#"+$idfile).attr('disabled','disabled');
        jQuery("#"+$idfile).hide();
        
    }
}

jQuery(document).ready(function() {
    
});