<?php
/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/classes/class.fieldconfig.php $
 * $LastChangedDate: 2014-04-16 18:10:26 +0200 (Wed, 16 Apr 2014) $
 * $LastChangedRevision: 21591 $
 * $LastChangedBy: marcin $
 *
 */
if (!class_exists("FieldConfig")) {
    /**
     * Description of FieldConfig
     *
     * @author ontheGoSystem
     */
    class FieldConfig {

        private $id;
        private $name = "cred[post_title]";
        private $value;
        private $type = 'textfield';
        private $title = 'Post title';
        private $repetitive = false;
        private $display = '';
        private $description = '';
        private $config = array();
        private $options = array();
        private $default_value = '';
        private $validation = array();

        private $add_time = false;

        public function __construct() {
        }

        public function setRepetitive($repetitive) {
            $this->repetitive = $repetitive;
        }

        public function isRepetitive() {
            return $this->repetitive;
        }

        public function set_add_time($addtime) {
            $this->add_time = $addtime;
        }

        public function setDefaultValue($type,$field_arr)
        {
            switch ($type) {
            case 'date':
                $this->add_time = 'and_time' == $field_arr['data']['date_and_time'];
                    break;
            case 'checkboxes':
                $this->default_value = @$field_arr['attr']['default'][0];
                break;

            case 'select':
                $this->default_value = isset( $field_arr['attr']['actual_value'][0] )? $field_arr['attr']['actual_value'][0] : null;
                break;

            case 'radios':
                $this->default_value = $field_arr['attr']['default'];
                break;

            default:
                $this->default_value = "";
                break;
            }
        }

        public function setOptions($name,$type,$values,$attrs) {
            $arr = array();
            switch ($type) {
                case 'checkboxes':
                    foreach ($attrs['actual_titles'] as $refvalue=>$title) {
                        $value = $attrs['actual_values'][$refvalue];
                        $arr[$refvalue] = array('value'=>$refvalue,'title'=>$title,'checked'=>(bool)$values[$refvalue],'name'=>$name);
                    }
                    break;
                case 'select':
                    $values = $attrs['options'];
                    foreach ($values as $refvalue=>$title) {
                        $arr[$refvalue] = array(
                            'value' => $refvalue,
                            'title' => $title,
                            'types-value' => $attrs['actual_options'][$refvalue],
                        );
                    }
                    break;
                case 'radios':
                    foreach ($attrs['actual_titles'] as $refvalue=>$title) {
                        $arr[$refvalue] = array(
                            'value' => $refvalue,
                            'title' => $title,
                            'checked' => false,
                            'name' => $refvalue,
                            'types-value' => $attrs['actual_values'][$refvalue],
                        );
                    }
                break;
                default:
                    return;
                break;
            }
            $this->options = $arr;
        }

        public function createConfig() {
            $base_name = "cred";
            $this->config = array(
                'id' => $this->getId(),
                'type' => $this->getType(),
                'title' => $this->getTitle(),
                'options' => $this->getOptions(),
                'defualt_value' => $this->getDefaultValue(),
                'description' => $this->getDescription(),
                'repetitive' => $this->isRepetitive(),
                /*'name' => $base_name."[".$this->getType()."]",*/
                'name' => $this->getName(),
                'value' => $this->getValue(),
                'add_time' => $this->getAddTime(),
                'validation' => array(),
                'display' => $this->getDisplay(),
            );
            return $this->config;
        }

        public function getAddTime() {
            return $this->add_time;
        }

        public function getType() {
            return $this->type;
        }

        public function setType($type) {
            $this->type = $type;
        }

        public function getOptions() {
            return $this->options;
        }

        public function getDefaultValue() {
            return $this->default_value;
        }

        public function getTitle() {
            return $this->title;
        }

        public function getDisplay()
        {
            return $this->display;
        }

        public function setTitle($title) {
            $this->title = $title;
        }

        public function getDescription() {
            return $this->description;
        }

        public function setDescription($description) {
            $this->description = $description;
        }

        public function getName() {
            return $this->name;
        }

        public function setName($name) {
            $this->name = $name;
        }

        public function getValue() {
            return $this->value;
        }

        public function setValue($value) {
            $this->value = $value;
        }

        public function getValidation() {
            return !empty($this->validation) ? $this->validation : array();
        }

        public function setValidation($validation) {
            $this->validation = $validation;
        }

        public function getConfig() {
            return $this->config;
        }

        public function setConfig($config) {
            $this->config = $config;
        }

        public function getId() {
            return $this->id;
        }

        public function setId($id) {
            $this->id = $id;
        }

        public function setDisplay($display)
        {
            $this->display = $display;
        }
    }
}

