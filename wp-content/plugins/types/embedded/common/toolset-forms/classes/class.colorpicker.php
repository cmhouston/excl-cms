<?php
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Colorpicker extends FieldFactory
{
    public function init()
    {
    }

    static public function registerScripts()
    {
        wp_register_script(
            'wptoolset-field-colorpicker',
            WPTOOLSET_FORMS_RELPATH . '/js/colorpicker.js',
            array('iris'),
            WPTOOLSET_FORMS_VERSION,
            true
        );
    }

    public function enqueueScripts()
    {
        if ( !is_admin() ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script(
                'iris',
                admin_url( 'js/iris.min.js' ),
                array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                false,
                1
            );
            wp_enqueue_script(
                'wp-color-picker',
                admin_url( 'js/color-picker.min.js' ),
                array( 'iris' ),
                false,
                1
            );
            $colorpicker_l10n = array(
                'clear' => __( 'Clear' ),
                'defaultString' => __( 'Default' ),
                'pick' => __( 'Select Color' )
            );
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
        }
        wp_enqueue_script( 'wptoolset-field-colorpicker' );
    }

    public function metaform()
    {
        $form = array();
        $form['name'] = array(
            '#type' => 'textfield',
            '#title' => $this->getTitle(),
            '#value' => $this->getValue(),
            '#name' => $this->getName(),
            '#attributes' => array('class' => 'js-wpt-colorpicker'),
            '#validate' => $this->getValidationData(),
            '#after' => '',
        );
        return $form;
    }

    public static function filterValidationValue($value)
    {
        if ( isset( $value['datepicker'] ) ) {
            return $value['datepicker'];
        }
        return $value;
    }
}
