<?php
/*
 * Types fields specific
 */
require_once 'class.types.php';
require_once 'class.conditional.php';

/**
 * Class description
 * 
 * @author Srdjan
 */
class WPToolset_Cred
{

    /**
     * Filters validation.
     * 
     * Loop over validation settings and create array of validation rules.
     * array( $rule => array( 'args' => array, 'message' => string ), ... )
     * 
     * @param array|string $field settings array (as stored in DB) or field ID
     * @return array array( $rule => array( 'args' => array, 'message' => string ), ... )
     */
    public static function filterValidation( $config ){
        /* Placeholder for field value '$value'.
         * 
         * Used for validation settings.
         * Field value is not processed here, instead string '$value' is used
         * to be replaced with actual value when needed.
         * 
         * For example:
         * validation['rangelength'] = array(
         *     'args' => array( '$value', 5, 12 ),
         *     'message' => 'Value length between %s and %s required'
         * );
         * validation['reqiuired'] = array(
         *     'args' => array( '$value', true ),
         *     'message' => 'This field is required'
         * );
         * 
         * Types have default and custom messages defined on it's side.
         */
        $value = '$value';
        $validation = array();
        if ( isset( $config['data']['validate'] ) ) {
            foreach ( $config['data']['validate'] as $rule => $settings ) {
                if ( $settings['active'] ) {
                    $validation[$rule] = array(
                        'args' => isset( $settings['args'] ) ? array_unshift( $value,
                                        $settings['args'] ) : array($value, true),
                        'message' => $settings['message']
                    );
                }
            }
        }
        return $validation;
    }

    /**
     * Filters conditional.
     * 
     * There are two types of conditionals:
     * 1. Regular conditionals created using Types GUI
     * 2. Custom onditionals (user entered manually)
     * 
     * 1. Regular conditional
     * 
     * Main properties:
     * [relation] - AND|OR evaluate as true if all or any condition is TRUE
     * [conditions] - array of conditions
     * [values] - values to check against (used only by PHP), evaluate method
     *      should not be aware if it's checking post meta or user meta,
     *      instead array of needed values (or missing) are passed to method.
     *      Types use filteres get_post_meta() and get_user_meta().
     * 
     * [conditions]
     * Each conditions is array with properties:
     * id: ID of trigger field (this field value is checked) to evaluate
     *      this field as TRUE or FALSE (corresponds to main IDs set here)
     * type: type of trigger field. JS and PHP needs at least this information
     *      when processing condtitional evaluation
     * operator: which operation to perform (=|>|<|>=|<=|!=|between)
     * args: arguments passed to checking functions
     * 
     * Example of reguar conditional
     * 
     * [conditional] => Array(
      [relation] => OR
      [conditions] => Array(
      [0] => Array(
      [id] => wpcf-my-date
      [type] => date
      [operator] => =
      [args] => Array(
      [0] => 02/01/2014
      )
      )
      [1] => Array(
      [id] => wpcf-my-date
      [type] => date
      [operator] => between
      [args] => Array(
      [0] => 02/07/2014
      [1] => 02/10/2014
      )
      )
      )
      [values] => Array(
      [wpcf-my-date] => 32508691200
      )
      )
     * 
     * 
     * 2. Custom conditional
     * Main properties:
     * [custom] - custom statement made by user, note that $xxxx should match
     *      IDs of fields that passed this filter.
     * [values] - same as for regular conditional
     * 
     * [conditional] => Array(
      [custom] => ($wpcf-my-date = DATE(01,02,2014)) OR ($wpcf-my-date > DATE(07,02,2014))
      [values] => Array(
      [wpcf-my-date] => 32508691200
      )
      )
     * 
     * @param array|string $field settings array (as stored in DB) or field ID
     * @param int $post_id Post or user ID to fetch meta data to check against
     * @return array
     */
    public static function filterConditional( $if, $post_id ){
//        $if = "($(foo) lte 4) OR $(my-text) eq 'show' ) AND ($(my-checkbox) ne 'hide' ) OR ($(text) gt '1' ) AND ($(text) lt '5' ) OR ($(text) gte '1' ) AND ($(text) lte '5' )";
        /*
         * CRED passes  Array
          (
          [if] => ($(text) eq  'show' ) AND ($(text) ne  'hide' ) OR ($(text) gt  '1' ) AND ($(text) lt  '5' ) OR ($(text) gte  '1' ) AND ($(text) lte  '5' )
          [mode] => fade-slide
          )
         */
        $patterns = array('/\$\(([\w\-]*)\)/', '/\s\)/', '/\s\s/', '/[\s](eq)[\s]/', '/[\s](ne)[\s]/', '/[\s](gt)[\s]/', '/[\s](lt)[\s]/', '/[\s](gte)[\s]/', '/[\s](lte)[\s]/');
        $replace = array('$\1', ')', ' ', ' = ', ' != ', ' > ', ' < ', ' >= ', ' <= ');
        $custom = preg_replace( $patterns, $replace, $if );

        preg_match_all( '/\((.*?)\)/', $custom, $conditions );
        preg_match( "/\)[\s](AND|OR)[\s]\(/", $custom, $relation );

        if ( empty( $conditions[1] ) ) {
            return array();
        }
        if ( empty( $relation[1] ) ) {
            $relation[1] = 'AND';
        }

        $c = array();
        $fields = WPToolset_Forms_Conditional::extractFields( $custom );
        foreach ( $fields as $field ) {
            foreach ( $conditions[1] as $k => $condition ) {
                preg_match( "/\'(.*?)\'/", $condition, $value );
                preg_match( "/[\s](=|!=|>|<|>=|<=)[\s]/", $condition, $operator );
                if ( empty( $value[1] ) || empty( $operator[1] ) ) {
                    continue;
                }
                if ( $config = WPToolset_Types::getConfig( $field ) ) {
                    $id =  WPToolset_Types::getPrefix( $field ) . $field;
                } else {
                    $id = $field;
                }
                $c[] = array(
                    'id' => $id,
                    'type' => $config['type'],
                    'operator' => $operator[1],
                    'args' => array($value[1]),
                );
            }
        }

        global $post;
        $data = array(
            'relation' => $relation[1],
            'conditions' => $c,
            'values' => get_post_custom( $post_id ),
        );
        return $data;
    }

}
