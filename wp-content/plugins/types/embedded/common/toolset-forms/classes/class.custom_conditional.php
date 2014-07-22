<?php
/*
 * Custom conditinal evaluation class.
 * 
 * Uses common functions.
 * Cloned wpv_condition() moved here to release it from post meta.
 * Instead getting post meta, array of values is passed (like get_post_custom())
 * and those values are used for evaluation.
 * 
 * Known bugs with wpv_condition()
 * - field names with slash, like '$my-field' fails regex
 * - some mixed statements fail, like '($t = 'show') OR ($t > 10)' cause using
 *      string and num comparation 
 */
require_once WPTOOLSET_COMMON_PATH . '/functions.php';
require_once WPTOOLSET_COMMON_PATH . '/wpv-filter-date-embedded.php';
require_once WPTOOLSET_COMMON_PATH . '/wplogger.php';

/**
 * Description of class
 */
class WPToolset_Forms_Custom_Conditional
{

    private $__evaluate, $__fields, $__values;

    public function __construct( $evaluate, $fields, $values ){
        $this->__evaluate = $evaluate;
        $this->__fields = $fields;
        $this->__values = $values;
    }
    
    public function evaluate(){
        return $this->_wpv_condition( $this->__evaluate, $this->__fields );
    }

    protected function _getValue( $key ){
        return isset( $this->__values[$key] ) ? $this->__values[$key] : null;
    }

    /**
     * Condition function to evaluate and display given block based on expressions
     * 
     * Supported actions and symbols:
     * 
     * Integer and floating-point numbers
     * Math operators: +, -, *, /
     * Comparison operators: &lt;, &gt;, =, &lt;=, &gt;=, !=
     * Boolean operators: AND, OR, NOT
     * Nested expressions - several levels of brackets
     * Variables defined as shortcode parameters starting with a dollar sign
     * empty() function that checks for blank or non-existing fields
     * 
     * @global type $wplogger
     * @param type $evaluate
     * @param type $fields
     * @return type
     */
    protected function _wpv_condition( $evaluate, $fields ){

        global $wplogger;

        $logging_string = "Original expression: " . $evaluate;

        add_filter( 'wpv-extra-condition-filters', 'wpv_add_time_functions' );
        $evaluate = apply_filters( 'wpv-extra-condition-filters', $evaluate );

        // evaluate empty() statements for variables
//        $empties = preg_match_all( "/empty\(\s*\\$(\w+)\s*\)/", $evaluate,
//                $matches );
        $empties = preg_match_all( "/empty\(\s*\\$([\w_-]+)\s*\)/", $evaluate,
                $matches );

        if ( $empties && $empties > 0 ) {
            for ( $i = 0; $i < $empties; $i++ ) {
                $match_var = $this->_getValue( $fields[$matches[1][$i]] );
                $is_empty = '1=0';

                // mark as empty only nulls and ""  
//            if ( is_null( $match_var ) || strlen( $match_var ) == 0 ) {
                if ( is_null( $match_var ) || ( is_string( $match_var ) && strlen( $match_var ) == 0 ) || ( is_array( $match_var ) && empty( $match_var ) ) ) {
                    $is_empty = '1=1';
                }

                $evaluate = str_replace( $matches[0][$i], $is_empty, $evaluate );
            }
        }

        // find variables that are to be used as strings.
        // eg '$f1'
        // will replace $f1 with the actual field value
        $strings_count = preg_match_all( '/(\'[\$\w^\']*\')/', $evaluate,
                $matches );
        if ( $strings_count && $strings_count > 0 ) {
            for ( $i = 0; $i < $strings_count; $i++ ) {
                $string = $matches[1][$i];
                // remove single quotes from string literals to get value only
                $string = (strpos( $string, '\'' ) === 0) ? substr( $string, 1,
                                strlen( $string ) - 2 ) : $string;
                if ( strpos( $string, '$' ) === 0 ) {
                    $variable_name = substr( $string, 1 ); // omit dollar sign
                    if ( isset( $fields[$variable_name] ) ) {
                        $string = $this->_getValue( $fields[$variable_name] );
                        $evaluate = str_replace( $matches[1][$i],
                                "'" . $string . "'", $evaluate );
                    }
                }
            }
        }

        // find string variables and evaluate
//        $strings_count = preg_match_all( '/((\$\w+)|(\'[^\']*\'))\s*([\!<>\=]+)\s*((\$\w+)|(\'[^\']*\'))/',
//                $evaluate, $matches );
        $strings_count = preg_match_all( '/((\$[\w_-]+)|([\'\d][^\']*[\'\d]))\s*([\!<>\=]+)\s*((\$[\w_-]+)|([\'\d]*[\'\d]))/',
                $evaluate, $matches );

        // get all string comparisons - with variables and/or literals
        if ( $strings_count && $strings_count > 0 ) {
            for ( $i = 0; $i < $strings_count; $i++ ) {

                // get both sides and sign
                $first_string = $matches[1][$i];
                $second_string = $matches[5][$i];
                $math_sign = $matches[4][$i];
                
                // remove single quotes from string literals to get value only
                $first_string = (strpos( $first_string, '\'' ) === 0) ? substr( $first_string,
                                1, strlen( $first_string ) - 2 ) : $first_string;
                $second_string = (strpos( $second_string, '\'' ) === 0) ? substr( $second_string,
                                1, strlen( $second_string ) - 2 ) : $second_string;

                // replace variables with text representation
                if ( strpos( $first_string, '$' ) === 0 ) {
                    $variable_name = substr( $first_string, 1 ); // omit dollar sign
                    if ( isset( $fields[$variable_name] ) ) {
                        $first_string = $this->_getValue( $fields[$variable_name] );
                    } else {
                        $first_string = '';
                    }
                }
                if ( strpos( $second_string, '$' ) === 0 ) {
                    $variable_name = substr( $second_string, 1 );
                    if ( isset( $fields[$variable_name] ) ) {
                        $second_string = $this->_getValue( $fields[$variable_name] );
                    } else {
                        $second_string = '';
                    }
                }

                // don't do string comparison if variables are numbers 
                if ( (is_numeric( $first_string ) && !is_numeric( $second_string ))
                        || (!is_numeric( $first_string ) && is_numeric( $second_string )) ) {
                    $evaluate = str_replace( $matches[0][$i], '1=0',
                                $evaluate );
                } else if ( !(is_numeric( $first_string ) && is_numeric( $second_string )) ) {
                    // compare string and return true or false
                    $compared_str_result = wpv_compare_strings( $first_string,
                            $second_string, $math_sign );

                    if ( $compared_str_result ) {
                        $evaluate = str_replace( $matches[0][$i], '1=1',
                                $evaluate );
                    } else {
                        $evaluate = str_replace( $matches[0][$i], '1=0',
                                $evaluate );
                    }
                } else {
                    $evaluate = str_replace( $matches[1][$i], $first_string,
                            $evaluate );
                    $evaluate = str_replace( $matches[5][$i], $second_string,
                            $evaluate );
                }
            }
        }

        // find remaining strings that maybe numeric values.
        // This handles 1='1'
        $strings_count = preg_match_all( '/(\'[^\']*\')/', $evaluate, $matches );
        if ( $strings_count && $strings_count > 0 ) {
            for ( $i = 0; $i < $strings_count; $i++ ) {
                $string = $matches[1][$i];
                // remove single quotes from string literals to get value only
                $string = (strpos( $string, '\'' ) === 0) ? substr( $string, 1,
                                strlen( $string ) - 2 ) : $string;
                if ( is_numeric( $string ) ) {
                    $evaluate = str_replace( $matches[1][$i], $string, $evaluate );
                }
            }
        }


        // find all variable placeholders in expression
//        $count = preg_match_all( '/\$(\w+)/', $evaluate, $matches );
        $count = preg_match_all( '/\$([\w-_]+)/', $evaluate, $matches );

        $logging_string .= "; Variable placeholders: " . var_export( $matches[1],
                        true );

        // replace all variables with their values listed as shortcode parameters
        if ( $count && $count > 0 ) {
            // sort array by length desc, fix str_replace incorrect replacement
            $matches[1] = wpv_sort_matches_by_length( $matches[1] );

            foreach ( $matches[1] as $match ) {
                if ( isset( $fields[$match] ) ) {
                    $meta = $this->_getValue( $fields[$match] );
                    if ( empty( $meta ) ) {
                        $meta = "0";
                    }
                } else {
                    $meta = "0";
                }
                $evaluate = str_replace( '$' . $match, $meta, $evaluate );
            }
        }

        $logging_string .= "; End evaluated expression: " . $evaluate;

        $wplogger->log( $logging_string, WPLOG_DEBUG );
//         evaluate the prepared expression using the custom eval script
        $result = wpv_evaluate_expression( $evaluate );

        // return true, false or error string to the conditional caller
        return $result;
    }
}