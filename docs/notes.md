# Bugs #

* If you get an error that looks like this on wordpress:  
	`Fatal error: Call to a member function get_language_for_element() on a non-object in /home/gkuncheria/excl.dreamhosters.com/qa/wp-content/plugins/types/embedded/includes/wpml.php on line 562`  
	then add this line to wp-config.php  
	`define('PLL_WPML_COMPAT', false);`  
	to disable WPML compatibility mode for Polylang. More info at http://polylang.wordpress.com/2014/05/30/fatal-error-call-to-a-member-function-on-a-non-object-in/