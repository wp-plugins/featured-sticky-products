<?php
/*
Plugin Name:WPSC Featured Products
Plugin URI: http://getshopped.com/featured-products
Description: A skeleton module for wpec
Version: 0.1
Author: Instinct Entertainment
Author URI: http://getshopped.com/
*/

//check to see whether the user is an admin or not.




	
	
/**
* wpsc_featured_products_scripts_and_styles function.
* 
* @access public
* @return void
*/
function wpsc_featured_products_scripts_and_styles() { 
	$plugin_url = untrailingslashit(plugin_dir_url(__FILE__));
	wp_enqueue_style('featured_products_css', $plugin_url.'/css-and-js/featured-products.css');
	if (is_admin()) {
		wp_enqueue_script('featured_products_js', $plugin_url.'/css-and-js/featured-products.js');
	}
}

add_action('wpsc_admin_edit_products_js', 'wpsc_featured_products_scripts_and_styles');

if(strpos($_SERVER['SCRIPT_NAME'], "wp-admin") === false) {
	add_action('init', 'wpsc_featured_products_scripts_and_styles');
}


if (is_admin()) {
 	/**
 	 * wpsc_update_featured_products function.
 	 * 
 	 * @access public
 	 * @return void
 	 */
 	function wpsc_update_featured_products() { 	
		global $wpdb;
		$is_ajax = (int)(bool)$_POST['ajax'];
		$product_id = absint($_GET['product_id']);
		check_admin_referer('feature_product_' . $product_id);
	  
		$status = (int)(bool)get_product_meta($product_id, 'featured_product');
		switch($status) {
			case 1: 
				$new_status = false;
			break;
			
			case 0:
			default:
				$new_status = true;
			break;
		} 
		
		update_product_meta($product_id, 'featured_product', (int)$new_status);
		if($is_ajax == true) {
			 if($new_status == true) :?>
jQuery('.featured_toggle_<?php echo $product_id; ?>').html("<img class='gold-star' src='<?php echo WPSC_URL; ?>/images/gold-star.gif' alt='<?php _e('Unmark as Featured', 'wpsc'); ?>' title='<?php _e('Unmark as Featured', 'wpsc'); ?>' />");
			<?php else: ?>
jQuery('.featured_toggle_<?php echo $product_id; ?>').html("<img class='grey-star' src='<?php echo WPSC_URL; ?>/images/grey-star.gif' alt='<?php _e('Mark as Featured', 'wpsc'); ?>' title='<?php _e('Mark as Featured', 'wpsc'); ?>' />");
			<?php endif; 
			exit();
		
		}
		//$sendback = add_query_arg('featured', "1", wp_get_referer());
		wp_redirect(wp_get_referer());
	 	exit();
 	}
 
 
	if($_REQUEST['wpsc_admin_action'] == 'update_featured_product') {
		add_action('admin_init', 'wpsc_update_featured_products');
	}
	
	
	/**
	 * wpsc_featured_products_forms function.
	 * 
	 * @access public
	 * @param mixed $product_data. (default: null)
	 * @return void
	 */
	function wpsc_featured_products_forms($product_data = null) {
		global $wpdb;
		$output='';
		if ($product_data == 'empty') {
			$display = "style='display:none;'";
		}
		$closed_state = ((array_search(__FUNCTION__, $product_data['closed_postboxes']) !== false) ? 'closed' : '');
		$hidden_state = ((array_search(__FUNCTION__, $product_data['hidden_postboxes']) !== false) ? 'style="display: none;"' : '');
		?>
		<div id='wpsc_product_variation_forms' class='postbox <?php echo $closed_state;	?>' <?php echo $hidden_state; ?>>
		
			<h3 class='hndle'><?php echo __('Featured Product Settings', 'wpsc'); ?></h3>
			
			<div class='inside'>
				<?php
				
				?>
			</div>
		</div>
		<?php 
	
	}
	
	/**
	 * wpsc_add_featured_products function.
	 * 
	 * @access public
	 * @param mixed $order
	 * @return void
	 */
	function wpsc_add_featured_products($order) {
		if(!in_array('wpsc_featured_products_forms', $order)) {
			$order[] = 'wpsc_featured_products_forms';
		}
		return $order;
	}
	
	
	/**
	 * wpsc_featured_products_toggle function.
	 * 
	 * @access public
	 * @param mixed $product_id
	 * @return void
	 */
	function wpsc_featured_products_toggle($product_id) {
		global $wpdb;							
		$featured_product_url = wp_nonce_url("admin.php?wpsc_admin_action=update_featured_product&amp;product_id=$product_id}", 'feature_product_'.$product_id);
		?>
		<a class="wpsc_featured_product_toggle featured_toggle_<?php echo $product_id; ?>" href='<?php echo $featured_product_url; ?>' >
			<?php if((int)(bool)get_product_meta($product_id, 'featured_product')) :?>
				<img class='gold-star' src='<?php echo WPSC_URL; ?>/images/gold-star.gif' alt='<?php _e('Unmark as Featured', 'wpsc'); ?>' title='<?php _e('Unmark as Featured', 'wpsc'); ?>' />
			<?php else: ?>
				<img class='grey-star' src='<?php echo WPSC_URL; ?>/images/grey-star.gif' alt='<?php _e('Mark as Featured', 'wpsc'); ?>' title='<?php _e('Mark as Featured', 'wpsc'); ?>' />
			<?php endif; ?>
		</a>
		<?php	
	}
	
	
	
	//add_filter('wpsc_products_page_forms', 'wpsc_add_featured_products');
	
	add_action('wpsc_admin_product_checkbox', 'wpsc_featured_products_toggle', 10, 1);
}

/**
 * wpsc_display_products_page function.
 * 
 * @access public
 * @param mixed $query
 * @return void
 */

function wpsc_display_featured_products_page($query) {
	global $wpdb, $wpsc_query, $wpsc_theme_path;	
	
	//echo "<pre>".print_r($wpsc_query->query_vars, true)."</pre>";
	
	if(($wpsc_query->query_vars['product_id'] == 0) && ($wpsc_query->query_vars['product_url_name'] == null)) {  
		$temp_wpsc_query = new WPSC_query($query);
		list($wpsc_query, $temp_wpsc_query) = array($temp_wpsc_query, $wpsc_query); // swap the wpsc_query objects
		
		//echo "<pre>".print_r($wpsc_query , true)."</pre>";
		$this_directory = plugin_dir_path(__FILE__);
		$GLOBALS['nzshpcrt_activateshpcrt'] = true;
		
		ob_start();
		if(file_exists($this_directory."template/featured-products-template.php")) {
			include($this_directory."template/featured-products-template.php");
		}
		$output = ob_get_contents();
		ob_end_clean();
		
		list($temp_wpsc_query, $wpsc_query) = array($wpsc_query, $temp_wpsc_query); // swap the wpsc_query objects back
	}
	return $output;
}



/**
 * wpsc_featured_products_shorttag function.
 * 
 * @access public
 * @param mixed $atts
 * @return void
 */
function wpsc_featured_products_shorttag($atts) {
	global $wpdb;
	$product_id = $wpdb->get_var("SELECT `product_id`
	FROM `".WPSC_TABLE_PRODUCTMETA."`
	WHERE `meta_key`
	IN (
	'featured_product'
	)
	AND `meta_value`
	IN ( 1 )
	ORDER BY RAND()
	LIMIT 1" );
	if($product_id > 0) {		
		//$number_per_page = get_option('use_pagination') ? get_option('wpsc_products_per_page') : 0;
		$query = shortcode_atts(array(
			'product_id' => $product_id,
			'product_url_name' => null,
			'product_name' => null,
			'category_id' => 0,
			'category_url_name' => null,
			'tag' => null,
			'price' => 0,
			'limit_of_items' => 0,
			'sort_order' => null,
			'number_per_page' => 1,
			'page' => 0,
			'custom_query' => true
		), $atts);
		$output = wpsc_display_featured_products_page($query);
	}
	return $output;
}

function wpsc_featured_products_hooked() {
	echo wpsc_featured_products_shorttag(null);
}


add_action('wpsc_top_of_products_page', 'wpsc_featured_products_hooked', 12);


add_shortcode('wpsc_featured_products', 'wpsc_featured_products_shorttag');


?>