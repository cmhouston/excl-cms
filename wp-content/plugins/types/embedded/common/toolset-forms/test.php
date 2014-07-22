<?php
add_action( 'admin_init', '_wptoolset_forms_test_fields' );
add_action( 'admin_init', '_wptoolset_forms_test_form' );
add_action( 'edit_form_after_editor', '_wptoolset_forms_test_fields_render' );
add_action( 'admin_footer', '_wptoolset_forms_test_form_render' );

function _wptoolset_forms_test_fields() {
    global $_html_test;
    $fields = types_get_fields(); //debug($fields);
    foreach ( $fields as $field ) {
        $config = wptoolset_forms_types_filter_field( $field, 'testme' );
        $_html_test .= wptoolset_form_field( 'post', $config );
    }
}

function _wptoolset_forms_test_form() {
    global $_html_test_2;
    $form = wptoolset_form( 'types-form' );
    $fields = types_get_fields(); //debug($fields);
    foreach ( $fields as $field ) {
        $config = wptoolset_forms_types_filter_field( $field, 'testme' );
        $form->addField( $config );
    }
    $form->addSubmit();
    $_html_test_2 = $form->createForm( 'types-form' );
}

function _wptoolset_forms_test_fields_render() {
    global $_html_test; //debug($_html_test);
    echo '<h2>Test group of elements</h2>' . $_html_test;
}

function _wptoolset_forms_test_form_render() {
    global $_html_test_2;
    echo '<h2>Test form</h2>' . $_html_test_2;
}