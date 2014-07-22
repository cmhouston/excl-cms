<?php
require_once 'class.textfield.php';

class WPToolset_Field_Skype extends WPToolset_Field_Textfield
{

    protected $_defaults = array('skypename' => '', 'button_style' => 'btn2');

    public function init(){
        wp_register_script( 'wptoolset-field-skype',
                WPTOOLSET_FORMS_RELPATH . '/js/skype.js', array('jquery'),
                WPTOOLSET_FORMS_VERSION, true );
        wp_register_style( 'wptoolset-field-skype',
                WPTOOLSET_FORMS_RELPATH . '/css/skype.css', array(),
                WPTOOLSET_FORMS_VERSION );
    }

    public function enqueueScripts() {
        wp_enqueue_script( 'wptoolset-field-skype' );
        add_thickbox();
        $translation = array('title' => __( 'Edit Skype button' ));
        wp_localize_script( 'wptoolset-field-skype', 'wptSkypeData',
                $translation );
        add_action( 'admin_footer', array($this, 'editButtonTemplate') );
        add_action( 'wp_footer', array($this, 'editButtonTemplate') );
    }

    public function enqueueStyles() {
        wp_enqueue_style( 'wptoolset-field-skype' );
    }

    public function metaform() {
        $value = wp_parse_args( $this->getValue(), $this->_defaults );
        $form = array();
        $form[] = array(
            '#type' => 'textfield',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName() . '[skypename]',
            '#attributes' => array(),
            '#value' => $value['skypename'],
            '#validate' => $this->getValidationData(),
            '#attributes' => array('class' => 'js-wpt-skypename js-wpt-cond-trigger'), // Mark to be checked as conditional
        );
        $form['style'] = array(
            '#type' => 'hidden',
            '#value' => $value['button_style'],
            '#name' => $this->getName() . '[button_style]',
            '#attributes' => array('class' => 'js-wpt-skypestyle'),
        );
        $form[] = array(
            '#type' => 'markup',
            '#markup' => $this->getButtonImage( $value['skypename'], $value['button_style'], 'js-wpt-skype-preview' ),
        );
        $button_element = array(
            '#name' => '',
            '#type' => 'button',
            '#value' => __( 'Edit Skype button' ),
            '#attributes' => array('class' => 'js-wpt-skype-edit-button button-secondary'),
        );
        if ( array_key_exists( 'use_bootstrap', $this->_data ) && $this->_data['use_bootstrap'] ) {
            $button_element['#attributes']['class'] .= ' btn btn-default btn-sm';
        }
        $form[] = $button_element;
        return $form;
    }

    public function editButtonTemplate(){
        $output = '';
        $output .= '<div id="tpl-wpt-skype-edit-button" style="display:none;">'
                . '<div id="wpt-skype-edit-button-popup">'
                . '<h3>' .__( 'Enter your Skype Name' ) . '</h3>'
                . '<input type="textfield" value="" class="js-wpt-skypename-popup">&nbsp;'
                . '<h3>' . __( 'Select a button from below' ) . '</h3>';
        for ( $index = 1; $index < 7; $index++ ) {
            if ( $index == 5 ) {
                $output .= '<h3>' . __( 'Skype buttons with status' ) . '</h3>'
                        . '<p>' . __( 'If you choose to show your Skype status, your Skype button will always reflect your availability on Skype. This status will be shown to everyone, whether they’re in your contact list or not.' )
                        . '</p>';
            }
            $output .= '<div><label><input type="radio" name="wpt-skypestyle-popup" value="btn'
                    . $index . '">&nbsp;'
                    . $this->getButtonImage( '', "btn{$index}",
                            'js-wpt-skype-preview' )
                    . '</label></div>';
        }
        $output .= '<input type="button" class="button-secondary js-wpt-close-thickbox" value="' . __( 'Save' ) . '">'
                . '</div></div>';
        echo $output;
    }

    public function editform( $config = null ) {
        
    }

    public function mediaEditor(){
        return array();
    }

    /**
     * Returns HTML formatted skype button.
     * 
     * @param type $skypename
     * @param type $template
     * @param type $class
     * @return type 
     */
    function getButton( $skypename, $template = '', $class = false ) {

        if ( empty( $skypename ) ) {
            return '';
        }

        switch ( $template ) {

            case 'btn1':
                // Call me big drawn
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script><a href="skype:'
                        . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;

            case 'btn4':
                // Call me small
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;

            case 'btn3':
                // Call me small drawn
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;

            case 'btn6':
                // Status
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;

            case 'btn5':
                // Status drawn
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;

            default:
                // Call me big
                $output = '<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<a href="skype:' . $skypename . '?call">'
                        . $this->getButtonImage( $skypename, $template, $class )
                        . '</a>';
                break;
        }

        return $output;
    }

    /**
     * Returns HTML formatted skype button image.
     * 
     * @param type $skypename
     * @param type $template
     * @return type 
     */
    public function getButtonImage( $skypename = '', $template = '', $class = '' ) {

        if ( empty( $skypename ) ) {
            $skypename = '--not--';
        }

        $class = !empty( $class ) ? ' class="' . strval( $class ) . '"' : '';

        switch ( $template ) {
            case 'btn1':
                // Call me big drawn
                $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_153x63.png" style="border: none;" width="153" height="63" alt="Skype Me™!"' . $class . ' />';
                break;

            case 'btn4':
                // Call me small
                $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_transparent_34x34.png" style="border: none;" width="34" height="34" alt="Skype Me™!"' . $class . ' />';
                break;

            case 'btn3':
                // Call me small drawn
                $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_green_white_92x82.png" style="border: none;" width="92" height="82" alt="Skype Me™!"' . $class . ' />';
                break;

            case 'btn6':
                // Status
                $output = '<img src="http://mystatus.skype.com/bigclassic/' . $skypename . '" style="border: none;" width="182" height="44" alt="My status"' . $class . ' />';
                break;

            case 'btn5':
                // Status drawn
                $output = '<img src="http://mystatus.skype.com/balloon/' . $skypename . '" style="border: none;" width="150" height="60" alt="My status"' . $class . ' />';
                break;

            default:
                // Call me big
                $output = '<img src="http://download.skype.com/share/skypebuttons/buttons/call_blue_white_124x52.png" style="border: none;" width="124" height="52" alt="Skype Me™!"' . $class . ' />';
                break;
        }
        return $output;
    }

}
