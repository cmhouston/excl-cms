<?php
error_reporting(E_ALL^E_NOTICE);
require 'classes/class.form_factory.php';


function pre($val) {
    echo "<pre>";
    print_r($val);
    echo "</pre>";
}


$frm = new FormFactory();
$data = array('type'=>'textfield',
	           'title'=>'title',
	           'description'=>'description',
	           'name'=>'ciao'  ,
	           'validation' => array()             
    );
    
$frm->addFormField('myname','textfield', $data);

echo $frm->createForm();
?>