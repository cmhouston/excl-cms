<?php
require_once WPTOOLSET_FORMS_ABSPATH . '/lib/adodb-time.inc.php';

class WPToolset_Field_Date_Scripts
{

    public static $_supported_date_formats = array(
        'F j, Y', //December 23, 2011
        'Y/m/d', // 2011/12/23
        'm/d/Y', // 12/23/2011
        'd/m/Y', // 23/22/2011
        'd/m/y', // 23/22/11
    );

    public $_supported_date_formats_text = array(
        'F j, Y' => 'Month dd, yyyy',
        'Y/m/d' => 'yyyy/mm/dd',
        'm/d/Y' => 'mm/dd/yyyy',
        'd/m/Y' => 'dd/mm/yyyy',
        'd/m/y' => 'dd/mm/yy',
    );

    // 15/10/1582 00:00 - 31/12/3000 23:59
    protected static $_mintimestamp = -12219292800;
    protected static $_maxtimestamp =  32535215940;

    public function __construct()
    {
        add_action( 'init', array( $this, 'register' ));
        add_action( 'init', array( $this, 'enqueue' ));
    }

    public function register()
    {
        /**
         * styles
         */
        wp_register_style(
            'wptoolset-field-datepicker',
            WPTOOLSET_FORMS_RELPATH . '/css/wpt-jquery-ui/datepicker.css',
            array(),
            WPTOOLSET_FORMS_VERSION
        );
        wp_register_style(
            'wptoolset-field-date',
            WPTOOLSET_FORMS_RELPATH . '/css/wpt-jquery-ui/jquery-ui-1.9.2.custom.min.css',
            array('wptoolset-field-datepicker'),
            WPTOOLSET_FORMS_VERSION
        );
        /**
         * scripts
         */
        wp_register_script(
            'wptoolset-field-date',
            WPTOOLSET_FORMS_RELPATH . '/js/date.js',
            array('jquery-ui-datepicker'),
            WPTOOLSET_FORMS_VERSION,
            true
        );
        // Localize datepicker
        if ( in_array( self::getDateFormat(), self::$_supported_date_formats ) ) {
            $locale = str_replace( '_', '-', strtolower( get_locale() ) );
            $file = WPTOOLSET_FORMS_ABSPATH . '/js/i18n/jquery.ui.datepicker-' . $locale . '.js';
            if ( file_exists( $file ) ) {
                wp_register_script(
                    'wptoolset-field-date-localized',
                    WPTOOLSET_FORMS_RELPATH . '/js/i18n/jquery.ui.datepicker-' . $locale . '.js',
                    array('jquery-ui-datepicker'),
                    WPTOOLSET_FORMS_VERSION,
                    true
                );
            }
        }
    }

    public function enqueue()
    {
        /**
         * styles
         */
        wp_enqueue_style( 'wptoolset-field-date' );
        /**
         * scripts
         */
        wp_enqueue_script( 'wptoolset-field-date' );
        $date_format = self::getDateFormat();
        $js_date_format = $this->_convertPhpToJs( $date_format );
        $js_data = array(
            'buttonImage' => WPTOOLSET_FORMS_RELPATH . '/images/calendar.gif',
            'buttonText' => __( 'Select date' ),
            'dateFormat' => $js_date_format,
            'dateFormatPhp' => $date_format,
            'dateFormatNote' => esc_js( sprintf( __( 'Input format: %s' ), $date_format ) ),
            'yearMin' => intval( self::timetodate( self::$_mintimestamp, 'Y' ) ) + 1,
            'yearMax' => self::timetodate( self::$_maxtimestamp, 'Y' ),
        );
        wp_localize_script( 'wptoolset-field-date', 'wptDateData', $js_data );
        wp_enqueue_script( 'wptoolset-field-date-localized' );
    }

    protected function _convertPhpToJs( $date_format )
    {
        $date_format = str_replace( 'd', 'dd', $date_format );
        $date_format = str_replace( 'j', 'd', $date_format );
        $date_format = str_replace( 'l', 'DD', $date_format );
        $date_format = str_replace( 'm', 'mm', $date_format );
        $date_format = str_replace( 'n', 'm', $date_format );
        $date_format = str_replace( 'F', 'MM', $date_format );
        $date_format = str_replace( 'y', 'y', $date_format );
        $date_format = str_replace( 'Y', 'yy', $date_format );

        return $date_format;
    }

    public static function getDateFormat() {
        $date_format = get_option( 'date_format' );
        if ( !in_array( $date_format, self::$_supported_date_formats ) ) {
            $date_format = 'F j, Y';
        }
        return $date_format;
    }

    public static function timetodate( $timestamp, $format = null )
    {
        if ( is_null( $format ) ) {
            $format = self::getDateFormat();
        }
        return self::_isTimestampInRange( $timestamp ) ? @adodb_date( $format, $timestamp ) : false;
    }

    public static function _isTimestampInRange( $timestamp )
    {
        return self::$_mintimestamp <= $timestamp && $timestamp <= self::$_maxtimestamp;
    }
}


