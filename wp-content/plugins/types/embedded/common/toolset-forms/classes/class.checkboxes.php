<?php
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Checkboxes extends FieldFactory
{

    public function metaform() {
        $value = $this->getValue();
        $data = $this->getData();
        
        $form = array();
        $_options = array();
        if (isset($data['options'])) {
            foreach ( $data['options'] as $option_key => $option ) {
                $_options[$option_key] = array(
                    '#value' => $option['value'],
                    '#title' => $option['title'],
                    '#type' => 'checkbox',
                    '#default_value' => isset( $data['checked'] ) ? $data['checked'] : !empty( $value[$option_key] ),
                    '#name' => $option['name'],
                    '#inline' => true,
                    '#after' => '<br />',
                );
            }
        }
        $form[] = array(
            '#type' => 'checkboxes',
            '#options' => $_options,
        );
        return $form;
    }

}