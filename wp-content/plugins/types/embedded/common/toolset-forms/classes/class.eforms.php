<?php
/*  Copyright 2011  enlimbo lancers  (email : lancers@enlimbo.net)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * Enlimbo Forms class for form creation
 *
 * @package Enlimbo
 * @subpackage Forms
 * @copyright enlimbo lancers 2012
 * @license GPLv2 or later
 * @version 1.1
 * @link http://enlimbo.net/forms
 * @author srdjan <srdjan@enlimbo.net>
 *
 * $HeadURL: https://www.onthegosystems.com/misc_svn/common/tags/Views-1.6.1-Types-1.5.7/toolset-forms/classes/class.eforms.php $
 * $LastChangedDate: 2014-05-20 15:27:50 +0200 (Tue, 20 May 2014) $
 * $LastChangedRevision: 22486 $
 * $LastChangedBy: marcin $
 *
 */
class Enlimbo_Forms
{

    /**
     * @var string
     */
    private $_id;

    /**
     * @var array
     */
    private $_errors = array();

    /**
     * @var array
     */
    private $_elements = array();

    /**
     * @var array
     */
    private $_count = array();

    /**
     * @var string
     */
    public $css_class = 'wpt-form';

    /**
     * @var string
     */
    private $_validationFunc = '';

    /**
     * @var array
     */
    public $form_settings = array();

    public function __construct( $id )
    {
        /**
         * default settings
         */
        $this->form_settings = array(
            'has_media_button' => true,
            'use_bootstrap' => false,
        );
        $this->_id = $id;
        if ( !is_admin() ) {
            $this->post_form_id = preg_replace( '/^cred_form_(\d+)_\d+$/', "$1", $this->_id );
            $form_settings = get_post_meta( $this->post_form_id, '_cred_form_settings', true );
            if ( isset($form_settings->form) ) {
                $this->form_settings = $form_settings->form;
            }
            unset($form_settings);
        }
        /**
         * check cread seting for bootstrap
         */
        $cred_cred_settings = get_option( 'cred_cred_settings' );
        if ( is_array($cred_cred_settings) ) {
            $this->form_settings['use_bootstrap'] = array_key_exists( 'use_bootstrap', $cred_cred_settings ) && $cred_cred_settings['use_bootstrap'];
        }
    }

    /**
     * Auto handler
     *
     * Renders.
     *
     * @param array $element
     * @return HTML formatted output
     */
    public function autoHandle($id, $form)
    {
        // Auto-add nonce field
        $form['nonce'] = array(
            '#type' => 'hidden',
            '#name' => '_nonce',
            '#value' => md5($id),
        );

        $this->_id = $id;
        $this->_elements = $form;

        // get submitted data
        if ($this->isSubmitted()) {

            // check if errors (validation)
            $this->validate($this->_elements);

            // callback
            if (empty($this->_errors)) {

                if (isset($form['#form']['callback'])) {
                    if (is_array($form['#form']['callback'])) {
                        foreach ($form['#form']['callback'] as $callback) {
                            if (is_callable($callback)) {
                                call_user_func($callback, $this);
                            }
                        }
                    } else {
                        if (is_callable($form['#form']['callback'])) {
                            call_user_func($form['#form']['callback'], $this);
                        }
                    }
                }
                // Maybe triggered by callback function
                if (empty($this->_errors)) {
                    // redirect
                    if (!isset($form['#form']['redirection'])) {
                        header('Location: ' . $_SERVER['REQUEST_URI']);
                    } else if ($form['#form']['redirection'] != false) {
                        header('Location: ' . $form['#form']['redirection']);
                    }
                }
            }
        }
    }

    /**
     * Checks if form is submitted.
     *
     * @param type $id
     * @return type
     */
    public function isSubmitted($id = '')
    {
        if (empty($id)) {
            $id = $this->_id;
        }
        return (isset($_REQUEST['_nonce'])
                && md5($_REQUEST['_nonce']) == $id);
    }

    /**
     * Sets validation function.
     *
     * @param type $class
     */
//    public function setValidationFunc($func)
//    {
//        $this->_validationFunc = $func;
//    }

    /**
     * Loops over elements and validates them.
     *
     * @param type $elements
     */
    public function validate(&$elements)
    {
        foreach ($elements as $key => &$element) {
            if (!isset($element['#type'])
                    || !$this->_isValidType($element['#type'])) {
                continue;
            }
            if ($element['#type'] != 'fieldset') {
                if (isset($element['#name'])
                        && !in_array($element['#type'], array('submit', 'reset'))) {
                    if ($this->isSubmitted()) {
                        // Set submitted data
                        if (!in_array($element['#type'], array('checkboxes'))
                                && empty($element['#forced_value'])) {
                            $element['#value'] = $this->getSubmittedData($element);
                        } else if (!empty($element['#options'])
                                && empty($element['#forced_value'])) {
                            foreach ($element['#options'] as $option_key => $option) {
                                $option['#type'] = 'checkbox';
                                $element['#options'][$option_key]['#value'] = $this->getSubmittedData($option);
                            }
                        }
                    }
                }
                // Validate
                if (isset($element['#validate'])) {
                    $this->validateElement($element);
                }
            } else if (isset($element['#type'])
                    && $element['#type'] == 'fieldset') {
                $this->validate($element);
            } else if (is_array($element)) {
                $this->validate($element);
            }
        }
    }

    /**
     * Validates element.
     *
     * @param type $element
     */
    public function validateElement( &$element )
    {
        $value = isset( $element['#value'] ) ? $element['#value'] : null;
        if ( is_null( $value ) && isset( $element['#default_value'] ) ) {
            $value = $element['#default_value'];
        }
        $element = apply_filters( 'wptoolset_form_' . $this->_id . '_validate_field',
                $element, $value );
        if ( isset( $element['error'] ) ) {
            $this->_errors = true;
            $_errors = $element['error']->get_error_data();
            $element['#error'] = $_errors[0];
        }
    }

    /**
     * Checks if there are errors.
     *
     * @return type
     */
    public function isError()
    {
        return $this->_errors;
    }

    /**
     * Sets errors to true.
     */
    public function triggerError()
    {
        $this->_errors = true;
    }

    /**
     * Renders form.
     *
     * @return type
     */
    public function renderForm()
    {
        // loop over elements and render them
        return $this->renderElements($this->_elements);
    }

    /**
     * Counts element types.
     *
     * @param type $type
     * @return type
     */
    private function _count($type) {
        if (!isset($this->_count[$type])) {
            $this->_count[$type] = 0;
        }
        $this->_count[$type] += 1;
        return $this->_count[$type];
    }

    /**
     * Check if element is of valid type
     *
     * @param string $type
     * @return boolean
     */
    private function _isValidType($type)
    {
        return in_array($type,
                        array('select', 'checkboxes', 'checkbox', 'radios',
                    'radio', 'textfield', 'textarea', 'file', 'submit', 'reset',
                    'hidden', 'fieldset', 'markup', 'button'));
    }

    /**
     * Renders elements.
     *
     * @param type $elements
     * @return type
     */
    public function renderElements($elements)
    {
        $output = '';
        foreach ($elements as $key => $element) {
            if (!isset($element['#type']) || !$this->_isValidType($element['#type'])) {
                continue;
            }
            if ($element['#type'] != 'fieldset') {
                $output .= $this->renderElement($element);
            } else if (isset($element['#type']) && $element['#type'] == 'fieldset') {
                $buffer = $this->renderElements($element);
                $output .= $this->fieldset($element, 'wrap', $buffer);
            } else if (is_array($element)) {
                $output .= $this->renderElements($element);
            }
        }
        return $output;
    }

    /**
     * Renders element.
     *
     * Depending on element type, it calls class methods.
     *
     * @param array $element
     * @return HTML formatted output
     */
    public function renderElement($element)
    {
        $method = $element['#type'];
        if (!isset($element['#name']) && !in_array( $element['#type'], array('markup', 'checkboxes'))) {
            if (!isset($element['#attributes']['name'])) {
                return '#name or #attributes[\'name\'] required!';
            } else {
                $element['#name'] = $element['#attributes']['name'];
            }
        }
        if (is_callable(array($this, $method))) {
            if (!isset($element['#id'])) {
                if (isset($element['#attributes']['id'])) {
                    $element['#id'] = $element['#attributes']['id'];
                } else {
                    $_id = isset( $this->_id ) ? $this->_id . '-' : '';
                    $element['#id'] = "{$_id}{$element['#type']}-"
                            . $this->_count($element['#type']) . '-' . time();
                }
            }
            if (isset($this->_errors[$element['#id']])) {
                $element['#error'] = $this->_errors[$element['#id']];
            }
            // Add JS validation
            if ( !empty( $element['#validate'] ) ) {
                if ( isset( $element['#validate']['required'] )
                        && !empty( $element['#title'] ) ) {
                    // Asterisk
                    $element['#title'] .= '&#42;';
                }
                $element['#attributes']['data-wpt-validate'] = esc_js( json_encode( apply_filters( 'wptoolset_forms_field_js_validation_data_' . $this->_id,
                                        $element['#validate'] ) ) );
            }
            if ( $element['#type'] == 'radios' && !empty( $element['#options'] ) ) {
                foreach ( $element['#options'] as &$option ) {
                    if ( !empty( $option['#validate'] ) ) {
                        $option['#attributes']['data-wpt-validate'] = esc_js( json_encode( apply_filters( 'wptoolset_forms_field_js_validation_data_' . $this->_id,
                                                $option['#validate'] ) ) );
                    }
                }
            }
            return $this->{$method}($element);
        }
    }

    /**
     * Sets other element attributes.
     *
     * @param array $element
     * @return string
     */
    private function _setElementAttributes($element)
    {
        $attributes = '';
        $error_class = '';//isset($element['#error']) ? ' ' . $this->css_class . '-error ' . $this->css_class . '-' . $element['#type'] . '-error ' . ' form-' . $element['#type'] . '-error ' . $element['#type'] . '-error form-error ' : '';

        $class = $this->css_class . '-' . $element['#type'] . ' form-' . $element['#type'] . ' ' . $element['#type'];
        if ( $this->form_settings['use_bootstrap'] && !in_array( $element['#type'], array( 'hidden', 'submit',  'button' ) ) ) {
            $class .= ' form-control';
        }
        if (isset($element['#attributes'])) {
            foreach ($element['#attributes'] as $attribute => $value) {
                // Prevent undesired elements
                if (in_array($attribute, array('id', 'name'))) {
                    continue;
                }
                // Don't set disabled for checkbox
                if ($attribute == 'disabled' && $element['#type'] == 'checkbox') {
                    continue;
                }
                // Append class values
                if ($attribute == 'class') {
                    $value = $value . ' ' . $class . $error_class;
                }
                // Set return string
                $attributes .= ' ' . $attribute . '="' . $value . '"';
            }
        }
        if (!isset($element['#attributes']['class'])) {
            $attributes .= ' class="' . $class . $error_class . '"';
        }
        return $attributes;
    }

    /**
     * Sets render elements.
     *
     * @param array $element
     */
    private function _setRender($element)
    {
        if (!isset($element['#id'])) {
            if (isset($element['#attributes']['id'])) {
                $element['#id'] = $element['#attributes']['id'];
            } else {
                $element['#id'] = 'form-' . mt_rand();
            }
        }
        $element['_attributes_string'] = $this->_setElementAttributes($element);
        $element['_render'] = array();
        $element['_render']['prefix'] = isset($element['#prefix']) ? $element['#prefix'] . "\r\n" : '';
        $element['_render']['suffix'] = isset($element['#suffix']) ? $element['#suffix'] . "\r\n" : '';
        $element['_render']['before'] = isset($element['#before']) ? $element['#before'] . "\r\n" : '';
        $element['_render']['after'] = isset($element['#after']) ? $element['#after'] . "\r\n" : '';
        $element['_render']['label'] = isset($element['#title']) ? '<label class="'
                . $this->css_class . '-label ' . $this->css_class . '-'
                . $element['#type'] . '-label" for="' . $element['#id'] . '">'
                . stripslashes($element['#title'])
                . '</label>' . "\r\n" : '';
        $element['_render']['title'] = $this->_setElementTitle($element);
        $element['_render']['description'] = isset($element['#description']) ? $this->_setElementDescription($element) : '';
        $element['_render']['error'] = $this->renderError($element) . "\r\n";

        return $element;
    }

    /**
     * Applies pattern to output.
     *
     * Pass element property #pattern to get custom renedered element.
     *
     * @param array $pattern
     *      Accepts: <prefix><suffix><label><title><desription><error>
     * @param array $element
     */
    private function _pattern($pattern, $element)
    {
        $pattern = strtolower($pattern);
        foreach ($element['_render'] as $key => $value) {
            $pattern = str_replace('<' . strtolower($key) . '>', $value, $pattern);
        }
        return $pattern;
    }

    /**
     * Wrapps element in <div></div>.
     *
     * @param arrat $element
     * @param string $output
     * @return string
     */
    private function _wrapElement($element, $output)
    {
        if (empty($element['#inline'])) {
            $classes = array();
            $classes[] = 'form-item';
            $classes[] = 'form-item-' . $element['#type'];
            $classes[] =  $this->css_class . '-item';
            $classes[] =  $this->css_class . '-item-' . $element['#type'];
            if ( $this->form_settings['use_bootstrap'] ) {
                $classes[] = 'form-group';
            }
            if ( preg_match( '/_hidden$/', $element['#id'] ) && !is_admin() ) {
                $classes[] = 'wpt-form-hide-container';
            }
            return sprintf(
                '<div id="%s-wrapper" class="%s">%s</div>',
                $element['#id'],
                implode( ' ', $classes ),
                $output
            );
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for element's title.
     *
     * @param string $element
     * @return string
     */
    private function _setElementTitle($element)
    {
        $output = '';
        if (isset($element['#title'])) {
            $output .= '<div class="title '
                    . $this->css_class . '-title '
                    . $this->css_class . '-title-' . $element['#type'] . ' '
                    . 'title-' . $element['#type'] . '">'
                    . stripslashes($element['#title'])
                    . "</div>\r\n";
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for element's description.
     *
     * @param array $element
     * @return string
     */
    private function _setElementDescription($element)
    {
        if ( empty( $element['#description'] ) ) return '';
        $element['#description'] = stripslashes($element['#description']);
        $output = "\r\n"
                . '<div class="description '
                . $this->css_class . '-description '
                . $this->css_class . '-description-' . $element['#type'] . ' '
                . 'description-' . $element['#type'] . '">'
                . $element['#description'] . "</div>\r\n";
        return $output;
    }

    /**
     * Returns HTML formatted element's error message.
     *
     * Pass #supress_errors in #form element to avoid error rendering.
     *
     * @param array $element
     * @return string
     */
    public function renderError($element)
    {
        if (!isset($element['#error'])) {
            return '';
        }
        $output = '<label class="' . $this->css_class . '-error" for="'
                . $element['#id'] . '" generated="true">'
                . $element['#error'] . '</label>' . "\r\n";
//        $output = '<div class="form-error '
//                . $this->css_class . '-error '
//                . $this->css_class . '-form-error '
//                . $this->css_class . '-' . $element['#type'] . '-error '
//                . $element['#type'] . '-error form-error-label'
//                . '">' . $element['#error'] . '</div>'
//                . "\r\n";
        return $output;
    }

    /**
     * Returns HTML formatted output for fieldset.
     *
     * @param array $element
     * @param string $action open|close|wrap
     * @param string $wrap_content HTML formatted output of child elements
     * @return string
     */
    public function fieldset($element, $action = 'open', $wrap_content = '')
    {
        $collapsible_open = '<div class="fieldset-wrapper">';
        $collapsible_close = '</div>';
        $legend_class = '';
        if (!isset($element['#id'])) {
            $element['#id'] = 'fieldset-' . $this->_count('fieldset');
        }
        if (!isset($element['_attributes_string'])) {
            $element['_attributes_string'] = $this->_setElementAttributes($element);
        }
        if ((isset($element['#collapsible']) && $element['#collapsible'])
                || (isset($element['#collapsed']) && $element['#collapsed'])) {
            $collapsible_open = '<div class="collapsible fieldset-wrapper">';
            $collapsible_close = '</div>';
            $legend_class = ' class="legend-expanded"';
        }
        if (isset($element['#collapsed']) && $element['#collapsed']) {
            $collapsible_open = str_replace('class="', 'class="collapsed ',
                    $collapsible_open);
            $legend_class = ' class="legend-collapsed"';
        }
        $output = '';
        switch ($action) {
            case 'close':
                $output .= $collapsible_close . "</fieldset>\r\n";
                $output .= isset($element['#suffix']) ? $element['#suffix']
                        . "\r\n" : '';
                $output .= "\n\r";
                break;

            case 'open':
                $output .= $collapsible_open;
                $output .= isset($element['#prefix']) ? $element['#prefix']
                        . "\r\n" : '';
                $output .= '<fieldset' . $element['_attributes_string']
                        . ' id="' . $element['#id'] . '">' . "\r\n";
                $output .= isset($element['#title']) ? '<legend'
                        . $legend_class . '>'
                        . stripslashes($element['#title'])
                        . "</legend>\r\n" : '';
                $output .=
                        isset($element['#description']) ? $this->_setElementDescription($element) : '';
                $output .= "\n\r";
                break;

            case 'wrap':
                if (!empty($wrap_content)) {
                    $output .= isset($element['#prefix']) ? $element['#prefix'] : '';
                    $output .= '<fieldset' . $element['_attributes_string']
                            . ' id="' . $element['#id'] . '">' . "\r\n";
                    $output .= '<legend' . $legend_class . '>'
                            . stripslashes($element['#title'])
                            . "</legend>\r\n"
                            . $collapsible_open;
                    $output .= isset($element['#description']) ? $this->_setElementDescription($element) : '';
                    $output .= $wrap_content . $collapsible_close
                            . "</fieldset>\r\n";
                    $output .=
                            isset($element['#suffix']) ? $element['#suffix'] : '';
                    $output .= "\n\r";
                }
                break;
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for checkbox element.
     *
     * @param array $element
     * @return string
     */
    public function checkbox($element)
    {
        $element['#type'] = 'checkbox';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="checkbox" id="'
                . $element['#id'] . '" name="'
                . $element['#name'] . '" value="';
        // Specific: if value is empty force 1 to be rendered
        $element['_render']['element'] .=
                !empty($element['#value']) ? htmlspecialchars($element['#value']) : 1;
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        if (
            (
                !$this->isSubmitted() && (
                    ( !empty($element['#default_value']) && $element['#default_value'] == $element['#value'] )
                    || $element['#checked']
                )
            )
            || ($this->isSubmitted() && !empty($element['#value']))
        ) {
            $element['_render']['element'] .= ' checked="checked"';
        }
        if (!empty($element['#attributes']['disabled']) || !empty($element['#disable'])) {
            $element['_render']['element'] .= ' onclick="javascript:return false; if(this.checked == 1){this.checked=1; return true;}else{this.checked=0; return false;}"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT>&nbsp;<LABEL><ERROR><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for checkboxes element.
     *
     * Renders more than one checkboxes provided as elements in '#options'
     * array element.
     *
     * @param array $element
     * @return string
     */
    public function checkboxes($element)
    {
        $element['#type'] = 'checkboxes';
        $element = $this->_setRender($element);
        $clone = $element;
        $clone['#type'] = 'checkbox';
        $element['_render']['element'] = '';
        foreach ($element['#options'] as $ID => $value) {
            if (!is_array($value)) {
                $value = array('#title' => $ID, '#value' => $value, '#name' => $element['#name'] . '[]');
            }
            $element['_render']['element'] .= $this->checkbox($value);
        }
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><PREFIX><TITLE><DESCRIPTION><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output;
    }

    /**
     * Returns HTML formatted output for radio element.
     *
     * @param array $element
     * @return string
     */
    public function radio($element)
    {
        $element['#type'] = 'radio';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="radio" id="'
                . $element['#id'] . '" name="'
                . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset($element['#value']) ? htmlspecialchars($element['#value']) : $this->_count['radio'];
        $element['_render']['element'] .= '"';
        $element['_render']['element'] .= $element['_attributes_string'];
        $element['_render']['element'] .= ( isset($element['#value'])
                && $element['#value'] === $element['#default_value']) ? ' checked="checked"' : '';
        if (isset($element['#disable']) && $element['#disable']) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        if ( array_key_exists( '#types-value', $element ) ) {
            $element['_render']['element'] .= sprintf( ' data-types-value="%s"', $element['#types-value'] );
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT>&nbsp;<LABEL><ERROR><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for radios elements.
     *
     * Radios are provided via #options array.
     * Requires #name value.
     *
     * @param array $element
     * @return string
     */
    public function radios($element)
    {
        if (!isset($element['#name']) || empty($element['#name'])) {
            return FALSE;
        }
        $element['#type'] = 'radios';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '';
        foreach ($element['#options'] as $ID => $value) {
            $this->_count('radio');
            if (!is_array($value)) {
                $value = array('#title' => $ID, '#value' => $value);
                $value['#inline'] = true;
                $value['#after'] = '<br />';
            }
            $value['#name'] = $element['#name'];
            $value['#default_value'] = isset($element['#default_value']) ? $element['#default_value'] : $value['#value'];
            $value['#disable'] = isset($element['#disable']) ? $element['#disable'] : false;
            $element['_render']['element'] .= $this->radio($value);
        }
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><PREFIX><TITLE><DESCRIPTION><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output;
    }

    /**
     * Returns HTML formatted output for select element.
     *
     * @param array $element
     * @return string
     */
    public function select($element)
    {
        $element['#type'] = 'select';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<select id="' . $element['#id']
                . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'] . ">\r\n";
        $count = 1;
        foreach ($element['#options'] as $id => $value) {
            if (!is_array($value)) {
                $value = array('#title' => $id, '#value' => $value, '#type' => 'option');
            }
            $value['#type'] = 'option';
            if (!isset($value['#value'])) {
                $value['#value'] = $this->_count['select'] . '-' . $count;
                $count += 1;
            }
            $element['_render']['element'] .= '<option value="'
                    . htmlspecialchars($value['#value']) . '"';
            $element['_render']['element'] .= ( $element['#default_value']
                    == $value['#value']) ? ' selected="selected"' : '';
            $element['_render']['element'] .= $this->_setElementAttributes($value);
            if ( array_key_exists( '#types-value', $value ) ) {
                $element['_render']['element'] .= sprintf( ' data-types-value="%s"', $value['#types-value'] );
            }
            $element['_render']['element'] .= '>';
            $element['_render']['element'] .= isset($value['#title']) ? $value['#title'] : $value['#value'];
            $element['_render']['element'] .= "</option>\r\n";
        }
        $element['_render']['element'] .= "</select>\r\n";
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><LABEL><DESCRIPTION><ERROR><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output;
    }

    /**
     * Returns HTML formatted output for textfield element.
     *
     * @param array $element
     * @return string
     */
    public function textfield($element)
    {
        $element['#type'] = 'textfield';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="text" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset($element['#value']) ? htmlspecialchars($element['#value']) : '';
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        if (isset($element['#disable']) && $element['#disable']) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for textfield element.
     *
     * @param array $element
     * @return string
     */
    public function password($element)
    {
        $element['#type'] = 'password';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="password" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset($element['#value']) ? $element['#value'] : '';
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        if (isset($element['#disable']) && $element['#disable']) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for textarea element.
     *
     * @param array $element
     * @return string
     */
    public function textarea($element)
    {
        $element['#type'] = 'textarea';
        if (!isset($element['#attributes']['rows'])) {
            $element['#attributes']['rows'] = 5;
        }
        if (!isset($element['#attributes']['cols'])) {
            $element['#attributes']['cols'] = 1;
        }
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<textarea id="' . $element['#id']
                . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'] . '>';
        $element['_render']['element'] .= isset($element['#value']) ? htmlspecialchars($element['#value']) : '';
        $element['_render']['element'] .= '</textarea>' . "\r\n";
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><LABEL><DESCRIPTION><ERROR><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for file upload element.
     *
     * @param array $element
     * @return string
     */
    public function file($element)
    {
        $element['#type'] = 'file';
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="file" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'];
        if (isset($element['#disable']) && $element['#disable']) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><DESCRIPTION><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        $output = $this->_wrapElement($element, $output);
        return $output;
    }

    /**
     * Returns HTML formatted output for markup element.
     *
     * @param array $element
     * @return string
     */
    public function markup($element)
    {
        return $element['#markup'];
    }

    /**
     * Returns HTML formatted output for hidden element.
     *
     * @param array $element
     * @return string
     */
    public function hidden($element)
    {
        $element['#type'] = 'hidden';
        $element = $this->_setRender($element);
        $output = '<input type="hidden" id="' . $element['#id'] . '"  name="'
                . $element['#name'] . '" value="';
        $output .= isset($element['#value']) ? $element['#value'] : 1;
        $output .= '"' . $element['_attributes_string'] . ' />';
        return $output;
    }

    /**
     * Returns HTML formatted output for reset button element.
     *
     * @param array $element
     * @return string
     */
    public function reset($element)
    {
        return $this->submit($element, 'reset', 'Reset');
    }

    /**
     * Returns HTML formatted output for button element.
     *
     * @param array $element
     * @return string
     */
    public function button($element)
    {
        return $this->submit($element, 'button', 'Button');
    }

    /**
     * Returns HTML formatted output for radio element.
     *
     * Used by reset and button.
     *
     * @param array $element
     * @param string $type
     * @param string $title
     * @return string
     */
    public function submit($element, $type = 'submit', $title = 'Submit')
    {
        $element['#type'] = $type;
        $element = $this->_setRender($element);
        $element['_render']['element'] = '<input type="' . $type . '" id="'
                . $element['#id'] . '"  name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset($element['#value']) ? $element['#value'] : $title;
        $element['_render']['element'] .= '"' . $element['_attributes_string']
                . ' />';
        $pattern = isset($element['#pattern']) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern($pattern, $element);
        return $output;
    }

    /**
     * Searches and returns submitted data for element.
     *
     * @param type $element
     * @return type mixed
     */
    public function getSubmittedData($element)
    {
        $name = $element['#name'];
        if (strpos($name, '[') === false) {
            if ($element['#type'] == 'file') {
                return $_FILES[$name]['tmp_name'];
            }
            return isset($_REQUEST[$name]) ? $_REQUEST[$name] : in_array($element['#type'],
                            array('textfield', 'textarea')) ? '' : 0;
        }

        $parts = explode('[', $name);
        $parts = array_map(create_function('&$a', 'return trim($a, \']\');'),
                $parts);
        if (!isset($_REQUEST[$parts[0]])) {
            return in_array($element['#type'], array('textfield', 'textarea')) ? '' : 0;
        }
        $search = $_REQUEST[$parts[0]];
        for ($index = 0; $index < count($parts); $index++) {
            $key = $parts[$index];
            // We're at the end but no data retrieved
            if (!isset($parts[$index + 1])) {
                return in_array($element['#type'],
                                array('textfield', 'textarea')) ? '' : 0;
            }
            $key_next = $parts[$index + 1];
            if ($index > 0) {
                if (!isset($search[$key])) {
                    return in_array($element['#type'],
                                    array('textfield', 'textarea')) ? '' : 0;
                } else {
                    $search = $search[$key];
                }
            }
            if (is_array($search) && array_key_exists($key_next, $search)) {
                if (!is_array($search[$key_next])) {
                    return $search[$key_next];
                }
            }
        }
        return 0;
    }

}
