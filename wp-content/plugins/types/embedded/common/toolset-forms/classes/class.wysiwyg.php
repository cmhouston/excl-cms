<?php
require_once 'class.textarea.php';

/**
 * Description of class
 *
 * @author Srdjan
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/classes/class.wysiwyg.php $
 * $LastChangedDate: 2014-04-14 16:17:33 +0200 (Mon, 14 Apr 2014) $
 * $LastChangedRevision: 21441 $
 * $LastChangedBy: marcin $
 *
 */
class WPToolset_Field_Wysiwyg extends WPToolset_Field_Textarea
{

    protected $_settings = array('min_wp_version' => '3.3');

    public function metaform()
    {
        $form = array();
        $form[] = array(
            '#type' => 'markup',
            '#markup' => $this->getTitle() . $this->getDescription() . $this->_editor(),
        );
        return $form;
    }

    protected function _editor()
    {
        ob_start();
        wp_editor( $this->getValue(), $this->getId(),
            array(
                'wpautop' => true, // use wpautop?
                'media_buttons' => $this->_data['has_media_button'], // show insert/upload button(s)
                'textarea_name' => $this->getName(), // set the textarea name to something different, square brackets [] can be used here
                'textarea_rows' => get_option( 'default_post_edit_rows', 10 ), // rows="..."
                'tabindex' => '',
                'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
                'editor_class' => 'wpt-wysiwyg', // add extra class(es) to the editor textarea
                'teeny' => false, // output the minimal editor config used in Press This
                'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
                'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
                'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
            ) );
        return ob_get_clean() . "\n\n";
    }

}
