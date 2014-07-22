<?php
require_once 'class.field_factory.php';
require_once WPTOOLSET_FORMS_ABSPATH . '/lib/adodb-time.inc.php';

/**
 * Description of class
 *
 * @author Srdjan
 */
class WPToolset_Field_Date extends FieldFactory
{

    // 15/10/1582 00:00 - 31/12/3000 23:59
    protected static $_mintimestamp = -12219292800, $_maxtimestamp = 32535215940;

    public function init()
    {
    }

    public static function registerScripts()
    {
    }

    public static function registerStyles()
    {
    }

    public static function addFilters(){
        if ( has_filter( 'wptoolset_validation_value_date',
                        array('WPToolset_Field_Date', 'filterValidationValue') ) )
                return;
        // Filter validation
        add_filter( 'wptoolset_validation_value_date',
                array('WPToolset_Field_Date', 'filterValidationValue') );
        add_filter( 'wptoolset_validation_rule_js',
                array('WPToolset_Field_Date', 'filterValidationRuleJs') );
        add_filter( 'wptoolset_validation_args_php',
                array('WPToolset_Field_Date', 'filterValidationArgsPhp'), 10, 2 );
        // Filter conditional
        add_filter( 'wptoolset_conditional_args_php',
                array('WPToolset_Field_Date', 'filterConditionalArgsPhp'), 10, 2 );
        add_filter( 'wptoolset_conditional_value_php',
                array('WPToolset_Field_Date', 'filterConditionalValuePhp'), 10,
                2 );
        add_filter( 'wptoolset_conditional_args_js',
                array('WPToolset_Field_Date', 'filterConditionalArgsJs'), 10, 2 );
    }

    public function enqueueScripts()
    {
    }

    public function enqueueStyles()
    {
    }

    public function metaform() {
        $timestamp = $this->getValue();
        $datepicker = $hour = $minute = null;
        if ( !empty( $timestamp ) && $timestamp != '0' ) {
            if ( !is_numeric( $timestamp ) ) {
                $timestamp = self::strtotime( $timestamp );
            } else {
                $timestamp = intval( $timestamp );
            }
            if ( $timestamp !== false && self::_isTimestampInRange( $timestamp ) ) {
                $datepicker = self::timetodate( $timestamp );
                $hour = self::timetodate( $timestamp, 'H' );
                $minute = self::timetodate( $timestamp, 'i' );
            }
        }
        $data = $this->getData();
        
        $form = array();
        $form[] = array(
            '#type' => 'textfield',
            '#title' => $this->getTitle(),
            '#attributes' => array('class' => 'js-wpt-date', 'style' => 'width:150px;'),
            '#name' => $this->getName() . '[datepicker]',
            '#value' => $datepicker,
            '#validate' => $this->getValidationData(),
        );

        if ( !empty( $data['add_time'] ) ) {
            // Hour
            $hours = 24;
            $options = array();
            for ( $index = 0; $index < $hours; $index++ ) {
                $prefix = $index < 10 ? '0' : '';
                $options[$index] = array(
                    '#title' => $prefix . strval( $index ),
                    '#value' => $index,
                );
            }
            $hour_element = array(
                '#type' => 'select',
                '#title' => __( 'Hour' ),
                '#options' => $options,
                '#default_value' => $hour,
                '#name' => $this->getName() . '[hour]',
                '#inline' => true,
            );
            if ( array_key_exists( 'use_bootstrap', $this->_data ) && $this->_data['use_bootstrap'] ) {
                $hour_element['#before'] = '<div class="clearfix"><br />';
            }
            $form[] = $hour_element;
            // Minutes
            $minutes = 60;
            $options = array();
            for ( $index = 0; $index < $minutes; $index++ ) {
                $prefix = $index < 10 ? '0' : '';
                $options[$index] = array(
                    '#title' => $prefix . strval( $index ),
                    '#value' => $index,
                );
            }
                $minute_element = array(
                '#type' => 'select',
                '#title' => __( 'Minute' ),
                '#options' => $options,
                '#default_value' => $minute,
                '#name' => $this->getName() . '[minute]',
                '#inline' => true,
            );
            if ( array_key_exists( 'use_bootstrap', $this->_data ) && $this->_data['use_bootstrap'] ) {
                $minute_element['#after'] = '</div>';
            }
            $form[] = $minute_element;
        }

        return $form;
    }

    public static function getDateFormat()
    {
        return WPToolset_Field_Date_Scripts::getDateFormat();
    }

    protected function _dateToStrftime( $format ) {
        $format = str_replace( 'd', '%d', $format );
        $format = str_replace( 'D', '%a', $format );
        $format = str_replace( 'j', '%e', $format );
        $format = str_replace( 'l', '%A', $format );
        $format = str_replace( 'N', '%u', $format );
        $format = str_replace( 'w', '%w', $format );

        $format = str_replace( 'W', '%W', $format );

        $format = str_replace( 'F', '%B', $format );
        $format = str_replace( 'm', '%m', $format );
        $format = str_replace( 'M', '%b', $format );
        $format = str_replace( 'n', '%m', $format );

        $format = str_replace( 'o', '%g', $format );
        $format = str_replace( 'Y', '%Y', $format );
        $format = str_replace( 'y', '%y', $format );

        return $format;
    }

    public static function filterValidationValue( $value ) {
        if ( isset( $value['datepicker'] ) ) {
            return $value['datepicker'];
        }
        return $value;
    }

    public static function filterValidationRuleJS( $rule ) {
        if ( $rule == 'date' && self::getDateFormat() == 'd/m/Y' ) {
            return 'dateITA';
        }
        return $rule;
    }

    public static function filterValidationArgsPhp( $args, $rule ) {
        if ( $rule == 'date' ) {
            return array('$value', self::getDateFormat());
        }
        return $args;
    }

    public static function filterConditionalArgsJs( $args, $type ) {
        if ( $type == 'date' ) {
            foreach ( $args as &$arg ) {
                if ( !is_numeric( $arg ) ) {
                    $arg = self::strtotime( $arg );
                }
                // Use date formated with JS
                $arg = self::timetodate( $arg );
            }
        }
        return $args;
    }

    public static function filterConditionalArgsPhp( $args, $type ) {
        if ( $type == 'date' ) {
            foreach ( $args as &$arg ) {
                $arg = self::filterConditionalValuePhp( $arg, $type );
            }
        }
        return $args;
    }

    public static function filterConditionalValuePhp( $value, $type ) {
        if ( $type == 'date' ) {
            if ( !is_numeric( $value ) ) {
                $value = self::strtotime( $value );
            }
            // Use timestamp with PHP
            // Convert back/forward to have rounded timestamp (no H and i)
            $value = self::strtotime( self::timetodate( $value ) );
        }
        return $value;
    }

    public static function strtotime( $value, $format = null )
    {
        if ( is_null( $format ) ) {
            $format = self::getDateFormat();
        }
        /**
         * add exception to handle short year
         */
        if ( 'd/m/y' == $format ) {
            preg_match_all( '/(\d{2})/', $value, $value );
            $value[0][2] += $value[0][2] < 70? 2000:1900;
            $value = implode('-', $value[0] );
        }
        if ( strpos($format, 'd/m/Y') !== false ) {
            // strtotime requires a dash or dot separator to determine dd/mm/yyyy format
            preg_match( '/\d{2}\/\d{2}\/\d{4}/', $value, $matches );
            if ( !empty( $matches ) ) {
                foreach ( $matches as $match ) {
                    $value = str_replace( $match,
                        str_replace( '/', '-', $match ), $value );
                }
            }
        }
        try {
            $date = new DateTime( $value );
        } catch ( Exception $e ) {
            return false;
        }
        $timestamp = $date->format( "U" );
        return self::_isTimestampInRange( $timestamp ) ? $timestamp : false;
    }

    public static function timetodate( $timestamp, $format = null )
    {
        return WPToolset_Field_Date_Scripts::timetodate( $timestamp, $format );
    }

    protected static function _isTimestampInRange( $timestamp )
    {
        return WPToolset_Field_Date_Scripts::_isTimestampInRange($timestamp);
    }

    /**
     * Checks if timestamp is numeric and within range.
     * 
     * @param type $timestamp
     * @return type
     */
    public static function timeIsValid( $time ) {
        /*
         * http://php.net/manual/en/function.strtotime.php
         * The valid range of a timestamp is typically
         * from Fri, 13 Dec 1901 20:45:54 UTC
         * to Tue, 19 Jan 2038 03:14:07 UTC.
         * (These are the dates that correspond to the minimum
         * and maximum values for a 32-bit signed integer.)
         * Additionally, not all platforms support negative timestamps,
         * therefore your date range may be limited to no earlier than
         * the Unix epoch.
         * This means that e.g. dates prior to Jan 1, 1970 will not
         * work on Windows, some Linux distributions,
         * and a few other operating systems.
         * PHP 5.1.0 and newer versions overcome this limitation though. 
         */
        // MIN 'Jan 1, 1970' - 0 | Fri, 13 Dec 1901 20:45:54 UTC
        $_min_time = self::timeNegativeSupported() ? -2147483646 : 0;
        // MAX 'Tue, 19 Jan 2038 03:14:07 UTC' - 2147483647
        $_max_time = 2147483647;

        return is_numeric( $time ) && $_min_time <= intval( $time ) && intval( $time ) <= $_max_time;
    }

    /**
     * Checks if timestamp supports negative values.
     * 
     * @return type
     */
    public static function timeNegativeSupported() {
        return strtotime( 'Fri, 13 Dec 1950 20:45:54 UTC' ) === -601010046;
    }

}
