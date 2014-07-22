<?php
require_once 'class.field_factory.php';

/**
 * Description of class
 *
 * @author Franko
 */
class WPToolset_Field_Textarea extends FieldFactory
{

    public function metaform() {
        $metaform = array();
        $metaform[] = array(
            '#type' => 'textarea',
            '#title' => $this->getTitle(),
            '#description' => $this->getDescription(),
            '#name' => $this->getName(),
            '#value' => $this->getValue(),
            '#validate' => $this->getValidationData(),
        );
        return $metaform;
    }

}
