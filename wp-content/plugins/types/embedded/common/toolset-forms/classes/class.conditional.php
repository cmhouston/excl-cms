<?php
/*
 * - Checks conditionals when form is displayed and values changed
 * - Checks simple conditionals using JS
 * - Checks custom conditinals via AJAX/PHP
 * - PHP simple and custom checks available using class methods
 * 
 * Simple conditionals
 * 
 * Data
 * [id] - Trigger ID to match data-wpt-id
 * [type] - field type (trigger)
 * [operator] - operator
 * [args] - array(value, value2...)
 * 
 * Example
 * $config['conditional'] = array(
 *  'relation' => 'OR'|'AND',
 *  'conditions' => array(
 *      array(
 *          'id' => 'wpcf-text',
 *          'type' => 'textfield',
 *          'operator' => '==',
 *          'args' => array('show')
 *      ),
 *      array(
 *          'id' => 'wpcf-date',
 *          'type' => 'date',
 *          'operator' => 'beetween',
 *          'args' => array('21/01/2014', '24/01/2014') // Accepts timestamps or string date
 *      )
 *  ),
 * );
 * 
 * Custom conditionals
 * 
 * Variable name should match trigger ID - data-wpt-id
 * Example
 * $config['conditional'] = array(
 *  'custom' => '($wpcf-text = show) OR ($wpcf-date > '21-01-2014')'
 * );
 */
require_once WPTOOLSET_COMMON_PATH . '/functions.php';
require_once WPTOOLSET_COMMON_PATH . '/wpv-filter-date-embedded.php';
require_once 'class.custom_conditional.php';

/**
 * Class description
 * 
 * @todo BUG common function wpv_condition has some flaws
 *      (dashed names, mixed checks for string and numeric values causes failure)
 * 
 * @author Srdjan
 */
class WPToolset_Forms_Conditional
{

    private $__formID;
    protected $_collected = array(), $_triggers = array(), $_fields = array(), $_custom_triggers = array(), $_custom_fields = array();

    /**
     * Register and enqueue scripts and actions.
     * 
     * @param type $formID
     */
    public function __construct( $formID ) {
        $this->__formID = trim( $formID, '#' );
        // Register and enqueue
        wp_register_script( 'wptoolset-form-conditional',
                WPTOOLSET_FORMS_RELPATH . '/js/conditional.js', array('jquery'),
                WPTOOLSET_FORMS_VERSION, true );
        wp_enqueue_script( 'wptoolset-form-conditional' );
        // Render settings
        add_action( 'admin_print_footer_scripts', array($this, 'renderJsonData'), 30 );
        add_action( 'wp_footer', array($this, 'renderJsonData'), 30 );
        // Check conditional and hide field
        add_action('wptoolset_field_class', array($this, 'actionFieldClass') );
    }

    /**
     * Collects data.
     * 
     * Called from form_factory.
     * 
     * @param type $config
     */
    public function add( $config ) {
        if ( !empty( $config['conditional'] ) ) {
            $this->_collected[$config['id']] = $config['conditional'];
            return;
        }
    }

    /**
     * Sets JSON data to be used with conditional.js
     */
    protected function _parseData() {
        foreach ( $this->_collected as $id => $config ) {
            if ( !empty( $config['custom'] ) ) {
                $evaluate = $config['custom'];
                $fields = self::extractFields( $evaluate );
                foreach ( $fields as $field ) {
                    $this->_custom_fields[$id]['custom'] = $config['custom'];
                    $this->_custom_fields[$id]['triggers'][] = $field;
                    $this->_custom_triggers[$field][] = $id;
                }
            } else {
                $this->_fields[$id]['relation'] = $config['relation'];
                foreach ( $config['conditions'] as &$c ) {
                    /*
                     * $c[id] - field id
                     * $c[type] - field type
                     * $c[operator] - operator
                     * $c[args] - array(value, [value2]...)
                     */
                    if ( !isset( $this->_triggers[$c['id']] ) )
                            $this->_triggers[$c['id']] = array();
                    $c['args'] = apply_filters( 'wptoolset_conditional_args_js',
                            $c['args'], $c['type'] );
                    $this->_fields[$id]['conditions'][] = $c;
                    if ( !in_array( $id, $this->_triggers[$c['id']] ) )
                            $this->_triggers[$c['id']][] = $id;
                }
            }
        }
    }

    /**
     * Renders JSON data in footer to be used with conditional.js
     */
    public function renderJsonData() {
        $this->_parseData();
        if ( !empty( $this->_triggers ) ) {
            echo '<script type="text/javascript">wptCondTriggers["#'
            . $this->__formID . '"] = ' . json_encode( $this->_triggers ) . ';</script>';
        }
        if ( !empty( $this->_fields ) ) {
            echo '<script type="text/javascript">wptCondFields["#'
            . $this->__formID . '"] = ' . json_encode( $this->_fields ) . ';</script>';
        }
        if ( !empty( $this->_custom_triggers ) ) {
            echo '<script type="text/javascript">wptCondCustomTriggers["#'
            . $this->__formID . '"] = ' . json_encode( $this->_custom_triggers ) . ';</script>';
        }
        if ( !empty( $this->_custom_fields ) ) {
            echo '<script type="text/javascript">wptCondCustomFields["#'
            . $this->__formID . '"] = ' . json_encode( $this->_custom_fields ) . ';</script>';
        }
    }

    /**
     * Compares values.
     * 
     * @param array $config
     * @param array $values
     * @return type 
     */
    public static function evaluate( $config ) {

        // Custom conditional
        if ( !empty( $config['custom'] ) ) {
            return self::evaluateCustom( $config['custom'], $config['values'] );
        }

        $passedOne = false;
        $passedAll = true;
        $relation = $config['relation'];

        foreach ( $config['conditions'] as $c ) {
            // Add filters
            wptoolset_form_field_add_filters( $c['type'] );
            $c['args'] = apply_filters( 'wptoolset_conditional_args_php',
                    $c['args'], $c['type'] );
            $value = isset( $config['values'][$c['id']] ) ? $config['values'][$c['id']] : null;
            $value = apply_filters( 'wptoolset_conditional_value_php', $value, $c['type'] );
            $compare = $c['args'][0];
            switch ( $c['operator'] ) {
                case '=':
                case '==':
                    $passed = $value == $compare;
                    break;

                case '>':
                    $passed = intval( $value ) > intval( $compare );
                    break;

                case '>=':
                    $passed = intval( $value ) >= intval( $compare );
                    break;

                case '<':
                    $passed = intval( $value ) < intval( $compare );
                    break;

                case '<=':
                    $passed = intval( $value ) <= intval( $compare );
                    break;

                case '===':
                    $passed = $value === $compare;
                    break;

                case '!==':
                    $passed = $value !== $compare;
                    break;

                case '<>':
                    $passed = $value <> $compare;
                    break;

                case 'between':
                    $passed = intval( $value ) > intval( $compare ) && intval( $value ) < intval( $c['args'][1] );
                    break;

                default:
                    $passed = false;
                    break;
            }
            if ( !$passed ) {
                $passedAll = false;
            } else {
                $passedOne = true;
            }
        }
        if ( $relation == 'AND' && $passedAll ) {
            return true;
        }
        if ( $relation == 'OR' && $passedOne ) {
            return true;
        }
        return false;
    }

    /**
     * Evaluates conditions using custom conditional statement.
     * 
     * @uses wpv_condition()
     * 
     * @param type $post
     * @param type $evaluate
     * @return boolean
     */
    public static function evaluateCustom( $evaluate, $values ) {
        $evaluate = trim( stripslashes( $evaluate ) );
        // Check dates
        $evaluate = wpv_filter_parse_date( $evaluate );
        $fields = self::extractFields( $evaluate );

        // Add quotes = > < >= <= === <> !==
        $strings_count = preg_match_all( '/[=|==|===|<=|<==|<===|>=|>==|>===|\!===|\!==|\!=|<>]\s(?!\$)(\w*)[\)|\$|\W]/',
                $evaluate, $matches );
        if ( !empty( $matches[1] ) ) {
            foreach ( $matches[1] as $temp_match ) {
                $temp_replace = is_numeric( $temp_match ) ? $temp_match : '\'' . $temp_match . '\'';
                $evaluate = str_replace( ' ' . $temp_match . ')',
                        ' ' . $temp_replace . ')', $evaluate );
            }
        }
        $custom = new WPToolset_Forms_Custom_Conditional( $evaluate, $fields, $values );
        $check = $custom->evaluate();
        if ( !is_bool( $check ) ) {
            return false;
        }
        return $check;
    }

    /**
     * Extracts fields from custom conditional statement.
     * 
     * @param type $evaluate
     * @return type
     */
    public static function extractFields( $evaluate ) {
        $evaluate = trim( stripslashes( $evaluate ) );
        // Check dates
        $evaluate = wpv_filter_parse_date( $evaluate );
        // Add quotes = > < >= <= === <> !==
        $strings_count = preg_match_all( '/[=|==|===|<=|<==|<===|>=|>==|>===|\!===|\!==|\!=|<>]\s(?!\$)(\w*)[\)|\$|\W]/',
                $evaluate, $matches );
        if ( !empty( $matches[1] ) ) {
            foreach ( $matches[1] as $temp_match ) {
                $temp_replace = is_numeric( $temp_match ) ? $temp_match : '\'' . $temp_match . '\'';
                $evaluate = str_replace( ' ' . $temp_match . ')',
                        ' ' . $temp_replace . ')', $evaluate );
            }
        }
        preg_match_all( '/\$([^\s]*)/', $evaluate, $matches );
        $fields = array();
        if ( !empty( $matches ) ) {
            foreach ( $matches[1] as $field_name ) {
                $fields[trim($field_name, '()')] = trim($field_name,'()');
            }
        }
        return $fields;
    }

    /**
     * Custom conditional AJAX check (called from bootstrap.php)
     */
    public static function ajaxCustomConditional() {
        $res = array('passed' => array(), 'failed' => array());
        foreach ( $_POST['conditions'] as $k => $c ) {
            $values = array();
            foreach ( $_POST['values'] as $fid => $value ) {
                if ( isset( $_POST['field_types'][$fid] ) ) {
                    $field_type = $_POST['field_types'][$fid];
                    wptoolset_form_field_add_filters( $field_type );
                    $value = apply_filters( 'wptoolset_conditional_value_php',
                            $value, $field_type );
                }
                $values[$fid] = $value;
            }
            if ( $passed = self::evaluateCustom( $c, $values ) ) {
                $res['passed'][] = $k;
            } else {
                $res['failed'][] = $k;
            }
        }
        echo json_encode( $res );
        die();
    }

    /**
     * Checks conditional and hides field.
     * 
     * @param type $config
     */
    public function actionFieldClass( $config ) {
        if ( !empty( $config['conditional'] )
                && !self::evaluate( $config['conditional'] ) ) {
            echo ' wpt-hidden js-wpt-remove-on-submit js-wpt-validation-ignore';
        }
    }

    /**
     * Returns collected JSON data
     * 
     * @return type
     */
    public function getData(){
        $this->_parseData();
        return array(
            'triggers' => $this->_triggers,
            'fields' => $this->_fields,
            'custom_triggers' => $this->_custom_triggers,
            'custom_fields' => $this->_custom_fields,
        );
    }

}
