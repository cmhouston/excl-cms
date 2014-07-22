<?php

/**
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/bootstrap.php $
 * $LastChangedDate: 2014-05-20 16:05:34 +0200 (Tue, 20 May 2014) $
 * $LastChangedRevision: 22493 $
 * $LastChangedBy: marcin $
 *
 */

require_once 'api.php';

define( 'WPTOOLSET_FORMS_VERSION', '0.1.1' );
define( 'WPTOOLSET_FORMS_ABSPATH', dirname( __FILE__ ) );
define( 'WPTOOLSET_FORMS_RELPATH', plugins_url( '', __FILE__ ) );
if ( !defined( 'WPTOOLSET_COMMON_PATH' ) ) {
    define( 'WPTOOLSET_COMMON_PATH', plugin_dir_path( __FILE__ ) );
}

class WPToolset_Forms_Bootstrap
{

    private $__forms;

    public final function __construct()
    {
        // Custom conditinal AJAX check
        add_action( 'wp_ajax_wptoolset_custom_conditional',
                array($this, 'ajaxCustomConditional') );

        // Date conditinal AJAX check
        add_action( 'wp_ajax_wptoolset_conditional',
                array($this, 'ajaxConditional') );

        // File media popup
        if ( (isset( $_GET['context'] ) && $_GET['context'] == 'wpt-fields-media-insert') || (isset( $_SERVER['HTTP_REFERER'] ) && strpos( $_SERVER['HTTP_REFERER'],
                        'context=wpt-fields-media-insert' ) !== false)
        ) {
            require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.file.php';
            add_action( 'init', array('WPToolset_Field_File', 'mediaPopup') );
        }
        add_filter('sanitize_file_name', array( $this, 'sanitize_file_name' ) );
        /**
         * common class for calendar
         */
        require_once WPTOOLSET_FORMS_ABSPATH.'/classes/class.date.scripts.php';
        new WPToolset_Field_Date_Scripts();
    }

    // returns HTML
    public function field($form_id, $config, $value)
    {
        $form = $this->form( $form_id, array() );
        return $form->metaform( $config, $config['name'], $value );
    }

    // returns HTML
//    public function fieldEdit($form_id, $config) {
//        $form = $this->form( $form_id, array() );
//        return $form->editform( $config );
//    }

    public function form( $form_id, $config = array() )
    {
        if ( isset( $this->__forms[$form_id] ) ) {
            return $this->__forms[$form_id];
        }
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.form_factory.php';
        return $this->__forms[$form_id] = new FormFactory( $form_id, $config );
    }

    public function validate_field($form_id, $config, $value)
    {
        if ( empty( $config['validation'] ) ) {
            return true;
        }
        $form = $this->form( $form_id, array() );
        return $form->validateField( $config, $value );
    }

    public function ajaxCustomConditional()
    {
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.conditional.php';
        WPToolset_Forms_Conditional::ajaxCustomConditional();
    }

    public function checkConditional($config)
    {
        if ( empty( $config['conditional'] ) ) {
            return true;
        }
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.conditional.php';
        return WPToolset_Forms_Conditional::evaluate( $config['conditional'] );
    }

    public function addConditional($form_id, $config)
    {
        $this->form( $form_id )->addConditional( $config );
    }

    public function ajaxConditional()
    {
        $data = $_POST['conditions'];
        $data['values'] = $_POST['values'];
        echo $this->checkConditional( array('conditional' => $data) );
        die();
    }

    public function filterTypesField($field, $post_id = null)
    {
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.types.php';
        return WPToolset_Types::filterField( $field, $post_id );
    }

    public function addFieldFilters($type)
    {
        if ( $class = $this->form( 'generic' )->loadFieldClass( $type ) ) {
            call_user_func( array($class, 'addFilters') );
            call_user_func( array($class, 'addActions') );
        }
    }

    public function getConditionalData($form_id)
    {
        return $this->form( $form_id )->getConditionalClass()->getData();
    }

    public function strtotime($date, $format = null)
    {
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.date.php';
        return WPToolset_Field_Date::strtotime( $date, $format );
    }

    public function timetodate($timestamp, $format = null)
    {
        require_once WPTOOLSET_FORMS_ABSPATH . '/classes/class.date.php';
        return WPToolset_Field_Date::timetodate( $timestamp, $format );
    }

    public function sanitize_file_name( $filename )
    {
        /**
         * replace german special characters
         */
        $de_from   = array('ä','ö','ü','ß','Ä','Ö','Ü');
        $de_to     = array('ae','oe','ue','ss','Ae','Oe','Ue');
        $filename = str_replace($de_from, $de_to, $filename);
        /**
         * replace polish special characters
         */
        $pl_from   = array( 'ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż' );
        $pl_to     = array( 'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'A', 'C', 'E', 'L', 'N', 'O', 'S', 'Z', 'Z' );
        $filename = str_replace($pl_from, $pl_to, $filename);
        /**
         * remove special characters
         */
        $filename = preg_replace( '/[^A-Za-z0-9\._]/', '-', $filename);
        $filename = preg_replace( '/[_ ]+/', '-', $filename);
        $filename = preg_replace( '/%20/', '-', $filename);
        return $filename;
    }
}

$GLOBALS['wptoolset_forms'] = new WPToolset_Forms_Bootstrap();
