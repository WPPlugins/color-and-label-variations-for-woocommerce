<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SWV_Admin {

	public function __construct() {
	  add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'admin_init', array( $this, 'init_attribute' ) );
	}

	public function enqueue_scripts() {
	  $screen = get_current_screen();

	  if ( false === strpos( $screen->id, 'pa_' ) ) {
	    return;
    }

	  global $woocommerce;

	  wp_enqueue_style( 'wp-color-picker' );
	  wp_enqueue_script( 'wp-color-picker' );

	  wp_enqueue_media();

	  wp_enqueue_style( 'swv_style', SWV_URL . '/assets/css/admin.css', array(), SWV_VERSION );
	  wp_enqueue_script( 'swv_script', SWV_URL . '/assets/js/admin.js', array( 'jquery' ), SWV_VERSION );

	  wp_localize_script( 'swv_script', 'swv_params', array(
	    'placeholder_url' => $woocommerce->plugin_url() . '/assets/images/placeholder.png',
	    'choose_image'    => __( 'Choose Image', 'color-and-label-variations-for-woocommerce' ),
    ) );
  }

	public function init_attribute() {

		$taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $taxonomies ) ) {
			return;
		}

		foreach ( $taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_field' ) );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( $this, 'edit_field' ), 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'add_column' ) );
			add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array( $this, 'add_column_attribute' ), 10, 3 );
		}

	  add_action( 'created_term', array( $this, 'save_attribute' ), 10, 3 );
	  add_action( 'edit_term', array( $this, 'save_attribute' ), 10, 3 );

  }

	public function save_attribute( $term_id, $tt_id, $taxonomy ) {
      if ( ! empty( $_POST['swv_type'] ) ) {
	      update_term_meta( $term_id, 'swv_type', sanitize_text_field( $_POST['swv_type'] ) );
	      update_term_meta( $term_id, 'swv_color', sanitize_text_field( $_POST['swv_color'] ) );
	      update_term_meta( $term_id, 'swv_image', intval( $_POST['swv_image'] ) );
      }
	}

	public function add_field( $taxonomy ) {
	  global $woocommerce;

	  /**
	   * @var SWV_Color_Label_Variations $SWV_Instance
	   */
	  global $SWV_Instance;

	  $image_size = $SWV_Instance->get_image_size();

		?>
    <div class="form-field">
      <label for="swv_type"><?php esc_html_e( 'Variation Type', 'color-and-label-variations-for-woocommerce' ); ?></label>
      <select name="swv_type" id="swv_type" class="js-swv-type postform">
        <option value=""><?php esc_html_e( 'Default', 'color-and-label-variations-for-woocommerce' ); ?></option>
        <option value="color"><?php esc_html_e( 'Color', 'color-and-label-variations-for-woocommerce' ); ?></option>
        <option value="image"><?php esc_html_e( 'Image', 'color-and-label-variations-for-woocommerce' ); ?></option>
        <option value="label"><?php esc_html_e( 'Label', 'color-and-label-variations-for-woocommerce' ); ?></option>
      </select>
    </div>
    <div class="form-field js-swv-color-field hidden">
      <label><?php esc_html_e( 'Color', 'color-and-label-variations-for-woocommerce' ); ?></label>
      <input type="text" name="swv_color" class="js-swv-color" />
    </div>
    <div class="form-field js-swv-image-field hidden">
      <label><?php esc_html_e( 'Image', 'color-and-label-variations-for-woocommerce' ); ?></label>
      <div class="swv-image-wrapper" style="width: <?php echo esc_attr( $image_size['width'] ) ?>px; height: <?php echo esc_attr( $image_size['height'] ) ?>px;">
        <img class="js-swv-image-placeholder" src="<?php echo $woocommerce->plugin_url() . '/assets/images/placeholder.png' ?>" />
      </div>
      <button type="submit" class="button js-swv-update-image"><?php _e( 'Upload/Add image', 'color-and-label-variations-for-woocommerce' ); ?></button>
      <button type="submit" class="button js-swv-remove-image"><?php _e( 'Remove image', 'color-and-label-variations-for-woocommerce' ); ?></button>
      <input type="hidden" name="swv_image" class="js-swv-image" />
    </div>
		<?php
	}

	public function edit_field( $term, $taxonomy ) {
	  global $woocommerce;

	  /**
	   * @var SWV_Color_Label_Variations $SWV_Instance
	   */
	  global $SWV_Instance;

	  $image_size = $SWV_Instance->get_image_size();

	  $attr  = swv_get_tax_attribute( $taxonomy );
	  $type = get_term_meta( $term->term_id, 'swv_type', true );
	  $color = get_term_meta( $term->term_id, 'swv_color', true );
	  $image = get_term_meta( $term->term_id, 'swv_image', true );
	  $image_url = $woocommerce->plugin_url() . '/assets/images/placeholder.png';

	  if ( ! empty( $image ) ) {
	    $image_url = wp_get_attachment_url( $image );
    }
    ?>
    <tr class="form-field">
      <th>
        <label for="swv_type"><?php esc_html_e( 'Variation Type', 'color-and-label-variations-for-woocommerce' ); ?></label>
      </th>
      <td>
        <select name="swv_type" id="swv_type" class="js-swv-type postform">
          <option value=""><?php esc_html_e( 'Default', 'color-and-label-variations-for-woocommerce' ); ?></option>
          <option value="color" <?php selected( 'color', $type ); ?>><?php esc_html_e( 'Color', 'color-and-label-variations-for-woocommerce' ); ?></option>
          <option value="image" <?php selected( 'image', $type ); ?>><?php esc_html_e( 'Image', 'color-and-label-variations-for-woocommerce' ); ?></option>
          <option value="label" <?php selected( 'label', $type ); ?>><?php esc_html_e( 'Label', 'color-and-label-variations-for-woocommerce' ); ?></option>
        </select>
      </td>
    </tr>
    <tr class="form-field js-swv-color-field hidden">
      <th>
        <label><?php esc_html_e( 'Color', 'color-and-label-variations-for-woocommerce' ); ?></label>
      </th>
      <td>
        <input type="text" name="swv_color" class="js-swv-color" value="<?php echo esc_attr( $color ); ?>" />
      </td>
    </tr>
    <tr class="form-field js-swv-image-field hidden">
      <th>
        <label><?php esc_html_e( 'Image', 'color-and-label-variations-for-woocommerce' ); ?></label>
      </th>
      <td>
        <div class="swv-image-wrapper" style="width: <?php echo esc_attr( $image_size['width'] ) ?>px; height: <?php echo esc_attr( $image_size['height'] ) ?>px;">
          <img class="js-swv-image-placeholder" src="<?php echo esc_url( $image_url ); ?>" />
        </div>
        <button type="submit" class="button js-swv-update-image"><?php _e( 'Upload/Add image', 'color-and-label-variations-for-woocommerce' ); ?></button>
        <button type="submit" class="button js-swv-remove-image"><?php _e( 'Remove image', 'color-and-label-variations-for-woocommerce' ); ?></button>
        <input type="hidden" name="swv_image" class="js-swv-image" value="<?php echo esc_attr( $image ); ?>" />
      </td>
    </tr>
    <?php
	}

	public function add_column( $columns ) {
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns[$this->meta_key] = __( 'Label', 'color-and-label-variations-for-woocommerce' );

		unset( $columns['cb'] );

		$columns = array_merge( $new_columns, $columns );

		return $columns;
	}

	public function add_column_attribute( $columns, $column, $id ) {
    global $woocommerce;

	  /**
	   * @var SWV_Color_Label_Variations $SWV_Instance
	   */
	  global $SWV_Instance;

	  $image_size = $SWV_Instance->get_image_size();


	  $attr  = swv_get_tax_attribute( $_REQUEST['taxonomy'] );

    $type = get_term_meta( $id, 'swv_type', true );

    switch ( $type ) {
      case 'color':
        $color = get_term_meta( $id, 'swv_color', true );
        break;
      case 'image':
        $image = get_term_meta( $id, 'swv_image', true );
        $image_url = $woocommerce->plugin_url() . '/assets/images/placeholder.png';

        if ( ! empty( $image ) ) {
          $image_url = wp_get_attachment_url( $image );
        }
        break;
      case 'label':
        break;

    }
	  ?>
    <?php if ( 'color' == $type ) : ?>
      <span class="swv-button-color" style="background: <?php echo esc_attr( $color ); ?>"></span>
    <?php elseif ( 'image' == $type ) : ?>
      <div class="swv-image-wrapper" style="width: <?php echo esc_attr( $image_size['width'] ) ?>px; height: <?php echo esc_attr( $image_size['height'] ) ?>px;">
        <img src="<?php echo esc_url( $image_url ); ?>" />
      </div>
    <?php elseif ( 'label' == $type ) : ?>
      <span class="swv-button-label"><?php esc_html_e( 'Label', 'color-and-label-variations-for-woocommerce' ); ?></span>
    <?php endif; ?>
    <?php
	}
}

return new SWV_Admin();