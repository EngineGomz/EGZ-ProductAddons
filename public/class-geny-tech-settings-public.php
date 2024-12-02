<?php

/**
 * https://chatgpt.com/c/67447915-d02c-800f-b884-148fdd188e88
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/eugine-gomez-9b1b952b7
 * @since      1.0.0
 *
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/public
 * @author     Eugine Gomez <engine.gomz@gmail.com>
 */
class Geny_Tech_Settings_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	
		add_action('woocommerce_before_single_variation', array($this, 'display_meta_box_on_product_page'));
	
		add_filter('woocommerce_get_item_data', array($this, 'display_addons_and_warranty_in_cart'), 10, 2);
	
		// add_action('woocommerce_checkout_create_order_line_item', array($this, 'save_addons_and_warranty_in_order'), 10, 4);

		// Adding Cart Item Data - [not used]
		// add_action('woocommerce_add_cart_item_data', array($this,'process_addons_and_warranty'), 10, 2);


		add_action('woocommerce_add_to_cart', array($this, 'addons_warranty_products_to_cart'), 10, 6);
	
		add_filter('woocommerce_add_to_cart_redirect', array($this, 'reset_fields_after_add_to_cart'));
	}	


	public function display_meta_box_on_product_page() {
		global $post;
	
		// Display add-on products - Admin side
		$addon_product_ids = get_post_meta($post->ID, '_addon_product_ids', true) ?: [];
		if (!empty($addon_product_ids)) {
			echo '<div class="addon-products">';
			echo '<h4 class="border-divide">' . __('Add-On Products', 'geny-tech-settings') . '</h4>';
	
			foreach ($addon_product_ids as $addon_product_id) {
				$addon_product = wc_get_product($addon_product_id);
				if ($addon_product) {
					$thumbnail_id = get_post_thumbnail_id($addon_product_id);
					$product_image = $thumbnail_id
						? wp_get_attachment_image($thumbnail_id, 'thumbnail', false, ['style' => 'width:100px; height:100px; object-fit:cover; border:1px solid #ccc; border-radius:4px;'])
						: '<div style="width:100px; height:100px; background-color:#ccc; display:flex; align-items:center; justify-content:center; text-align:center; font-size:14px; color:#666;">No Image</div>';
	
					echo '<div style="margin-bottom: 10px; display: flex; align-items: center;">';
					echo '<div style="margin-right: 10px;">' . $product_image . '</div>';
					echo '<label style="flex: 1;">';
					echo '<input type="checkbox" name="addon_products[]" value="' . esc_attr($addon_product_id) . '" style="margin-right: 10px;">';
					echo esc_html($addon_product->get_name()) . ' - ' . wc_price($addon_product->get_price());
					echo '</label>';
					echo '</div>';
				}
			}
	
			echo '</div>';
		}
	
		// Display warranty
		$warranty_product_id = get_post_meta($post->ID, '_warranty_product_id', true);
		if ($warranty_product_id) {
			$warranty_product = wc_get_product($warranty_product_id);
			if ($warranty_product) {
				echo '<h4 class="border-divide">' . __('Warranty', 'geny-tech-settings') . '</h4>';
				echo '<div class="warranty-section">';
				echo '<input type="checkbox" name="warranty_agreement" value="yes">';
				echo '<img src="' . esc_url(get_the_post_thumbnail_url($warranty_product_id)) . '" alt="' . esc_attr($warranty_product->get_name()) . '" style="max-width: 100px;">';
				echo '<div class="warranty-product">';
				echo '<h4>' . esc_html($warranty_product->get_name()) . '</h4>';
				echo '<p>' . esc_html($warranty_product->get_short_description()) . '</p>';
				echo '</div>';
				echo wc_price($warranty_product->get_price());
				echo '</div>';
			}
		}
	}

	// public function process_addons_and_warranty($cart_item_data, $cart_item_key) {
		
	// 	// Handle add-on products
	// 	if (isset($_POST['addon_products']) && is_array($_POST['addon_products'])) {
	// 		$addon_product_ids = array_map('intval', $_POST['addon_products']);
	// 		$cart_item_data['addon_product_ids'] = $addon_product_ids; // Save add-on product IDs to cart item
	// 	}
	
	// 	// Handle warranty agreement
	// 	if (isset($_POST['warranty_agreement']) && $_POST['warranty_agreement'] === 'yes') {
	// 		$cart_item_data['warranty_agreed'] = true; // Mark warranty as agreed
	// 	}
	
	// 	return $cart_item_data;
	// }

    public function display_addons_and_warranty_in_cart($item_data, $cart_item) {
		// Add-on Products
		if (isset($cart_item['addon_product_ids']) && !empty($cart_item['addon_product_ids'])) {
			$addon_names = [];
			foreach ($cart_item['addon_product_ids'] as $addon_product_id) {
				$addon_product = wc_get_product($addon_product_id);
				if ($addon_product) {
					$addon_names[] = $addon_product->get_name() . ' (' . wc_price($addon_product->get_price()) . ')';
				}
			}
			$item_data[] = [
				'name' => __('Add-On Products', 'geny-tech-settings'),
				'value' => implode(', ', $addon_names),
			];
		}
	
		// Warranty
		if (isset($cart_item['warranty_agreed']) && $cart_item['warranty_agreed']) {
			$item_data[] = [
				'name' => __('Warranty', 'geny-tech-settings'),
				'value' => __('Warranty agreed', 'geny-tech-settings'),
			];
		}
		
		// log_custom_data($item_data);

		return $item_data;
	}

    public function save_addons_and_warranty_in_order($item, $cart_item_key, $values, $order) {
        // Add-On Products
		if (isset($values['addon_product_ids']) && !empty($values['addon_product_ids'])) {
			$addon_names = [];
			foreach ($values['addon_product_ids'] as $addon_product_id) {
				$addon_product = wc_get_product($addon_product_id);
				if ($addon_product) {
					$addon_names[] = $addon_product->get_name();
				}
			}
			$item->add_meta_data(__('Add-On Products', 'geny-tech-settings'), implode(', ', $addon_names));
		}

		// Warranty
		if (isset($values['warranty_agreed']) && $values['warranty_agreed']) {
			$item->add_meta_data(__('Warranty', 'geny-tech-settings'), __('Warranty agreed', 'geny-tech-settings'));
		}
    }

	public function addons_warranty_products_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
		
		log_custom_data($cart_item_key);
		log_custom_data($product_id);
		log_custom_data($quantity);
		log_custom_data($variation_id);
		log_custom_data($variation);
		log_custom_data($cart_item_data);

		log_custom_data($_POST['addon_products']);
		log_custom_data($_POST['warranty_agreement']);


		// Add-on products
		// if (isset($_POST['addon_products']) && is_array($_POST['addon_products'])) {
			
		// 	// log_custom_data($_POST['addon_products']);
		// 	foreach ($_POST['addon_products'] as $addon_product_id) {
		// 		$addon_product = wc_get_product($addon_product_id);
		// 		if ($addon_product && $addon_product->is_purchasable()) {
		// 			WC()->cart->add_to_cart($addon_product_id, 1);
		// 		}
		// 	}
		// }
	
		// // Warranty
		// if (isset($_POST['warranty_agreement']) && $_POST['warranty_agreement'] === 'yes') {
		// 	// log_custom_data($_POST['warranty_agreement']);
		// 	$warranty_product_id = get_post_meta($product_id, '_warranty_product_id', true);
		// 	if ($warranty_product_id) {
		// 		$warranty_product = wc_get_product($warranty_product_id);
		// 		if ($warranty_product && $warranty_product->is_purchasable()) {
		// 			WC()->cart->add_to_cart($warranty_product_id, 1);
		// 		}
		// 	}
		// }
	}

	public function reset_fields_after_add_to_cart($url) {
		// Check if the add-on product form was submitted
		// if (isset($_POST['addon_products_submitted']) && $_POST['addon_products_submitted'] === '1') {
			// Redirect to the current page without POST data
			$product_id = absint($_POST['add-to-cart']); // Get product ID from the form
			$url = get_permalink($product_id); // Redirect to the product page
			
			return esc_url($url); // Return sanitized URL for redirection
		// }
		
		// return esc_url($url);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Geny_Tech_Settings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Geny_Tech_Settings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/geny-tech-settings-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Geny_Tech_Settings_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Geny_Tech_Settings_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/geny-tech-settings-public.js', array( 'jquery' ), $this->version, false );

	}

}
