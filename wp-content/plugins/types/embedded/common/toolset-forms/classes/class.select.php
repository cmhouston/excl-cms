<?php
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Select extends FieldFactory
{

    public function metaform() {
        $value = $this->getValue();
        $data = $this->getData();

        $form = array();
        $options = array();
        if (isset($data['options'])) {
            foreach ( $data['options'] as $option ) {
                $one_option_data = array(
                    '#value' => $option['value'],
                    '#title' => $option['title'],
                );
                /**
                 * add default value if needed
                 * issue: frontend, multiforms CRED
                 */
                if ( array_key_exists( 'types-value', $option ) ) {
                    $one_option_data['#types-value'] = $option['types-value'];
                }
                /**
                 * add to options array
                 */
                $options[] = $one_option_data;
            }
        }
        if ( !empty( $value ) || $value == '0' ) {
            $data['default_value'] = $value;
        }
        $form[] = array(
            '#type' => 'select',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#options' => $options,
            '#default_value' => isset( $data['default_value'] ) ? $data['default_value'] : null,
            '#validate' => $this->getValidationData(),
            '#class' => 'form-inline',
        );
        return $form;
    }

}
