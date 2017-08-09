<?php

function swv_get_tax_attribute( $taxonomy ) {
	global $wpdb;

	$attr = substr( $taxonomy, 3 );
	$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

	return $attr;
}