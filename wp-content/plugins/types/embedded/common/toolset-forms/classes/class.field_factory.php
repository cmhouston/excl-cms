<?php

/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/classes/class.field_factory.php $
 * $LastChangedDate: 2014-05-19 11:12:04 +0200 (Mon, 19 May 2014) $
 * $LastChangedRevision: 22437 $
 * $LastChangedBy: marcin $
 *
 */

require 'abstract.field.php';

abstract class FieldFactory extends FieldAbstract
{
    protected $_nameField, $_data, $_value, $_use_bootstrap;

    function __construct($data, $global_name_field, $value)
    {
        $this->_nameField = $global_name_field;
        $this->_data = $data;
        $this->_value = $value;

        $this->init();
    }

    public function init()
    {
        $cred_cred_settings = get_option( 'cred_cred_settings' );
        $this->_use_bootstrap = is_array($cred_cred_settings) && array_key_exists( 'use_bootstrap', $cred_cred_settings ) && $cred_cred_settings['use_bootstrap'];
    }

    public function set_metaform($metaform)
    {
        $this->_metaform = $metaform;
    }

    public function get_metaform()
    {
        return $this->_metaform;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function set_data($data)
    {
        $this->data = $data;
    }

    public function set_nameField($nameField)
    {
        $this->_nameField = $nameField;
    }

    public function get_nameField()
    {
        return $this->_nameField;
    }

    public function getId()
    {
        return $this->_data['id'];
    }

    public function getType()
    {
        return $this->_data['type'];
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getTitle()
    {
        return $this->_data['title'];
    }

    public function getDescription()
    {
        return wpautop( $this->_data['description'] );
    }

    public function getName()
    {
        return $this->_data['name'];
    }

    public function getData()
    {
        return $this->_data;
    }

    public function getValidationData()
    {
        return !empty( $this->_data['validation'] ) ? $this->_data['validation'] : array();
    }

    public function setValidationData($validation)
    {
        $this->_data['validation'] = $validation;
    }

    public function getSettings()
    {
        return isset( $this->_settings ) ? $this->_settings : array();
    }

    public static function registerScripts() {}
    public static function registerStyles() {}
    public static function addFilters() {}
    public static function addActions() {}

    public function enqueueScripts() {}
    public function enqueueStyles() {}
    public function metaform() {}
    public function editform() {}
    public function mediaEditor() {}
}
