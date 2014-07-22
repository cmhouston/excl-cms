<?php

function wptoolset_form( $form_id, $config = array() ){
    global $wptoolset_forms;
    $html = $wptoolset_forms->form( $form_id, $config );
    return apply_filters( 'wptoolset_form', $html, $config );
}

function wptoolset_form_field( $form_id, $config, $value = array() ){
    global $wptoolset_forms;
    $html = $wptoolset_forms->field( $form_id, $config, $value );    
    return apply_filters( 'wptoolset_fieldform', $html, $config, $form_id );
}

//function wptoolset_form_field_edit( $form_id, $config ){
//    global $wptoolset_forms;
//    $html = $wptoolset_forms->fieldEdit( $form_id, $config );
//    return apply_filters( 'wptoolset_fieldform_edit', $html, $config, $form_id );
//}

function wptoolset_form_validate_field( $form_id, $config, $value ){
    global $wptoolset_forms;
    return $wptoolset_forms->validate_field( $form_id, $config, $value );
}

function wptoolset_form_conditional_check( $config ){
    global $wptoolset_forms;
    return $wptoolset_forms->checkConditional( $config );
}

function wptoolset_form_add_conditional( $form_id, $config ){
    global $wptoolset_forms;
    return $wptoolset_forms->addConditional( $form_id, $config );
}

function wptoolset_form_filter_types_field( $field, $post_id = null ){
    global $wptoolset_forms;
    return $wptoolset_forms->filterTypesField( $field, $post_id );
}

function wptoolset_form_field_add_filters( $type ){
    global $wptoolset_forms;
    $wptoolset_forms->addFieldFilters( $type );
}

function wptoolset_form_get_conditional_data( $post_id ){
    global $wptoolset_forms;
    return $wptoolset_forms->getConditionalData( $post_id );
}

function wptoolset_strtotime( $date, $format = null ){
    global $wptoolset_forms;
    return $wptoolset_forms->strtotime( $date, $format );
}

function wptoolset_timetodate( $timestamp, $format = null ){
    global $wptoolset_forms;
    return $wptoolset_forms->timetodate( $timestamp, $format );
}