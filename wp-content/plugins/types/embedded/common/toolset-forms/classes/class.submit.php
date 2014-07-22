<?php
require_once 'class.textfield.php';

/**
 * Description of class
 *
 * @author Franko
 */
class WPToolset_Field_Submit extends WPToolset_Field_Textfield
{

    public function metaform() {
        $metaform = array();
        $metaform[] = array(
            '#type' => 'submit',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => $this->getValue(),
            '#validate' => $this->getValidationData()
        );
        if ( array_key_exists( 'use_bootstrap', $this->_data ) && $this->_data['use_bootstrap'] ) {
            $metaform[0]['#attributes']['class'] = 'btn btn-primary';
        }
        $this->set_metaform($metaform); 
        return $metaform;
    }

}
