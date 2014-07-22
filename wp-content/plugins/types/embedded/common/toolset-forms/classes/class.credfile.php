<?php
require_once 'class.textfield.php';

/**
 * Description of class
 *
 * @author Francesco / Srdjan
 */
class WPToolset_Field_Credfile extends WPToolset_Field_Textfield
{

    public function init() {        
    }

    public static function registerScripts() {
        wp_register_script( 'wpt-field-credfile',
                WPTOOLSET_FORMS_RELPATH . '/js/credfile.js',
                array('wptoolset-forms'), WPTOOLSET_FORMS_VERSION, true );
    }

    public static function registerStyles() {
        wp_register_style( 'wpt-field-credfile',
                WPTOOLSET_FORMS_RELPATH . '/css/credfile.css' );
    }

    public function enqueueScripts() {
        wp_enqueue_script( 'wpt-field-credfile' );
    }

    public function enqueueStyles() {
        wp_enqueue_style( 'wpt-field-credfile' );
    }

    public function metaform() {
        $value = $this->getValue();        

        $is_image = false;
        if ( !empty( $value ) && $this->getName() == '_featured_image' ) {
            if ( preg_match( '/src="([\w\d\:\/\._-]*)"/', $value, $_v ) ) {
                $value = $_v[1];
            }
        }
        
        $id = str_replace(array("[","]"),"",$this->getName());
        $preview_file = WPTOOLSET_FORMS_RELPATH . '/images/icon-attachment32.png';
        $attr_hidden = array(
            'id' => $id."_hidden"
        );
        $attr_file = array(
            'id' => $id."_file",
            'class' => 'js-wpt-credfile-upload-file wpt-credfile-upload-file',
            'alt' => $value,
        );

        if ( !empty( $value ) ) {
            $preview_file = $value;
            $pathinfo = pathinfo( $value );
            if ( in_array( strtolower( $pathinfo['extension'] ), array('png', 'gif', 'jpg', 'jpeg', 'bmp') ) ) {
                $is_image = true;
            }
            // Set attributes
            $attr_file['disabled'] = 'disabled';
            $attr_file['style'] = 'display:none';
        } else {
            $attr_hidden['disabled'] = 'disabled';
        }

        $form = array();
        if ( !empty( $value ) ) {
            $form[] = array(
                '#type' => 'markup',
                '#markup' => '<input type="button" value="' . __( 'Delete' ) . '" id="'.$id.'_button" name="switch" class="js-wpt-credfile-delete-button wpt-credfile-delete-button" onclick="_cred_switch(\''.$id.'\')"/>',
            );
        }
        $form[] = array(
            '#type' => 'hidden',
            '#name' => $this->getName(),
            '#value' => $value,
            '#attributes' => $attr_hidden,
        );
        $form[] = array(
            '#type' => 'file',
            '#name' => $this->getName(),
            '#value' => $value,
            '#title' => $this->getTitleFromName( $this->getTitle() ),
            '#before' => '',
            '#after' => '',
            '#attributes' => $attr_file,
            '#validate' => $this->getValidationData(),
        );
        if ( $is_image ) {
            $form[] = array(
                '#type' => 'markup',
                '#markup' => '<img id="'.$id.'_image" src="' . $preview_file . '" class="js-wpt-credfile-preview wpt-credfile-preview" />',
            );
        } else {
            if (!empty($value))
            $form[] = array(
                '#type' => 'markup',
                '#markup' => $preview_file,
            );
        }

        return $form;
    }

    private function getTitleFromName( $name ) {
        switch ( $name ) {

            case "_featured_image":
                return "Featured Image";

            default:
                return $name;
        }
    }

}