<?php
/*
Plugin Name: Color and Label Variations for WooCommerce
Plugin URI: http://saturnplugins.com
Description: WordPress plugin replaces WooCommerce selects with variation swatches.
Author: saturnplugins
Version: 1.0.0
Author URI: http://saturnplugins.com/
*/

class SWV_Color_Label_Variations {

	protected $image_size;

	public function __construct() {
		define( 'SWV_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'SWV_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
		define( 'SWV_VERSION', '1.0.0' );

		load_plugin_textdomain( 'color-and-label-variations-for-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		$this->image_size = array(
			'width' => 30,
			'height' => 30,
		);

		include_once( SWV_PATH . '/inc/core-functions.php' );

		if ( is_admin() ) {
			include_once( SWV_PATH . '/admin/class-swv-admin.php' );
		}

		if ( ! is_admin() ) {
			include_once( SWV_PATH . '/inc/class-swv-render.php' );
		}

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {
		$this->image_size = apply_filters( 'swv_image_size', $this->image_size );
	}

	public function get_image_size() {
		return $this->image_size;
	}
}

add_action( 'plugins_loaded', 'swv_init' );
function swv_init() {
	if ( function_exists( 'WC' ) ) {
		$GLOBALS['SWV_Instance'] = new SWV_Color_Label_Variations();
	}
}
