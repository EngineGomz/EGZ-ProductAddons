<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.linkedin.com/in/eugine-gomez-9b1b952b7
 * @since      1.0.0
 *
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Geny_Tech_Settings
 * @subpackage Geny_Tech_Settings/admin
 * @author     Eugine Gomez <engine.gomz@gmail.com>
 */
class Geny_Tech_Settings_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_addon_products_metabox'));
	}
	
	public function add_meta_boxes() {
		add_meta_box(
			'addon_products',
			__('Add-On Products', 'geny-tech-settings'),
			array($this, 'render_addon_products_metabox'),
			'product',
			'normal',
			'default'
		);
	
		add_meta_box(
			'product_warranty',
			__('Warranty', 'geny-tech-settings'),
			array($this, 'render_product_warranty_metabox'),
			'product',
			'normal',
			'default'
		);
	}
	
	public function render_addon_products_metabox($post) {
		wp_nonce_field('save_addon_products', 'addon_products_nonce');
	
		$addon_product_ids = get_post_meta($post->ID, '_addon_product_ids', true) ?: [];
		$args = array(
			'post_type' => 'product',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		);
		$products = get_posts($args);
	
		echo '<p>' . __('Select add-on products:', 'geny-tech-settings') . '</p>';
		echo '<select multiple name="addon_products[]" style="width: 100%;">';
		foreach ($products as $product) {
			$selected = in_array($product->ID, $addon_product_ids) ? 'selected' : '';
			echo '<option value="' . esc_attr($product->ID) . '" ' . $selected . '>' . esc_html($product->post_title) . '</option>';
		}
		echo '</select>';
		echo '<p class="description">' . __('Hold down the CTRL (Windows) / Command (Mac) key to select multiple products.', 'geny-tech-settings') . '</p>';
	}
	
	public function render_product_warranty_metabox($post) {
		wp_nonce_field('save_product_warranty', 'product_warranty_nonce');
	
		$warranty_product_id = get_post_meta($post->ID, '_warranty_product_id', true);
	
		echo '<p>' . __('Enter the Product ID for the warranty:', 'geny-tech-settings') . '</p>';
		echo '<input type="number" name="warranty_product_id" value="' . esc_attr($warranty_product_id) . '" style="width: 100%;" />';
		echo '<p class="description">' . __('The product ID will be used to fetch the warranty details.', 'geny-tech-settings') . '</p>';
	}
	
	public function save_addon_products_metabox($post_id) {
		if (
			!isset($_POST['addon_products_nonce']) || 
			!wp_verify_nonce($_POST['addon_products_nonce'], 'save_addon_products')
		) {
			return;
		}
	
		if (
			!isset($_POST['product_warranty_nonce']) || 
			!wp_verify_nonce($_POST['product_warranty_nonce'], 'save_product_warranty')
		) {
			return;
		}
	
		if (isset($_POST['addon_products'])) {
			$addon_product_ids = array_map('intval', $_POST['addon_products']);
			update_post_meta($post_id, '_addon_product_ids', $addon_product_ids);
		} else {
			delete_post_meta($post_id, '_addon_product_ids');
		}
	
		if (isset($_POST['warranty_product_id'])) {
			$warranty_product_id = intval($_POST['warranty_product_id']);
			if (get_post_status($warranty_product_id) === 'publish') {
				update_post_meta($post_id, '_warranty_product_id', $warranty_product_id);
			} else {
				delete_post_meta($post_id, '_warranty_product_id');
			}
		}
	}
	

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/geny-tech-settings-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		// This is a Test
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/geny-tech-settings-admin.js', array( 'jquery' ), $this->version, false );

	}

}
