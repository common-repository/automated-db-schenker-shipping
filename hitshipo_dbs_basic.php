<?php
/**
 * Plugin Name: Automated DB Schenker Shipping
 * Plugin URI: https://wordpress.org/plugins/automated-db-schenker-shipping/
 * Description: Manual Shipping Rates, Shipping label, Pickup automation included.
 * Version: 1.3.2
 * Author: HITShipo
 * Author URI: https://hitshipo.com/
 * Developer: HITShipo
 * Developer URI: https://hitshipo.com/
 * Text Domain: HITShipo
 * Domain Path: /i18n/languages/
 *
 * WC requires at least: 2.6
 * WC tested up to: 8.2
 *
 *
 * @package WooCommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'HITSHIPO_DBS_PLUGIN_FILE' ) ) {
	define( 'HITSHIPO_DBS_PLUGIN_FILE', __FILE__ );
}


// set HPOS feature compatible by plugin
add_action(
    'before_woocommerce_init',
    function () {
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }
);


// Include the main WooCommerce class.
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	if( !class_exists('hitshipo_dbs_parent') ){
		Class hitshipo_dbs_parent
		{
			public $hpos_enabled = false;
			public $new_prod_editor_enabled = false;
			private $errror = '';
			public function __construct() {
				if (get_option("woocommerce_custom_orders_table_enabled") === "yes") {
					$this->hpos_enabled = true;
				}
				if (get_option("woocommerce_feature_product_block_editor_enabled") === "yes") {
					$this->new_prod_editor_enabled = true;
				}
				add_action( 'woocommerce_shipping_init', array($this,'hits_dbs_init') );
				add_action( 'init', array($this,'hits_dbs_order_status_update') );
				add_filter( 'woocommerce_shipping_methods', array($this,'hits_dbs_method') );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'hits_dbs_plugin_action_links' ) );
				add_action( 'add_meta_boxes', array($this, 'hits_create_dbs_shipping_meta_box' ));
				if ($this->hpos_enabled) {
					add_action( 'woocommerce_process_shop_order_meta', array($this, 'hits_create_dbs_shipping'), 10, 1 );
				} else {
					add_action( 'save_post', array($this, 'hits_create_dbs_shipping'), 10, 1 );
				}
				
				add_filter( 'bulk_actions-edit-shop_order', array($this, 'hits_dbs_bulk_order_menu'), 10, 1 );
				add_filter( 'handle_bulk_actions-edit-shop_order', array($this, 'hits_dbs_bulk_create_order'), 10, 3 );
				add_action( 'admin_notices', array($this, 'hits_dbs_bulk_label_action_admin_notice' ) );
				add_action( 'admin_menu', array($this, 'hit_dbs_menu_page' ));
				// add_filter( 'woocommerce_product_data_tabs', array($this,'hits_dbs_product_data_tab') );
				// add_action( 'woocommerce_process_product_meta', array($this,'hits_dbs_save_product_options' ));
				// add_filter( 'woocommerce_product_data_panels', array($this,'hits_dbs_product_option_view') );

				// add_action( 'woocommerce_checkout_order_processed', array( $this, 'hits_dbs_wc_checkout_order_processed' ) );
				// add_action( 'woocommerce_thankyou', array( $this, 'hits_dbs_wc_checkout_order_processed' ) );
				add_action( 'woocommerce_order_status_processing', array( $this, 'hits_dbs_wc_checkout_order_processed' ) );
				// add_action('woocommerce_order_details_after_order_table', array( $this, 'hits_dbs_track' ) );
				
				$general_settings = get_option('hits_dbs_main_settings');
				$general_settings = empty($general_settings) ? array() : $general_settings;

				if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' ){
					add_action( 'woocommerce_product_options_shipping', array($this,'hits_dbs_choose_vendor_address' ));
					add_action( 'woocommerce_process_product_meta', array($this,'hits_dbs_save_product_meta' ));

					// Edit User Hooks
					add_action( 'edit_user_profile', array($this,'hits_dbs_define_credentails') );
					add_action( 'edit_user_profile_update', array($this, 'hits_dbs_save_user_fields' ));

				}
			
			}
			function hit_dbs_menu_page() {
				$general_settings = get_option('hits_dbs_main_settings');
				if (isset($general_settings['hits_dbs_integration_key']) && !empty($general_settings['hits_dbs_integration_key'])) {
					add_menu_page(__( 'DBS Labels', 'hitshipo_dbs' ), 'DBS Labels', 'manage_options', 'hit-dbs-labels', array($this,'my_label_page_contents'), '', 6);
				}
			}
			function my_label_page_contents(){
				$general_settings = get_option('hits_dbs_main_settings');
				$url = site_url();
				if (isset($general_settings['hits_dbs_integration_key']) && !empty($general_settings['hits_dbs_integration_key'])) {
					echo "<iframe style='width: 100%;height: 100vh;' src='https://app.hitshipo.com/embed/label.php?shop=".$url."&key=".$general_settings['hits_dbs_integration_key']."&show=ship'></iframe>";
				}
            }
			public function hits_dbs_bulk_order_menu( $actions ) {
				// echo "<pre>";print_r($actions);die();
				$actions['create_label_dbs_shipo'] = __( 'Create DBS Labels - HITShipo', 'woocommerce' );
				return $actions;
			}

			public function hits_dbs_bulk_create_order($redirect_to, $action, $order_ids){
				$success = 0;
				$failed = 0;
				$failed_ids = [];
				if($action == "create_label_dbs_shipo"){
					
					if(!empty($order_ids)){
						$create_shipment_for = "default";
						$service_code = isset($general_settings['hits_dbs_bulk_service_dom']) ? $general_settings['hits_dbs_bulk_service_dom'] : 'f';
						$ship_content = 'Shipment Content';
						$pickup_mode = 'manual';
						
						foreach($order_ids as $key => $order_id){
							$order = wc_get_order( $order_id );
							if($order){

									$order_data = $order->get_data();
									$order_id = $order_data['id'];
									$order_currency = $order_data['currency'];

									// $order_shipping_first_name = $order_data['shipping']['first_name'];
									// $order_shipping_last_name = $order_data['shipping']['last_name'];
									// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
									// $order_shipping_address_1 = $order_data['shipping']['address_1'];
									// $order_shipping_address_2 = $order_data['shipping']['address_2'];
									// $order_shipping_city = $order_data['shipping']['city'];
									// $order_shipping_state = $order_data['shipping']['state'];
									// $order_shipping_postcode = $order_data['shipping']['postcode'];
									// $order_shipping_country = $order_data['shipping']['country'];
									// $order_shipping_phone = $order_data['billing']['phone'];
									// $order_shipping_email = $order_data['billing']['email'];

									$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
									$order_shipping_first_name = $shipping_arr['first_name'];
									$order_shipping_last_name = $shipping_arr['last_name'];
									$order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
									$order_shipping_address_1 = $shipping_arr['address_1'];
									$order_shipping_address_2 = $shipping_arr['address_2'];
									$order_shipping_city = $shipping_arr['city'];
									$order_shipping_state = $shipping_arr['state'];
									$order_shipping_postcode = $shipping_arr['postcode'];
									$order_shipping_country = $shipping_arr['country'];
									$order_shipping_phone = $order_data['billing']['phone'];
									$order_shipping_email = $order_data['billing']['email'];
									
									
									$items = $order->get_items();
									$pack_products = array();
									$general_settings = get_option('hits_dbs_main_settings',array());

									if($general_settings['hits_dbs_country'] != $order_shipping_country){
										$service_code = isset($general_settings['hits_dbs_bulk_service_intl']) ? $general_settings['hits_dbs_bulk_service_intl'] : 'f';
									}

									foreach ( $items as $item ) {
										$product_data = $item->get_data();

										$product = array();
										$product['product_name'] = str_replace('"', '', $product_data['name']);
										$product['product_quantity'] = $product_data['quantity'];
										$product['product_id'] = $product_data['product_id'];

										$product_variation_id = $item->get_variation_id();
										if(empty($product_variation_id)){
											$getproduct = wc_get_product( $product_data['product_id'] );
										}else{
											$getproduct = wc_get_product( $product_variation_id );
										}
										
										$woo_weight_unit = get_option('woocommerce_weight_unit');
										$woo_dimension_unit = get_option('woocommerce_dimension_unit');

										$hits_dbs_mod_weight_unit = $hits_dbs_mod_dim_unit = '';

										if(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM')
										{
											$hits_dbs_mod_weight_unit = 'kg';
											$hits_dbs_mod_dim_unit = 'cm';
										}elseif(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'LB_IN')
										{
											$hits_dbs_mod_weight_unit = 'lbs';
											$hits_dbs_mod_dim_unit = 'in';
										}
										else
										{
											$hits_dbs_mod_weight_unit = 'kg';
											$hits_dbs_mod_dim_unit = 'cm';
										}

										$product['price'] = $getproduct->get_price();

										if(!$product['price']){
											$product['price'] = (isset($product_data['total']) && isset($product_data['quantity'])) ? number_format(($product_data['total'] / $product_data['quantity']), 2) : 0;
										}

										if ($woo_dimension_unit != $hits_dbs_mod_dim_unit) {
										$prod_width = $getproduct->get_width();
										$prod_height = $getproduct->get_height();
										$prod_depth = $getproduct->get_length();

										//	( $dimension, $to_unit, $from_unit );
										$product['width'] = (!empty($prod_width) && $prod_width > 0) ? round(wc_get_dimension( $prod_width, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5 ;
										$product['height'] = (!empty($prod_height) && $prod_height > 0) ? round(wc_get_dimension( $prod_height, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5 ;
										$product['depth'] = (!empty($prod_depth) && $prod_depth > 0) ? round(wc_get_dimension( $prod_depth, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5 ;

										}else {
											$product['width'] = (!empty($getproduct->get_width()) && $getproduct->get_width() > 0) ? round($getproduct->get_width(), 2) : 0.5;
											$product['height'] = (!empty($getproduct->get_height()) && $getproduct->get_height() > 0) ? round($getproduct->get_height(), 2) : 0.5;
											$product['depth'] = (!empty($getproduct->get_length()) && $getproduct->get_length() > 0) ? round($getproduct->get_length(), 2) : 0.5;
										}
										
										if ($woo_weight_unit != $hits_dbs_mod_weight_unit) {
											$prod_weight = $getproduct->get_weight();
											$product['weight'] = (!empty($prod_weight) && $prod_weight > 0) ? round(wc_get_weight( $prod_weight, $hits_dbs_mod_weight_unit, $woo_weight_unit ), 2) : 0.1 ;
										}else{
											$product['weight'] = (!empty($getproduct->get_weight()) && $getproduct->get_weight() > 0) ? round($getproduct->get_weight(), 2) : 0.1;
										}

										$pack_products[] = $product;
										
									}
									
									$custom_settings = array();
									$custom_settings['default'] = array(
														'hits_dbs_site_id' => $general_settings['hits_dbs_site_id'],
														'hits_dbs_shipper_name' => $general_settings['hits_dbs_shipper_name'],
														'hits_dbs_company' => $general_settings['hits_dbs_company'],
														'hits_dbs_mob_num' => $general_settings['hits_dbs_mob_num'],
														'hits_dbs_email' => $general_settings['hits_dbs_email'],
														'hits_dbs_address1' => $general_settings['hits_dbs_address1'],
														'hits_dbs_address2' => $general_settings['hits_dbs_address2'],
														'hits_dbs_city' => $general_settings['hits_dbs_city'],
														'hits_dbs_state' => $general_settings['hits_dbs_state'],
														'hits_dbs_zip' => $general_settings['hits_dbs_zip'],
														'hits_dbs_country' => $general_settings['hits_dbs_country'],
														'hits_dbs_gstin' => $general_settings['hits_dbs_gstin'],
														'hits_dbs_con_rate' => $general_settings['hits_dbs_con_rate'],
														'service_code' => $service_code,
														'hits_dbs_label_email' => $general_settings['hits_dbs_label_email'],
													);
									$vendor_settings = array();
								// 	if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' && isset($general_settings['hits_dbs_v_labels']) && $general_settings['hits_dbs_v_labels'] == 'yes'){
								// 	// Multi Vendor Enabled
								// 	foreach ($pack_products as $key => $value) {
								// 		$product_id = $value['product_id'];
								// if ($this->hpos_enabled) {
								// 	$hpos_prod_data = wc_get_product($product_id);
								// 	$dbs_account = $hpos_prod_data->get_meta("hits_dbs_address");
								// } else {
								// 	$dbs_account = get_post_meta($product_id,'hits_dbs_address', true);
								// }
								// 		if(empty($dbs_account) || $dbs_account == 'default'){
								// 			$dbs_account = 'default';
								// 			if (!isset($vendor_settings[$dbs_account])) {
								// 				$vendor_settings[$dbs_account] = $custom_settings['default'];
								// 			}
											
								// 			$vendor_settings[$dbs_account]['products'][] = $value;
								// 		}

								// 		if($dbs_account != 'default'){
								// 			$user_account = get_post_meta($dbs_account,'hits_dbs_vendor_settings', true);
								// 			$user_account = empty($user_account) ? array() : $user_account;
								// 			if(!empty($user_account)){
								// 				if(!isset($vendor_settings[$dbs_account])){

								// 					$vendor_settings[$dbs_account] = $custom_settings['default'];
													
								// 				if($user_account['hits_dbs_site_id'] != '' && $user_account['hits_dbs_site_pwd'] != '' && $user_account['hits_dbs_acc_no'] != ''){
													
								// 					$vendor_settings[$dbs_account]['hits_dbs_site_id'] = $user_account['hits_dbs_site_id'];

								// 					if($user_account['hits_dbs_site_pwd'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_site_pwd'] = $user_account['hits_dbs_site_pwd'];
								// 					}

								// 					if($user_account['hits_dbs_acc_no'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_acc_no'] = $user_account['hits_dbs_acc_no'];
								// 					}

								// 					$vendor_settings[$dbs_account]['hits_dbs_import_no'] = !empty($user_account['hits_dbs_import_no']) ? $user_account['hits_dbs_import_no'] : '';
													
								// 				}

								// 				if ($user_account['hits_dbs_address1'] != '' && $user_account['hits_dbs_city'] != '' && $user_account['hits_dbs_state'] != '' && $user_account['hits_dbs_zip'] != '' && $user_account['hits_dbs_country'] != '' && $user_account['hits_dbs_shipper_name'] != '') {
													
								// 					if($user_account['hits_dbs_shipper_name'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_shipper_name'] = $user_account['hits_dbs_shipper_name'];
								// 					}

								// 					if($user_account['hits_dbs_company'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_company'] = $user_account['hits_dbs_company'];
								// 					}

								// 					if($user_account['hits_dbs_mob_num'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_mob_num'] = $user_account['hits_dbs_mob_num'];
								// 					}

								// 					if($user_account['hits_dbs_email'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_email'] = $user_account['hits_dbs_email'];
								// 					}

								// 					if ($user_account['hits_dbs_address1'] != '') {
								// 						$vendor_settings[$dbs_account]['hits_dbs_address1'] = $user_account['hits_dbs_address1'];
								// 					}

								// 					$vendor_settings[$dbs_account]['hits_dbs_address2'] = $user_account['hits_dbs_address2'];
													
								// 					if($user_account['hits_dbs_city'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_city'] = $user_account['hits_dbs_city'];
								// 					}

								// 					if($user_account['hits_dbs_state'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_state'] = $user_account['hits_dbs_state'];
								// 					}

								// 					if($user_account['hits_dbs_zip'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_zip'] = $user_account['hits_dbs_zip'];
								// 					}

								// 					if($user_account['hits_dbs_country'] != ''){
								// 						$vendor_settings[$dbs_account]['hits_dbs_country'] = $user_account['hits_dbs_country'];
								// 					}

								// 					$vendor_settings[$dbs_account]['hits_dbs_gstin'] = $user_account['hits_dbs_gstin'];
								// 					$vendor_settings[$dbs_account]['hits_dbs_con_rate'] = $user_account['hits_dbs_con_rate'];

								// 				}
													
								// 					if(isset($general_settings['hits_dbs_v_email']) && $general_settings['hits_dbs_v_email'] == 'yes'){
								// 						$user_dat = get_userdata($dbs_account);
								// 						$vendor_settings[$dbs_account]['hits_dbs_label_email'] = $user_dat->data->user_email;
								// 					}
													

								// 					if($order_data['shipping']['country'] != $vendor_settings[$dbs_account]['hits_dbs_country']){
								// 						$vendor_settings[$dbs_account]['service_code'] = empty($service_code) ? $user_account['hits_dbs_def_inter'] : $service_code;
								// 					}else{
								// 						$vendor_settings[$dbs_account]['service_code'] = empty($service_code) ? $user_account['hits_dbs_def_dom'] : $service_code;
								// 					}
								// 				}
								// 				unset($value['product_id']);
								// 				$vendor_settings[$dbs_account]['products'][] = $value;
								// 			}
								// 		}

								// 	}

								// }

								if(empty($vendor_settings)){
									$custom_settings['default']['products'] = $pack_products;
								}else{
									$custom_settings = $vendor_settings;
								}

								if(!empty($general_settings) && isset($general_settings['hits_dbs_integration_key']) && isset($custom_settings[$create_shipment_for])){
									$mode = 'live';
									if(isset($general_settings['hits_dbs_test']) && $general_settings['hits_dbs_test']== 'yes'){
										$mode = 'test';
									}

									$execution = 'manual';
									
									$boxes_to_shipo = array();
									if (isset($general_settings['hits_dbs_packing_type']) && $general_settings['hits_dbs_packing_type'] == "box") {
										if (isset($general_settings['hits_dbs_boxes']) && !empty($general_settings['hits_dbs_boxes'])) {
											foreach ($general_settings['hits_dbs_boxes'] as $box) {
												if ($box['enabled'] != 1) {
													continue;
												}else {
													$boxes_to_shipo[] = $box;
												}
											}
										}
									}

									$pic_frm = $pic_to = $ves_arr = $ves_dep = date('c');

									if (isset($general_settings['hits_dbs_pic_ready_from']) && !empty($general_settings['hits_dbs_pic_ready_from']) && $general_settings['hits_dbs_pic_ready_from'] > 0) {
										$pic_frm = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_from'].' days'));
									}

									if (isset($general_settings['hits_dbs_pic_ready_to']) && !empty($general_settings['hits_dbs_pic_ready_to']) && $general_settings['hits_dbs_pic_ready_to'] > 0) {
										$pic_to = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_to'].' days'));
									}

									if (isset($general_settings['hits_dbs_ves_arr']) && !empty($general_settings['hits_dbs_ves_arr']) && $general_settings['hits_dbs_ves_arr'] > 0) {
										$ves_arr = date('c', strtotime('+'.$general_settings['hits_dbs_ves_arr'].' days'));
									}

									if (isset($general_settings['hits_dbs_ves_dep']) && !empty($general_settings['hits_dbs_ves_dep']) && $general_settings['hits_dbs_ves_dep'] > 0) {
										$ves_dep = date('c', strtotime('+'.$general_settings['hits_dbs_ves_dep'].' days'));
									}
									
									$cod = ( isset($general_settings['hits_dbs_cod']) && $general_settings['hits_dbs_cod'] == 'yes') ? "Y" : "N";
									
									$data = array();
									$data['integrated_key'] = $general_settings['hits_dbs_integration_key'];
									$data['order_id'] = $order_id;
									$data['exec_type'] = $execution;
									$data['mode'] = $mode;
									$data['carrier_type'] = "dbs";
									$data['ship_price'] = 0;
									$data['meta'] = array(
										"site_id" => $custom_settings[$create_shipment_for]['hits_dbs_site_id'],
										"t_company" => $order_shipping_company,
										"t_address1" => str_replace('"', '', $order_shipping_address_1),
										"t_address2" => str_replace('"', '', $order_shipping_address_2),
										"t_city" => $order_shipping_city,
										"t_state" => $order_shipping_state,
										"t_postal" => $order_shipping_postcode,
										"t_country" => $order_shipping_country,
										"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
										"t_phone" => $order_shipping_phone,
										"t_email" => $order_shipping_email,
										"insurance" => ( isset($general_settings['hits_dbs_insure']) && $general_settings['hits_dbs_insure'] == 'yes' ) ? 'Y' : 'N',
										"pack_this" => "Y",
										"products" => $custom_settings[$create_shipment_for]['products'],
										"pack_algorithm" => $general_settings['hits_dbs_packing_type'],
										"boxes" => $boxes_to_shipo,
										"max_weight" => $general_settings['hits_dbs_max_weight'],
										"cod" => $cod,
										"service_code" => $custom_settings[$create_shipment_for]['service_code'],
										"email_alert" => ( isset($general_settings['hits_dbs_email_alert']) && ($general_settings['hits_dbs_email_alert'] == 'yes') ) ? "Y" : "N",
										"shipment_content" => $ship_content,
										"s_company" => $custom_settings[$create_shipment_for]['hits_dbs_company'],
										"s_address1" => $custom_settings[$create_shipment_for]['hits_dbs_address1'],
										"s_address2" => $custom_settings[$create_shipment_for]['hits_dbs_address2'],
										"s_city" => $custom_settings[$create_shipment_for]['hits_dbs_city'],
										"s_state" => $custom_settings[$create_shipment_for]['hits_dbs_state'],
										"s_postal" => $custom_settings[$create_shipment_for]['hits_dbs_zip'],
										"s_country" => $custom_settings[$create_shipment_for]['hits_dbs_country'],
										"gstin" => $custom_settings[$create_shipment_for]['hits_dbs_gstin'],
										"s_name" => $custom_settings[$create_shipment_for]['hits_dbs_shipper_name'],
										"s_phone" => $custom_settings[$create_shipment_for]['hits_dbs_mob_num'],
										"s_email" => $custom_settings[$create_shipment_for]['hits_dbs_email'],
										"label_size" => $general_settings['hits_dbs_print_size'],
										"sent_email_to" => $custom_settings[$create_shipment_for]['hits_dbs_label_email'],
										"pic_from" => $pic_frm,
					    				"pic_to" => $pic_to,
					    				"ves_arr" => $ves_arr,
					    				"ves_dep" => $ves_dep,
										"payment_con" => (isset($general_settings['hits_dbs_pay_con']) ? $general_settings['hits_dbs_pay_con'] : 'S'),
										"cus_payment_con" => (isset($general_settings['hits_dbs_cus_pay_con']) ? $general_settings['hits_dbs_cus_pay_con'] : ''),
										"translation" => ( (isset($general_settings['hits_dbs_translation']) && $general_settings['hits_dbs_translation'] == "yes" ) ? 'Y' : 'N'),
										"translation_key" => (isset($general_settings['hits_dbs_translation_key']) ? $general_settings['hits_dbs_translation_key'] : ''),
										"loc_type_sender" => (isset($general_settings['hits_dbs_loc_type_sender']) ? $general_settings['hits_dbs_loc_type_sender'] : ''),
										"loc_type_receiver" => (isset($general_settings['hits_dbs_loc_type_receiver']) ? $general_settings['hits_dbs_loc_type_receiver'] : ''),
										"con_type_sender" => (isset($general_settings['hits_dbs_con_per_type_sender']) ? $general_settings['hits_dbs_con_per_type_sender'] : ''),
										"con_type_receiver" => (isset($general_settings['hits_dbs_con_per_type_receiver']) ? $general_settings['hits_dbs_con_per_type_receiver'] : ''),
										"incoterm_air" => (isset($general_settings['hits_dbs_incoterm_air']) ? $general_settings['hits_dbs_incoterm_air'] : ''),
										"incoterm_ocean" => (isset($general_settings['hits_dbs_incoterm_ocean']) ? $general_settings['hits_dbs_incoterm_ocean'] : ''),
										"incoterm_land" => (isset($general_settings['hits_dbs_incoterm_land']) ? $general_settings['hits_dbs_incoterm_land'] : ''),
										"incoterm_loc_air" => (isset($general_settings['hits_dbs_incoterm_loc_air']) ? $general_settings['hits_dbs_incoterm_loc_air'] : ''),
										"incoterm_loc_ocean" => (isset($general_settings['hits_dbs_incoterm_loc_ocean']) ? $general_settings['hits_dbs_incoterm_loc_ocean'] : ''),
										"incoterm_loc_land" => (isset($general_settings['hits_dbs_incoterm_loc_land']) ? $general_settings['hits_dbs_incoterm_loc_land'] : ''),
										"ser_type_air" => (isset($general_settings['hits_dbs_ser_type_air']) ? $general_settings['hits_dbs_ser_type_air'] : ''),
										"ser_type_ocean" => (isset($general_settings['hits_dbs_ser_type_ocean']) ? $general_settings['hits_dbs_ser_type_ocean'] : ''),
										"ship_pack_type" => (isset($general_settings['hits_dbs_ship_pack_type']) ? $general_settings['hits_dbs_ship_pack_type'] : ''),
										"container_type" => (isset($general_settings['hits_dbs_container_type']) ? $general_settings['hits_dbs_container_type'] : ''),
										"food" => ( (isset($general_settings['hits_dbs_food']) && !empty($general_settings['hits_dbs_food']) && $general_settings['hits_dbs_food'] == 'yes') ? 'Y' : 'N'),
										"heat" => ( (isset($general_settings['hits_dbs_heat']) && !empty($general_settings['hits_dbs_heat']) && $general_settings['hits_dbs_heat'] == 'yes') ? 'Y' : 'N'),
										"wight_dim_unit" => (isset($general_settings['hits_dbs_weight_unit']) ? $general_settings['hits_dbs_weight_unit'] : 'KG_CM'),
										"ship_charge" => isset($order_data['total']) ? $order_data['total'] : 0,
										"label" => $create_shipment_for
										// "" => (isset($general_settings['']) ? $general_settings[''] : ''),
									);
									 	// echo '<pre>';print_r($data);die();
										//For Bulk Label
										// $request_url = "http://localhost/hitshipo/label_api/create_shipment.php";		
										$request_url = "https://app.hitshipo.com/label_api/create_shipment.php";
										$result = wp_remote_post($request_url, array(
											'method' => 'POST',
											'timeout' => 60,
											'sslverify' => 0,
											'headers'     => array(),
			    							'cookies'     => array(),
											'body' => json_encode($data),
											'sslverify'   => FALSE
										));
										
										if (is_array($result) && !isset($result['body'])) {
											$failed += 1;
											$failed_ids[] = $order_id;
											continue;
										}
										$output = (is_array($result) && isset($result['body'])) ? json_decode($result['body'],true) : [];
										
										if($output){
											if(isset($output['status'])){

												if(isset($output['status']) && is_array($output) && $output['status'] != 'success'){
													// update_option('hits_dbs_status_'.$order_id, $output['status'][0]);
													$failed += 1;
													$failed_ids[] = $order_id;

												}else if(isset($output['status']) && $output['status'] == 'success'){
													$output['user_id'] = $create_shipment_for;
													$val = get_option('hits_dbs_values_'.$order_id, []);
													$result_arr = array();
													if(!empty($val)){
														$result_arr = json_decode($val, true);
													}	
														$result_arr[] = $output;											

													update_option('hits_dbs_values_'.$order_id, json_encode($result_arr));

													$success += 1;
													
												}
												
											}else{
												$failed += 1;
												$failed_ids[] = $order_id;
											}
										}else{
											$failed += 1;
											$failed_ids[] = $order_id;
										}
									}
							}else{
								$failed += 1;
							}
							
						}
						return $redirect_to = add_query_arg( array(
							'success_lbl' => $success,
							'failed_lbl' => $failed,
							// 'failed_lbl_ids' => implode( ',', rtrim($failed_ids, ",") ),
						), $redirect_to );
					}
				}
				
			}

			function hits_dbs_bulk_label_action_admin_notice() {
				if(isset($_GET['success_lbl']) && isset($_GET['failed_lbl'])){
					printf( '<div id="message" class="updated fade"><p>
						Generated labels: '.esc_html($_GET['success_lbl']).' Failed Label: '.esc_html($_GET['failed_lbl']).' </p></div>');
				}

			}

			public function hits_dbs_track($order){
				$general_settings = get_option('hits_dbs_main_settings',array());
				$order_id = $order->get_id();
				$json_data = get_option('hits_dbs_values_'.$order_id);

				if (!empty($json_data) && isset($general_settings['hits_dbs_trk_status_cus']) && $general_settings['hits_dbs_trk_status_cus'] == "yes") {

					$array_data_to_track = json_decode($json_data, true);
					$track_datas = array();

					if (isset($array_data_to_track[0])) {
						$track_datas = $array_data_to_track;
					}else {
						$track_datas[] = $array_data_to_track;
					}
					$trk_count = 1;
					$tot_trk_count = count($track_datas);
					
// echo '<pre>';print_r($array_data_to_track);echo '<br/>'; print_r($track_datas);die();

					if ($track_datas) {

						echo '<div style = "box-shadow: 1px 1px 10px 1px #d2d2d2;">
							<div style= "font-size: 1.5rem; padding: 20px;"><img src="'.content_url().'/plugins/hitshipo_dbs/dbs.jpg" style="width: 70px; height: 70px; border-radius: 50%; vertical-align: middle; margin-right: 20px">
							DB Schenker Tracking</div>';

						foreach ($track_datas as $value) {
							if (isset($general_settings['hits_dbs_site_id']) && isset($general_settings['hits_dbs_site_pwd'])) {
								$trk_no = $value['tracking_num'];
								$user_id = $value['user_id'];		//Test track No : 2192079993	'.$trk_no.'

								$xml = '<?xml version="1.0" encoding="UTF-8"?>
										<req:KnownTrackingRequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com TrackingRequestKnown.xsd" schemaVersion="1.0">
											<Request>
												<ServiceHeader>
													<MessageTime>2002-06-25T11:28:56-08:00</MessageTime>
													<MessageReference>1234567890123456789012345678</MessageReference>
													<SiteID>'.$general_settings['hits_dbs_site_id'].'</SiteID>
													<Password></Password>
												</ServiceHeader>
											</Request>
											<LanguageCode>en</LanguageCode>
											<AWBNumber>'.$trk_no.'</AWBNumber>
											<LevelOfDetails>ALL_CHECK_POINTS</LevelOfDetails>
											<PiecesEnabled>B</PiecesEnabled>
										</req:KnownTrackingRequest>';

								// $ch=curl_init();
								// 	curl_setopt($ch, CURLOPT_URL,""); 
								// 	curl_setopt($ch, CURLOPT_POST, 1);
								// 	curl_setopt($ch, CURLOPT_HEADER, 0);
								// 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
								// 	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
								// 	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
								// 	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
								// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								// 	$result = curl_exec($ch);
$result ='';
									if (!empty($result)) {
										$xml = simplexml_load_string($result);
										$xml = json_decode(json_encode($xml), true);
// echo '<pre>';print_r($xml);die();
										if(isset($xml['AWBInfo']['ShipmentInfo']['ShipmentEvent']) && $xml['AWBInfo']['ShipmentInfo']['ShipmentEvent']){

											$events = $xml['AWBInfo']['ShipmentInfo']['ShipmentEvent'];
											$last_event_status = '';
											
											$event_count = count($events);
        									if (isset($events[$event_count -1])) {
        										$last_event_status = $events[$event_count -1]['ServiceEvent']['Description'];
        									}

											$to_disp = '<div style= "background-color:#4CBB87; width: 100%; height: 80px; display: flex; flex-direction: row;">
															<div style= "color: #ecf0f1; display: flex; flex-direction: column; align-items: center; padding: 23px; width: 50%;">Package Status: '.$last_event_status.'</div>
															<span style= "border-left: 4px solid #fdfdfd; margin-top: 20px; height: 40px;"></span>
															<div style= "color: #ecf0f1; display: flex; flex-direction: column; align-items: center; padding: 12px; width: 50%;">Package '.$trk_count.' of '.$tot_trk_count.'
																<span>Tracking No: '.$trk_no.'</span>
															</div>
														</div>
														<div style= "padding-bottom: 5px;">
															<ul style= "list-style: none; padding-bottom: 5px;">';
											
        									foreach ($events as $key => $value) {
        										$event_status = $value['ServiceEvent']['Description'];
        										$event_loc = $value['ServiceArea']['Description'];
        										$event_time = date('h:i - A', strtotime($value['Time']));
        										$event_date = date('M d Y', strtotime($value['Date']));
        										
// echo '<pre>';echo '<h4>XML</h4>';print_r($value);print_r($events);die();
        										$to_disp .= '<li style= "display: flex; flex-direction: row;">
																<div style= "display: flex;margin-top: 0px; margin-bottom: 0px; ">
																	<div style="border-left:1px #ecf0f1 solid; position: relative; left:161px; height:150%; margin-top: -28px; z-index: -1;"></div>
																	<div style= "display: flex; flex-direction: column; width: 120px; align-items: end;">
																		<p style= "font-weight: bold; margin: 0;">'.$event_date.'</p>
																		<p style= "margin: 0; color: #4a5568;">'.$event_time.'</p>
																	</div>
																	<div style= "display: flex; flex-direction: column; width: 80px; align-items: center;">';

														if ($value['ServiceEvent']['EventCode'] == "OK") {
															$to_disp .= '<img style="width: 34px; height: 34px;" src="data:image/svg+xml;charset=utf-8;base64,PHN2ZyB4bWxuczpza2V0Y2g9Imh0dHA6Ly93d3cuYm9oZW1pYW5jb2RpbmcuY29tL3NrZXRjaC9ucyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgMTI4IDEyOCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTI4IDEyOCI+PHN0eWxlIHR5cGU9InRleHQvY3NzIj4uc3Qwe2ZpbGw6IzRDQkI4Nzt9IC5zdDF7ZmlsbDojRkZGRkZGO308L3N0eWxlPjxnIGlkPSJEZWxpdmVkIiBza2V0Y2g6dHlwZT0iTVNMYXllckdyb3VwIj48cGF0aCBpZD0iT3ZhbC03LUNvcHktMiIgc2tldGNoOnR5cGU9Ik1TU2hhcGVHcm91cCIgY2xhc3M9InN0MCIgZD0iTTY0IDEyOGMzNS4zIDAgNjQtMjguNyA2NC02NHMtMjguNy02NC02NC02NC02NCAyOC43LTY0IDY0IDI4LjcgNjQgNjQgNjR6Ii8+PHBhdGggaWQ9IlNoYXBlIiBza2V0Y2g6dHlwZT0iTVNTaGFwZUdyb3VwIiBjbGFzcz0ic3QxIiBkPSJNODIuNSA1My4ybC0zLjQtMy40Yy0uNS0uNS0xLS43LTEuNy0uN3MtMS4yLjItMS43LjdsLTE2LjIgMTYuNS03LjMtNy40Yy0uNS0uNS0xLS43LTEuNy0uN3MtMS4yLjItMS43LjdsLTMuNCAzLjRjLS41LjUtLjcgMS0uNyAxLjdzLjIgMS4yLjcgMS43bDkgOS4xIDMuNCAzLjRjLjUuNSAxIC43IDEuNy43czEuMi0uMiAxLjctLjdsMy40LTMuNCAxNy45LTE4LjJjLjUtLjUuNy0xIC43LTEuN3MtLjItMS4yLS43LTEuN3oiLz48L2c+PC9zdmc+">';
														}else {
															$to_disp .= '<div style="width: 36px; height: 36px; border-radius: 50%; border-width: 1px; border-style: solid; border-color: #ecf0f1; margin-top: 10px; background-color: #ffffff;">
																		<div style="width: 12px; height: 12px; transform: translate(-50%,-50%); background-color: #ddd; border-radius: 100%; margin-top: 17px; margin-left: 17px;"></div>
																	</div>';
														}
														
														$to_disp .= '</div>
																	<div style= "display: flex; flex-direction: column; width: 250px;">
																		<p style= "font-weight: bold; margin: 0;">'.$event_status.'</p>
																		<p style= "margin: 0; color: #4a5568;">'.$event_loc.'</p>
																	</div>
																</div>
															</li>';
        									}
        									$to_disp .= '</ul></div>';
        								}else {
        									$to_disp = '<h4 style= "text-align: center;">Sorry! No data found for this package...<h4/></div>';
        									echo $to_disp;
        									return;
        								}
									}else {
										$to_disp = '<h4 style= "text-align: center;>Sorry! No data found for this package...<h4/></div>';
										echo $to_disp;
										return;
									}
							}
							$trk_count ++;
						}

						$to_disp = '</div>';
						echo $to_disp;
					}
				}
			}
			public function hits_dbs_save_user_fields($user_id){
				if(isset($_POST['hits_dbs_country'])){
					$general_settings['hits_dbs_site_id'] = sanitize_text_field(isset($_POST['hits_dbs_site_id']) ? $_POST['hits_dbs_site_id'] : '');
					$general_settings['hits_dbs_shipper_name'] = sanitize_text_field(isset($_POST['hits_dbs_shipper_name']) ? $_POST['hits_dbs_shipper_name'] : '');
					$general_settings['hits_dbs_company'] = sanitize_text_field(isset($_POST['hits_dbs_company']) ? $_POST['hits_dbs_company'] : '');
					$general_settings['hits_dbs_mob_num'] = sanitize_text_field(isset($_POST['hits_dbs_mob_num']) ? $_POST['hits_dbs_mob_num'] : '');
					$general_settings['hits_dbs_email'] = sanitize_text_field(isset($_POST['hits_dbs_email']) ? $_POST['hits_dbs_email'] : '');
					$general_settings['hits_dbs_address1'] = sanitize_text_field(isset($_POST['hits_dbs_address1']) ? $_POST['hits_dbs_address1'] : '');
					$general_settings['hits_dbs_address2'] = sanitize_text_field(isset($_POST['hits_dbs_address2']) ? $_POST['hits_dbs_address2'] : '');
					$general_settings['hits_dbs_city'] = sanitize_text_field(isset($_POST['hits_dbs_city']) ? $_POST['hits_dbs_city'] : '');
					$general_settings['hits_dbs_state'] = sanitize_text_field(isset($_POST['hits_dbs_state']) ? $_POST['hits_dbs_state'] : '');
					$general_settings['hits_dbs_zip'] = sanitize_text_field(isset($_POST['hits_dbs_zip']) ? $_POST['hits_dbs_zip'] : '');
					$general_settings['hits_dbs_country'] = sanitize_text_field(isset($_POST['hits_dbs_country']) ? $_POST['hits_dbs_country'] : '');
					$general_settings['hits_dbs_gstin'] = sanitize_text_field(isset($_POST['hits_dbs_gstin']) ? $_POST['hits_dbs_gstin'] : '');
					$general_settings['hits_dbs_con_rate'] = sanitize_text_field(isset($_POST['hits_dbs_con_rate']) ? $_POST['hits_dbs_con_rate'] : '');
					$general_settings['hits_dbs_def_dom'] = sanitize_text_field(isset($_POST['hits_dbs_def_dom']) ? $_POST['hits_dbs_def_dom'] : '');

					$general_settings['hits_dbs_def_inter'] = sanitize_text_field(isset($_POST['hits_dbs_def_inter']) ? $_POST['hits_dbs_def_inter'] : '');

					update_post_meta($user_id,'hits_dbs_vendor_settings',$general_settings);
				}

			}

			public function hits_dbs_define_credentails( $user ){

				$main_settings = get_option('hits_dbs_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				$allow = false;
				
				if(!isset($main_settings['hits_dbs_v_roles'])){
					return;
				}else{
					foreach ($user->roles as $value) {
						if(in_array($value, $main_settings['hits_dbs_v_roles'])){
							$allow = true;
						}
					}
				}
				
				if(!$allow){
					return;
				}

				$general_settings = get_post_meta($user->ID,'hits_dbs_vendor_settings',true);
				$general_settings = empty($general_settings) ? array() : $general_settings;
				$countires =  array(
									'AF' => 'Afghanistan',
									'AL' => 'Albania',
									'DZ' => 'Algeria',
									'AS' => 'American Samoa',
									'AD' => 'Andorra',
									'AO' => 'Angola',
									'AI' => 'Anguilla',
									'AG' => 'Antigua and Barbuda',
									'AR' => 'Argentina',
									'AM' => 'Armenia',
									'AW' => 'Aruba',
									'AU' => 'Australia',
									'AT' => 'Austria',
									'AZ' => 'Azerbaijan',
									'BS' => 'Bahamas',
									'BH' => 'Bahrain',
									'BD' => 'Bangladesh',
									'BB' => 'Barbados',
									'BY' => 'Belarus',
									'BE' => 'Belgium',
									'BZ' => 'Belize',
									'BJ' => 'Benin',
									'BM' => 'Bermuda',
									'BT' => 'Bhutan',
									'BO' => 'Bolivia',
									'BA' => 'Bosnia and Herzegovina',
									'BW' => 'Botswana',
									'BR' => 'Brazil',
									'VG' => 'British Virgin Islands',
									'BN' => 'Brunei',
									'BG' => 'Bulgaria',
									'BF' => 'Burkina Faso',
									'BI' => 'Burundi',
									'KH' => 'Cambodia',
									'CM' => 'Cameroon',
									'CA' => 'Canada',
									'CV' => 'Cape Verde',
									'KY' => 'Cayman Islands',
									'CF' => 'Central African Republic',
									'TD' => 'Chad',
									'CL' => 'Chile',
									'CN' => 'China',
									'CO' => 'Colombia',
									'KM' => 'Comoros',
									'CK' => 'Cook Islands',
									'CR' => 'Costa Rica',
									'HR' => 'Croatia',
									'CU' => 'Cuba',
									'CY' => 'Cyprus',
									'CZ' => 'Czech Republic',
									'DK' => 'Denmark',
									'DJ' => 'Djibouti',
									'DM' => 'Dominica',
									'DO' => 'Dominican Republic',
									'TL' => 'East Timor',
									'EC' => 'Ecuador',
									'EG' => 'Egypt',
									'SV' => 'El Salvador',
									'GQ' => 'Equatorial Guinea',
									'ER' => 'Eritrea',
									'EE' => 'Estonia',
									'ET' => 'Ethiopia',
									'FK' => 'Falkland Islands',
									'FO' => 'Faroe Islands',
									'FJ' => 'Fiji',
									'FI' => 'Finland',
									'FR' => 'France',
									'GF' => 'French Guiana',
									'PF' => 'French Polynesia',
									'GA' => 'Gabon',
									'GM' => 'Gambia',
									'GE' => 'Georgia',
									'DE' => 'Germany',
									'GH' => 'Ghana',
									'GI' => 'Gibraltar',
									'GR' => 'Greece',
									'GL' => 'Greenland',
									'GD' => 'Grenada',
									'GP' => 'Guadeloupe',
									'GU' => 'Guam',
									'GT' => 'Guatemala',
									'GG' => 'Guernsey',
									'GN' => 'Guinea',
									'GW' => 'Guinea-Bissau',
									'GY' => 'Guyana',
									'HT' => 'Haiti',
									'HN' => 'Honduras',
									'HK' => 'Hong Kong',
									'HU' => 'Hungary',
									'IS' => 'Iceland',
									'IN' => 'India',
									'ID' => 'Indonesia',
									'IR' => 'Iran',
									'IQ' => 'Iraq',
									'IE' => 'Ireland',
									'IL' => 'Israel',
									'IT' => 'Italy',
									'CI' => 'Ivory Coast',
									'JM' => 'Jamaica',
									'JP' => 'Japan',
									'JE' => 'Jersey',
									'JO' => 'Jordan',
									'KZ' => 'Kazakhstan',
									'KE' => 'Kenya',
									'KI' => 'Kiribati',
									'KW' => 'Kuwait',
									'KG' => 'Kyrgyzstan',
									'LA' => 'Laos',
									'LV' => 'Latvia',
									'LB' => 'Lebanon',
									'LS' => 'Lesotho',
									'LR' => 'Liberia',
									'LY' => 'Libya',
									'LI' => 'Liechtenstein',
									'LT' => 'Lithuania',
									'LU' => 'Luxembourg',
									'MO' => 'Macao',
									'MK' => 'Macedonia',
									'MG' => 'Madagascar',
									'MW' => 'Malawi',
									'MY' => 'Malaysia',
									'MV' => 'Maldives',
									'ML' => 'Mali',
									'MT' => 'Malta',
									'MH' => 'Marshall Islands',
									'MQ' => 'Martinique',
									'MR' => 'Mauritania',
									'MU' => 'Mauritius',
									'YT' => 'Mayotte',
									'MX' => 'Mexico',
									'FM' => 'Micronesia',
									'MD' => 'Moldova',
									'MC' => 'Monaco',
									'MN' => 'Mongolia',
									'ME' => 'Montenegro',
									'MS' => 'Montserrat',
									'MA' => 'Morocco',
									'MZ' => 'Mozambique',
									'MM' => 'Myanmar',
									'NA' => 'Namibia',
									'NR' => 'Nauru',
									'NP' => 'Nepal',
									'NL' => 'Netherlands',
									'NC' => 'New Caledonia',
									'NZ' => 'New Zealand',
									'NI' => 'Nicaragua',
									'NE' => 'Niger',
									'NG' => 'Nigeria',
									'NU' => 'Niue',
									'KP' => 'North Korea',
									'MP' => 'Northern Mariana Islands',
									'NO' => 'Norway',
									'OM' => 'Oman',
									'PK' => 'Pakistan',
									'PW' => 'Palau',
									'PA' => 'Panama',
									'PG' => 'Papua New Guinea',
									'PY' => 'Paraguay',
									'PE' => 'Peru',
									'PH' => 'Philippines',
									'PL' => 'Poland',
									'PT' => 'Portugal',
									'PR' => 'Puerto Rico',
									'QA' => 'Qatar',
									'CG' => 'Republic of the Congo',
									'RE' => 'Reunion',
									'RO' => 'Romania',
									'RU' => 'Russia',
									'RW' => 'Rwanda',
									'SH' => 'Saint Helena',
									'KN' => 'Saint Kitts and Nevis',
									'LC' => 'Saint Lucia',
									'VC' => 'Saint Vincent and the Grenadines',
									'WS' => 'Samoa',
									'SM' => 'San Marino',
									'ST' => 'Sao Tome and Principe',
									'SA' => 'Saudi Arabia',
									'SN' => 'Senegal',
									'RS' => 'Serbia',
									'SC' => 'Seychelles',
									'SL' => 'Sierra Leone',
									'SG' => 'Singapore',
									'SK' => 'Slovakia',
									'SI' => 'Slovenia',
									'SB' => 'Solomon Islands',
									'SO' => 'Somalia',
									'ZA' => 'South Africa',
									'KR' => 'South Korea',
									'SS' => 'South Sudan',
									'ES' => 'Spain',
									'LK' => 'Sri Lanka',
									'SD' => 'Sudan',
									'SR' => 'Suriname',
									'SZ' => 'Swaziland',
									'SE' => 'Sweden',
									'CH' => 'Switzerland',
									'SY' => 'Syria',
									'TW' => 'Taiwan',
									'TJ' => 'Tajikistan',
									'TZ' => 'Tanzania',
									'TH' => 'Thailand',
									'TG' => 'Togo',
									'TO' => 'Tonga',
									'TT' => 'Trinidad and Tobago',
									'TN' => 'Tunisia',
									'TR' => 'Turkey',
									'TC' => 'Turks and Caicos Islands',
									'TV' => 'Tuvalu',
									'VI' => 'U.S. Virgin Islands',
									'UG' => 'Uganda',
									'UA' => 'Ukraine',
									'AE' => 'United Arab Emirates',
									'GB' => 'United Kingdom',
									'US' => 'United States',
									'UY' => 'Uruguay',
									'UZ' => 'Uzbekistan',
									'VU' => 'Vanuatu',
									'VE' => 'Venezuela',
									'VN' => 'Vietnam',
									'YE' => 'Yemen',
									'ZM' => 'Zambia',
									'ZW' => 'Zimbabwe',
								);
				 $_dbs_carriers = array(
					//"Public carrier name" => "technical name",
					'f'                    => 'Jet cargo first (Air)',
					's'                    => 'Jetcargo special (Air)',
					'b'                    => 'Jetcargo business (Air)',
					'e'                    => 'Jetcargo economy (Air)',
					'eagd'                    => 'Jetexpress gold (Air)',
					'easv'                    => 'Jetexpress silver (Air)',
					'fcl'                    => 'Complete -FCL (Ocean)',
					'lcl'                    => 'Combine -LCL (Ocean)',
					'CON'                    => 'Concepts (Land)',
					'DIR'                    => 'Directs (Land)',
					'LPA'                    => 'Logistics Parcel (Land)',
					'PAL'                    => 'Pallets (Land)',
					'PRI'                    => 'Privpark (Land)',
					'auc0'                    => 'System premium10 (Land)',
					'auc2'                    => 'System premium13 (Land)',
					'auc8'                    => 'System premium 8 (Land)',
					'aucc'                    => 'System premium (Land)',
					'auco'                    => 'System (Land)',
					'ecsp'                    => 'System-plus (Land)',
					'ect1'                    => 'Speed 10 (Land)',
					'ect2'                    => 'Speed 12 (Land)',
					'sch2'                    => 'Top 12 (Land)',
					'schs'                    => 'System international (Land)',
					'sysd'                    => 'System domestic (Land)',
					'scht'                    => 'Top (Land)',
					'schx'                    => 'System fix (Land)',
					'ecpa'                    => 'Parcel (Land)',
					'ect8'                    => 'Speed 8 (Land)',
					'ectn'                    => 'Speed (Land)',
					'40'                    => 'System classic (Land)',
					'41'                    => 'System speed (Land)',
					'42'                    => 'System fixday (Land)',
					'43'                    => 'System (Land)',
					'44'                    => 'System Premium (Land)',
					'71'                    => 'Full load (Land)',
					'72'                    => 'Part load (Land)',
				);

			$dbs_core = array();
			$dbs_core['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
			$dbs_core['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
			$dbs_core['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
			$dbs_core['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
			$dbs_core['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
			$dbs_core['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
			$dbs_core['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dbs_core['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
			$dbs_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
			$dbs_core['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
			$dbs_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
			$dbs_core['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
			$dbs_core['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
			$dbs_core['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
			$dbs_core['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
			$dbs_core['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
			$dbs_core['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
			$dbs_core['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
			$dbs_core['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
			$dbs_core['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
			$dbs_core['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
			$dbs_core['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
			$dbs_core['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
			$dbs_core['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
			$dbs_core['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
			$dbs_core['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
			$dbs_core['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
			$dbs_core['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dbs_core['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
			$dbs_core['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
			$dbs_core['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
			$dbs_core['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
			$dbs_core['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
			$dbs_core['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
			$dbs_core['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['CZ'] = array('region' => 'EU', 'currency' =>'CZK', 'weight' => 'KG_CM');
			$dbs_core['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
			$dbs_core['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dbs_core['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
			$dbs_core['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
			$dbs_core['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
			$dbs_core['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
			$dbs_core['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
			$dbs_core['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
			$dbs_core['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dbs_core['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
			$dbs_core['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['GH'] = array('region' => 'AP', 'currency' =>'GHS', 'weight' => 'KG_CM');
			$dbs_core['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
			$dbs_core['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
			$dbs_core['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
			$dbs_core['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
			$dbs_core['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
			$dbs_core['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
			$dbs_core['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
			$dbs_core['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
			$dbs_core['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
			$dbs_core['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
			$dbs_core['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
			$dbs_core['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
			$dbs_core['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
			$dbs_core['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
			$dbs_core['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
			$dbs_core['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
			$dbs_core['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
			$dbs_core['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
			$dbs_core['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
			$dbs_core['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
			$dbs_core['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
			$dbs_core['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
			$dbs_core['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
			$dbs_core['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dbs_core['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
			$dbs_core['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
			$dbs_core['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
			$dbs_core['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
			$dbs_core['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
			$dbs_core['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
			$dbs_core['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
			$dbs_core['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
			$dbs_core['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
			$dbs_core['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
			$dbs_core['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
			$dbs_core['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
			$dbs_core['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
			$dbs_core['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
			$dbs_core['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
			$dbs_core['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
			$dbs_core['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
			$dbs_core['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
			$dbs_core['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
			$dbs_core['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
			$dbs_core['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
			$dbs_core['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
			$dbs_core['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
			$dbs_core['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
			$dbs_core['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
			$dbs_core['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
			$dbs_core['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
			$dbs_core['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
			$dbs_core['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
			$dbs_core['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
			$dbs_core['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
			$dbs_core['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
			$dbs_core['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
			$dbs_core['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dbs_core['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dbs_core['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
			$dbs_core['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
			$dbs_core['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
			$dbs_core['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
			$dbs_core['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
			$dbs_core['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
			$dbs_core['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
			$dbs_core['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
			$dbs_core['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
			$dbs_core['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
			$dbs_core['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
			$dbs_core['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
			$dbs_core['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
			$dbs_core['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
			$dbs_core['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
			$dbs_core['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
			$dbs_core['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
			$dbs_core['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
			$dbs_core['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
			$dbs_core['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
			$dbs_core['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
			$dbs_core['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
			$dbs_core['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
			$dbs_core['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
			$dbs_core['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
			$dbs_core['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
			$dbs_core['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
			$dbs_core['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
			$dbs_core['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
			$dbs_core['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
			$dbs_core['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
			$dbs_core['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
			$dbs_core['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
			$dbs_core['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
			$dbs_core['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
			$dbs_core['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
			$dbs_core['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
			$dbs_core['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
			$dbs_core['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
			$dbs_core['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
			$dbs_core['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
			$dbs_core['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
			$dbs_core['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
			$dbs_core['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
			$dbs_core['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
			$dbs_core['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
			$dbs_core['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
			$dbs_core['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
			$dbs_core['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dbs_core['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dbs_core['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
			$dbs_core['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
			$dbs_core['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
			$dbs_core['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
			$dbs_core['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
			$dbs_core['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
			$dbs_core['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
			$dbs_core['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
			$dbs_core['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
			$dbs_core['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');

				 echo '<hr><h3 class="heading">DB Schenker - <a href="https://hitshipo.com/" target="_blank">HITShipo</a></h3>';
				    ?>
				    
				    <table class="form-table">
						<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DB Schenker Integration Team will give this details to you.','hitshipo_dbs') ?>"></span>	<?php _e('DB Schenker API Access Key','hitshipo_dbs') ?></h4>
							<p> <?php _e('Leave this field as empty to use default account.','hitshipo_dbs') ?> </p>
						</td>
						<td>
							<input type="text" name="hits_dbs_site_id" value="<?php echo (isset($general_settings['hits_dbs_site_id'])) ? $general_settings['hits_dbs_site_id'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipping Person Name','hitshipo_dbs') ?>"></span>	<?php _e('Shipper Name','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_shipper_name" value="<?php echo (isset($general_settings['hits_dbs_shipper_name'])) ? $general_settings['hits_dbs_shipper_name'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Company Name.','hitshipo_dbs') ?>"></span>	<?php _e('Company Name','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_company" value="<?php echo (isset($general_settings['hits_dbs_company'])) ? $general_settings['hits_dbs_company'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Mobile / Contact Number.','hitshipo_dbs') ?>"></span>	<?php _e('Contact Number','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_mob_num" value="<?php echo (isset($general_settings['hits_dbs_mob_num'])) ? $general_settings['hits_dbs_mob_num'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Email Address of the Shipper.','hitshipo_dbs') ?>"></span>	<?php _e('Email Address','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_email" value="<?php echo (isset($general_settings['hits_dbs_email'])) ? $general_settings['hits_dbs_email'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 1 of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Address Line 1','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_address1" value="<?php echo (isset($general_settings['hits_dbs_address1'])) ? $general_settings['hits_dbs_address1'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 2 of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Address Line 2','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_address2" value="<?php echo (isset($general_settings['hits_dbs_address2'])) ? $general_settings['hits_dbs_address2'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%;padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the Shipper from address.','hitshipo_dbs') ?>"></span>	<?php _e('City','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_city" value="<?php echo (isset($general_settings['hits_dbs_city'])) ? $general_settings['hits_dbs_city'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the Shipper from address.','hitshipo_dbs') ?>"></span>	<?php _e('State (Two Digit String)','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_state" value="<?php echo (isset($general_settings['hits_dbs_state'])) ? $general_settings['hits_dbs_state'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal/Zip Code.','hitshipo_dbs') ?>"></span>	<?php _e('Postal/Zip Code','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_zip" value="<?php echo (isset($general_settings['hits_dbs_zip'])) ? $general_settings['hits_dbs_zip'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Country','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<select name="hits_dbs_country" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($countires as $key => $value)
								{

									if(isset($general_settings['hits_dbs_country']) && ($general_settings['hits_dbs_country'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value." [". $dbs_core[$key]['currency'] ."]</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value." [". $dbs_core[$key]['currency'] ."]</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('GSTIN/VAT No.','hitshipo_dbs') ?>"></span>	<?php _e('GSTIN/VAT No','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_gstin" value="<?php echo (isset($general_settings['hits_dbs_gstin'])) ? $general_settings['hits_dbs_gstin'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Conversion Rate from Site Currency to DB Schenker Currency.','hitshipo_dbs') ?>"></span>	<?php _e('Conversion Rate from Site Currency to DB Schenker Currency ( Ignore if auto conversion is Enabled )','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_con_rate" value="<?php echo (isset($general_settings['hits_dbs_con_rate'])) ? $general_settings['hits_dbs_con_rate'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default Domestic Express Shipping.','hitshipo_dbs') ?>"></span>	<?php _e('Default Domestic Service','hitshipo_dbs') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','hitshipo_dbs') ?></p>
						</td>
						<td>
							<select name="hits_dbs_def_dom" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dbs_carriers as $key => $value)
								{
									if(isset($general_settings['hits_dbs_def_dom']) && ($general_settings['hits_dbs_def_dom'] == $key))
									{
										echo "<option value=".$key." selected='true'>[".$key."] ".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">[".$key."] ".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; padding: 5px; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Default International Shipping.','hitshipo_dbs') ?>"></span>	<?php _e('Default International Service','hitshipo_dbs') ?></h4>
							<p><?php _e('This will be used while shipping label generation.','hitshipo_dbs') ?></p>
						</td>
						<td>
							<select name="hits_dbs_def_inter" class="wc-enhanced-select" style="width:210px;">
								<?php foreach($_dbs_carriers as $key => $value)
								{
									if(isset($general_settings['hits_dbs_def_inter']) && ($general_settings['hits_dbs_def_inter'] == $key))
									{
										echo "<option value=".$key." selected='true'>[".$key."] ".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">[".$key."] ".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				    </table>
				    <hr>
				    <?php
			}
			public function hits_dbs_save_product_meta( $post_id ){
				if(isset( $_POST['hits_dbs_shipment'])){
					$hits_dbs_shipment = sanitize_text_field($_POST['hits_dbs_shipment']);
					if( !empty( $hits_dbs_shipment ) )
					update_post_meta( $post_id, 'hits_dbs_address', (string) esc_html( $hits_dbs_shipment ) );	
				}
							
			}
			public function hits_dbs_choose_vendor_address(){
				global $woocommerce, $post;
				$hit_multi_vendor = get_option('hit_multi_vendor');
				$hit_multi_vendor = empty($hit_multi_vendor) ? array() : $hit_multi_vendor;

				if ($this->hpos_enabled) {
					$hpos_prod_data = wc_get_product($post->ID);
					$selected_addr = $hpos_prod_data->get_meta("hits_dbs_address");
				} else {
					$selected_addr = get_post_meta( $post->ID, 'hits_dbs_address', true);
				}

				$main_settings = get_option('hits_dbs_main_settings');
				$main_settings = empty($main_settings) ? array() : $main_settings;
				if(!isset($main_settings['hits_dbs_v_roles']) || empty($main_settings['hits_dbs_v_roles'])){
					return;
				}
				$v_users = get_users( [ 'role__in' => $main_settings['hits_dbs_v_roles'] ] );
				
				?>
				<div class="options_group">
				<p class="form-field hits_dbs_shipment">
					<label for="hits_dbs_shipment"><?php _e( 'DB Schenker Account', 'woocommerce' ); ?></label>
					<select id="hits_dbs_shipment" style="width:240px;" name="hits_dbs_shipment" class="wc-enhanced-select" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce' ); ?>">
						<option value="default" >Default Account</option>
						<?php
							if ( $v_users ) {
								foreach ( $v_users as $value ) {
									echo '<option value="' .  $value->data->ID  . '" '.($selected_addr == $value->data->ID ? 'selected="true"' : '').'>' . $value->data->display_name . '</option>';
								}
							}
						?>
					</select>
					</p>
				</div>
				<?php
			}

			public function hits_dbs_init()
			{
				include_once("controllors/hitshipo_dbs_init.php");
			}
			public function hits_dbs_order_status_update(){
				global $woocommerce;
				if(isset($_GET['h1t_updat3_0rd3r']) && isset($_GET['key']) && isset($_GET['action'])){
					$order_id = sanitize_text_field($_GET['h1t_updat3_0rd3r']);
					$key = sanitize_text_field($_GET['key']);
					$action = sanitize_text_field($_GET['action']);
					$order_ids = explode(",",$order_id);
					$general_settings = get_option('hits_dbs_main_settings',array());
					
					if(isset($general_settings['hits_dbs_integration_key']) && $general_settings['hits_dbs_integration_key'] == $key){
						if($action == 'processing'){
							foreach ($order_ids as $order_id) {
								$order = wc_get_order( $order_id );
								$order->update_status( 'processing' );
							}
						}else if($action == 'completed'){
							foreach ($order_ids as $order_id) {
								  $order = wc_get_order( $order_id );
								  $order->update_status( 'completed' );
								  	
							}
						}
					}
					die();
				}

				if(isset($_GET['h1t_updat3_sh1pp1ng']) && isset($_GET['key']) && isset($_GET['user_id']) && isset($_GET['carrier']) && isset($_GET['track']) && isset($_GET['carrier_type']) && $_GET['carrier'] == "dbs"){
					$order_id = sanitize_text_field($_GET['h1t_updat3_sh1pp1ng']);
					$key = sanitize_text_field($_GET['key']);
					$general_settings = get_option('hits_dbs_main_settings',array());
					$user_id = sanitize_text_field($_GET['user_id']);
					$carrier = sanitize_text_field($_GET['carrier']);
					$track = sanitize_text_field($_GET['track']);
					$carrier_type = sanitize_text_field($_GET['carrier_type']);
					$output['status'] = 'success';
					$output['tracking_num'] = $track;
					if (!empty($carrier_type) && ($carrier_type != "fcl") || ($carrier_type != "lcl")) {
						$output['label'] = "https://app.hitshipo.com/api/shipping_labels/".$user_id."/".$carrier."/order_".$order_id."_track_".$track."_label.pdf";
					}else {
						$output['label'] = "";
					}
					$output['invoice'] = "";
					$result_arr = array();
					if(isset($general_settings['hits_dbs_integration_key']) && $general_settings['hits_dbs_integration_key'] == $key){
						
						if(isset($_GET['label'])){
							$output['user_id'] = sanitize_text_field($_GET['label']);
							if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes'){
								$val = get_option('hits_dbs_values_'.$order_id, []);
								$result_arr = array();
								if(!empty($val)){
									$result_arr = json_decode($val, true);
								}	
							}
							
							$result_arr[] = $output;

						}else{
							$result_arr[] = $output;
						}

						update_option('hits_dbs_values_'.$order_id, json_encode($result_arr));
						
					}

					die();
				}
			}
			public function hits_dbs_method( $methods )
			{
				if (is_admin() && !is_ajax() || apply_filters('hits_dbs_shipping_method_enabled', true)) {
					$methods['hitshipo_dbs'] = 'hitshipo_dbs'; 
				}

				return $methods;
			}
			
			public function hits_dbs_plugin_action_links($links)
			{
				$setting_value = version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
				$plugin_links = array(
					'<a href="' . admin_url( 'admin.php?page=' . $setting_value  . '&tab=shipping&section=hits_dbs' ) . '" style="color:green;">' . __( 'Configure', 'hitshipo_dbs' ) . '</a>',
					'<a href="#" target="_blank" >' . __('Support', 'hitshipo_dbs') . '</a>'
					);
				return array_merge( $plugin_links, $links );
			}
			public function hits_create_dbs_shipping_meta_box() {
				$meta_scrn = $this->hpos_enabled ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order';
	       		add_meta_box( 'hits_dbs_shipping', __('DBS Schenker Label','hitshipo_dbs'), array($this, 'create_dbs_shipping_label_genetation'), $meta_scrn, 'side', 'core' );
		    }
		    public function create_dbs_shipping_label_genetation($post){
		    	// print_r('expression');
		    	// die();	
				if(!$this->hpos_enabled && $post->post_type !='shop_order' ){
		    		return;
		    	}	    	
				$order = (!$this->hpos_enabled) ? wc_get_order( $post->ID ) : $post;
		    	$order_id = $order->get_id();
		        $_dbs_carriers = array(
								//"Public carrier name" => "technical name",
								'f'                    => 'Jet cargo first (Air)',
								's'                    => 'Jetcargo special (Air)',
								'b'                    => 'Jetcargo business (Air)',
								'e'                    => 'Jetcargo economy (Air)',
								'eagd'                    => 'Jetexpress gold (Air)',
								'easv'                    => 'Jetexpress silver (Air)',
								'fcl'                    => 'Complete -FCL (Ocean)',
								'lcl'                    => 'Combine -LCL (Ocean)',
								'CON'                    => 'Concepts (Land)',
								'DIR'                    => 'Directs (Land)',
								'LPA'                    => 'Logistics Parcel (Land)',
								'PAL'                    => 'Pallets (Land)',
								'PRI'                    => 'Privpark (Land)',
								'auc0'                    => 'System premium 10 (Land)',
								'auc2'                    => 'System premium 13 (Land)',
								'auc8'                    => 'System premium 8 (Land)',
								'aucc'                    => 'System premium (Land)',
								'auco'                    => 'System (Land)',
								'ecsp'                    => 'System-plus (Land)',
								'ect1'                    => 'Speed 10 (Land)',
								'ect2'                    => 'Speed 12 (Land)',
								'sch2'                    => 'Top 12 (Land)',
								'schs'                    => 'System international (Land)',
								'sysd'                    => 'System domestic (Land)',
								'scht'                    => 'Top (Land)',
								'schx'                    => 'System fix (Land)',
								'ecpa'                    => 'Parcel (Land)',
								'ect8'                    => 'Speed 8 (Land)',
								'ectn'                    => 'Speed (Land)',
								'40'                    => 'System classic (Land)',
								'41'                    => 'System speed (Land)',
								'42'                    => 'System fixday (Land)',
								'43'                    => 'System (Land)',
								'44'                    => 'System Premium (Land)',
								'71'                    => 'Full load (Land)',
								'72'                    => 'Part load (Land)',
							);

		        $general_settings = get_option('hits_dbs_main_settings',array());
		       	
		        $items = $order->get_items();

    		    $custom_settings = array();
		    	$custom_settings['default'] =  array();
		    	$vendor_settings = array();

		    	$pack_products = array();
				
				foreach ( $items as $item ) {
					$product_data = $item->get_data();
				    $product = array();
				    $product['product_name'] = $product_data['name'];
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];
				    
				    $pack_products[] = $product;
				    
				}

				if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' && isset($general_settings['hits_dbs_v_labels']) && $general_settings['hits_dbs_v_labels'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						
						if ($this->hpos_enabled) {
							$hpos_prod_data = wc_get_product($product_id);
							$dbs_account = $hpos_prod_data->get_meta("hits_dbs_address");
						} else {
							$dbs_account = get_post_meta($product_id,'hits_dbs_address', true);
						}
						if(empty($dbs_account) || $dbs_account == 'default'){
							$dbs_account = 'default';
							if (!isset($vendor_settings[$dbs_account])) {
								$vendor_settings[$dbs_account] = $custom_settings['default'];
							}
							
							$vendor_settings[$dbs_account]['products'][] = $value;
						}

						if($dbs_account != 'default'){
							$user_account = get_post_meta($dbs_account,'hits_dbs_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dbs_account])){

									$vendor_settings[$dbs_account] = $custom_settings['default'];
									unset($value['product_id']);
									$vendor_settings[$dbs_account]['products'][] = $value;
								}
							}else{
								$dbs_account = 'default';
								$vendor_settings[$dbs_account] = $custom_settings['default'];
								$vendor_settings[$dbs_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}

		       	$json_data = get_option('hits_dbs_values_'.$order_id);
				$notice = get_option('hits_dbs_status_'.$order_id, null);
				if($notice && $notice != 'success'){
		        	echo "<p style='color:red'>".$notice."</p>";
		        	delete_option('hits_dbs_status_'.$order_id);
		        }
		        if(!empty($json_data)){
					// echo '<pre>';print_r($json_data);die();
   					$array_data = json_decode( $json_data, true );
   					// echo '<pre>';print_r($array_data);
		       		if(isset($array_data[0])){
		       			foreach ($array_data as $key => $value) {
		       				if(isset($value['user_id'])){
		       					unset($custom_settings[$value['user_id']]);
		       				}
		       				if(isset($value['user_id']) && $value['user_id'] == 'default'){
		       					echo '<br/><b>Default Account</b><br/>';
		       				}else{
		       					$user = get_user_by( 'id', $value['user_id'] );
		       					echo '<br/><b>Account:</b> <small>'.$user->display_name.'</small><br/>';
		       				}
		       				echo '<b>Booking ID:</b> <small>'.(isset($value['tracking_num']) ? $value['tracking_num'] : "").'</small><br/>';
		       				if (isset($value['label']) && !empty($value['label'])) {
		       					echo '<a href="'.$value['label'].'" target="_blank" style="background:#da0322; color: #000002;border-color: #da0322;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;margin-top:3px;" class="button button-primary"> Shipping Label</a>';
		       				}

		       				if (isset($value['invoice']) && !empty($value['invoice'])) {
		       					echo ' <a href="'.$value['invoice'].'" target="_blank" class="button button-primary" style="margin-top:3px;"> Invoice </a><br/>';
		       				}else {
		       					echo '<br/>';
		       				}
			       			
		       			}
		       		}else{
		       			$custom_settings = array();
		       			echo '<br/><b>Booking ID:</b> <small>'.isset($array_data['tracking_num']) ? $array_data['tracking_num'] : "".'</small><br/>';
		       			if (isset($array_data['label']) && !empty($array_data['label'])) {
		       				echo '<a href="'.$array_data['label'].'" target="_blank" style="background:#da0322; color: #000002;border-color: #da0322;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;" class="button button-primary"> Shipping Label</a> ';
		       			}
		       			if (isset($array_data['invoice']) && !empty($array_data['invoice'])) {
		       				echo ' <a href="'.$array_data['invoice'].'" target="_blank" class="button button-primary"> Invoice </a>';
		       			}
		       		}
   				}
				
	       		foreach ($custom_settings as $ukey => $value) {
	       			if($ukey == 'default'){
	       				echo '<br/><b>Default Account</b>';
				        echo '<br/><select name="hits_dbs_service_code_default">';
				        if(!empty($general_settings['hits_dbs_carrier'])){
				        	foreach ($general_settings['hits_dbs_carrier'] as $key => $value) {
				        		echo "<option value='".$key."'>".$key .' - ' .$_dbs_carriers[$key]."</option>";
				        	}
				        }
				        echo '</select>';
				        echo '<br/><b>Shipment Content</b>';
		        
				        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hits_dbs_shipment_content_default" placeholder="Shipment content" value="' . (($general_settings['hits_dbs_ship_content']) ? $general_settings['hits_dbs_ship_content'] : "") . '" >';
				        
				        echo '<button name="hits_dbs_create_label" value="default" style="background:#da0322; color: #000002;border-color: #da0322;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;" class="button button-primary">Create Shipment</button>';
				        
	       			}else{
					
	       				$user = get_user_by( 'id', $ukey );
		       			echo '<br/><b>Account:</b> <small>'.$user->display_name.'</small>';
				        echo '<br/><select name="hits_dbs_service_code_'.$ukey.'">';
				        if(!empty($general_settings['hits_dbs_carrier'])){
				        	foreach ($general_settings['hits_dbs_carrier'] as $key => $value) {
				        		echo "<option value='".$key."'>".$key .' - ' .$_dbs_carriers[$key]."</option>";
				        	}
				        }
						
				        echo '</select>';
				        echo '<br/><b>Shipment Content</b>';
		        
				        echo '<br/><input type="text" style="width:250px;margin-bottom:10px;"  name="hits_dbs_shipment_content_'.$ukey.'" placeholder="Shipment content" value="' . (($general_settings['hits_dbs_ship_content']) ? $general_settings['hits_dbs_ship_content'] : "") . '" >';
				       
				        echo '<button name="hits_dbs_create_label" value="'.$ukey.'" style="background:#da0322; color: #000002;border-color: #da0322;box-shadow: 0px 1px 0px #FFCC00;text-shadow: 0px 1px 0px #D40511;" class="button button-primary">Create Shipment</button><br/>';
				        
	       			}
	       			
	       		}

		       	if(!empty($json_data)){
		       		
		       		echo '<br/><button name="hits_dbs_reset" class="button button-secondary" style="margin-top:3px;"> Reset Shipments</button>';
		       	}

		    }

		    public function hits_dbs_wc_checkout_order_processed($order_id){
				// echo "fsdfsdfds";
		    	// die();

				if ($this->hpos_enabled) {
					if ('shop_order' !== Automattic\WooCommerce\Utilities\OrderUtil::get_order_type($order_id)) {
						return;
					}
				} else {
					$post = get_post($order_id);
	
					if($post->post_type !='shop_order' ){
						return;
					}
				}

		    	$ship_content = !empty($_POST['hits_dbs_shipment_content']) ? sanitize_text_field($_POST['hits_dbs_shipment_content']) : 'Shipment Content';
		        $order = wc_get_order( $order_id );

		        $service_code = $multi_ven = '';
		        foreach( $order->get_shipping_methods() as $item_id => $item ){
					$service_code = $item->get_meta('hits_dbs_service');
					$multi_ven = $item->get_meta('hits_dbs_multi_ven');

				}
				// if(empty($service_code)){
				// 	return;
				// }
				$general_settings = get_option('hits_dbs_main_settings',array());
		    	$order_data = $order->get_data();
		    	$items = $order->get_items();

		    	$desination_country = (isset($order_data['shipping']['country']) && $order_data['shipping']['country'] != '') ? $order_data['shipping']['country'] : $order_data['billing']['country'];
						if(isset($general_settings['hits_dbs_country']) && $general_settings["hits_dbs_country"] == $desination_country){
							$service_code = $general_settings['hits_dbs_bulk_service_dom'];
						}else{
							$service_code = $general_settings['hits_dbs_bulk_service_intl'];
						}	
						
		    	if(!isset($general_settings['hits_dbs_label_automation']) || $general_settings['hits_dbs_label_automation'] != 'yes'){
		    		return;
		    	}

		    	$custom_settings = array();
				$custom_settings['default'] = array(
									'hits_dbs_site_id' => $general_settings['hits_dbs_site_id'],
									'hits_dbs_shipper_name' => $general_settings['hits_dbs_shipper_name'],
									'hits_dbs_company' => $general_settings['hits_dbs_company'],
									'hits_dbs_mob_num' => $general_settings['hits_dbs_mob_num'],
									'hits_dbs_email' => $general_settings['hits_dbs_email'],
									'hits_dbs_address1' => $general_settings['hits_dbs_address1'],
									'hits_dbs_address2' => $general_settings['hits_dbs_address2'],
									'hits_dbs_city' => $general_settings['hits_dbs_city'],
									'hits_dbs_state' => $general_settings['hits_dbs_state'],
									'hits_dbs_zip' => $general_settings['hits_dbs_zip'],
									'hits_dbs_country' => $general_settings['hits_dbs_country'],
									'hits_dbs_gstin' => $general_settings['hits_dbs_gstin'],
									'hits_dbs_con_rate' => $general_settings['hits_dbs_con_rate'],
									'service_code' => $service_code,
									'hits_dbs_label_email' => $general_settings['hits_dbs_label_email'],
								);
				$vendor_settings = array();



				if(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM')
				{
					$hits_dbs_mod_weight_unit = 'kg';
					$hits_dbs_mod_dim_unit = 'cm';
				}elseif(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'LB_IN')
				{
					$hits_dbs_mod_weight_unit = 'lbs';
					$hits_dbs_mod_dim_unit = 'in';
				}
				else
				{
					$hits_dbs_mod_weight_unit = 'kg';
					$hits_dbs_mod_dim_unit = 'cm';
				}
			    

				$pack_products = array();
				
				foreach ( $items as $item ) {
					$product_data = $item->get_data();

				    $product = array();
				    $product['product_name'] = str_replace('"', '', $product_data['name']);
				    $product['product_quantity'] = $product_data['quantity'];
				    $product['product_id'] = $product_data['product_id'];
				    
				    $product_variation_id = $item->get_variation_id();
				    if(empty($product_variation_id) || $product_variation_id == 0){
				    	$getproduct = wc_get_product( $product_data['product_id'] );
				    }else{
				    	$getproduct = wc_get_product( $product_variation_id );
				    }
				    $woo_weight_unit = get_option('woocommerce_weight_unit');
					$woo_dimension_unit = get_option('woocommerce_dimension_unit');

					$hits_dbs_mod_weight_unit = $hits_dbs_mod_dim_unit = '';

				    $product['price'] = $getproduct->get_price();

				    if(!$product['price']){
						$product['price'] = (isset($product_data['total']) && isset($product_data['quantity'])) ? number_format(($product_data['total'] / $product_data['quantity']), 2) : 0;
					}

				    if ($woo_dimension_unit != $hits_dbs_mod_dim_unit) {
				    	$prod_width = $getproduct->get_width();
				    	$prod_height = $getproduct->get_height();
				    	$prod_depth = $getproduct->get_length();

				    	//wc_get_dimension( $dimension, $to_unit, $from_unit );
				    	$product['width'] = (!empty($prod_width) && $prod_width > 0) ? round(wc_get_dimension( $prod_width, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;
				    	$product['height'] = (!empty($prod_height) && $prod_height > 0) ? round(wc_get_dimension( $prod_height, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;
						$product['depth'] = (!empty($prod_depth) && $prod_depth > 0) ? round(wc_get_dimension( $prod_depth, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;

				    }else {
				    	$product['width'] = (!empty($getproduct->get_width()) && $getproduct->get_width() > 0) ? round($getproduct->get_width(),2) : 0.5;
				    	$product['height'] = (!empty($getproduct->get_height()) && $getproduct->get_height() > 0) ? round($getproduct->get_height(),2) : 0.5;
				    	$product['depth'] = (!empty($getproduct->get_length()) && $getproduct->get_length() > 0) ? round($getproduct->get_length(),2) : 0.5;
				    }
				    
				    if ($woo_weight_unit != $hits_dbs_mod_weight_unit) {
				    	$prod_weight = $getproduct->get_weight();
				    	$product['weight'] = (!empty($prod_weight) && $prod_weight > 0) ? round(wc_get_weight( $prod_weight, $hits_dbs_mod_weight_unit, $woo_weight_unit ), 2) : 0.1 ;
				    }else{
				    	$product['weight'] = (!empty($getproduct->get_weight()) && $getproduct->get_weight() > 0) ? round($getproduct->get_weight(),2) : 0.1;
					}
				    $pack_products[] = $product;
				    
				}

				if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' && isset($general_settings['hits_dbs_v_labels']) && $general_settings['hits_dbs_v_labels'] == 'yes'){
					// Multi Vendor Enabled
					foreach ($pack_products as $key => $value) {

						$product_id = $value['product_id'];
						if ($this->hpos_enabled) {
							$hpos_prod_data = wc_get_product($product_id);
							$dbs_account = $hpos_prod_data->get_meta("hits_dbs_address");
						} else {
							$dbs_account = get_post_meta($product_id,'hits_dbs_address', true);
						}
						if(empty($dbs_account) || $dbs_account == 'default'){
							$dbs_account = 'default';
							if (!isset($vendor_settings[$dbs_account])) {
								$vendor_settings[$dbs_account] = $custom_settings['default'];
							}
							
							$vendor_settings[$dbs_account]['products'][] = $value;
						}

						if($dbs_account != 'default'){
							$user_account = get_post_meta($dbs_account,'hits_dbs_vendor_settings', true);
							$user_account = empty($user_account) ? array() : $user_account;
							if(!empty($user_account)){
								if(!isset($vendor_settings[$dbs_account])){

									$vendor_settings[$dbs_account] = $custom_settings['default'];
									
									if($user_account['hits_dbs_site_id'] != ''){
										
										$vendor_settings[$dbs_account]['hits_dbs_site_id'] = $user_account['hits_dbs_site_id'];

									}

									if ($user_account['hits_dbs_address1'] != '' && $user_account['hits_dbs_city'] != '' && $user_account['hits_dbs_state'] != '' && $user_account['hits_dbs_zip'] != '' && $user_account['hits_dbs_country'] != '' && $user_account['hits_dbs_shipper_name'] != '') {
										
										if($user_account['hits_dbs_shipper_name'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_shipper_name'] = $user_account['hits_dbs_shipper_name'];
										}

										if($user_account['hits_dbs_company'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_company'] = $user_account['hits_dbs_company'];
										}

										if($user_account['hits_dbs_mob_num'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_mob_num'] = $user_account['hits_dbs_mob_num'];
										}

										if($user_account['hits_dbs_email'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_email'] = $user_account['hits_dbs_email'];
										}

										if ($user_account['hits_dbs_address1'] != '') {
											$vendor_settings[$dbs_account]['hits_dbs_address1'] = $user_account['hits_dbs_address1'];
										}

										$vendor_settings[$dbs_account]['hits_dbs_address2'] = $user_account['hits_dbs_address2'];
										
										if($user_account['hits_dbs_city'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_city'] = $user_account['hits_dbs_city'];
										}

										if($user_account['hits_dbs_state'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_state'] = $user_account['hits_dbs_state'];
										}

										if($user_account['hits_dbs_zip'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_zip'] = $user_account['hits_dbs_zip'];
										}

										if($user_account['hits_dbs_country'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_country'] = $user_account['hits_dbs_country'];
										}

										$vendor_settings[$dbs_account]['hits_dbs_gstin'] = $user_account['hits_dbs_gstin'];
										$vendor_settings[$dbs_account]['hits_dbs_con_rate'] = $user_account['hits_dbs_con_rate'];
									}

									if(isset($general_settings['hits_dbs_v_email']) && $general_settings['hits_dbs_v_email'] == 'yes'){
										$user_dat = get_userdata($dbs_account);
										$vendor_settings[$dbs_account]['hits_dbs_label_email'] = $user_dat->data->user_email;
									}
									
									if($multi_ven !=''){
										$array_ven = explode('|',$multi_ven);
										$scode = '';
										foreach ($array_ven as $key => $svalue) {
											$ex_service = explode("_", $svalue);
											if($ex_service[0] == $dbs_account){
												$vendor_settings[$dbs_account]['service_code'] = $ex_service[1];
											}
										}
										
										if($scode == ''){
											if($order_data['shipping']['country'] != $vendor_settings[$dbs_account]['hits_dbs_country']){
												$vendor_settings[$dbs_account]['service_code'] = $user_account['hits_dbs_def_inter'];
											}else{
												$vendor_settings[$dbs_account]['service_code'] = $user_account['hits_dbs_def_dom'];
											}
										}

									}else{
										if($order_data['shipping']['country'] != $vendor_settings[$dbs_account]['hits_dbs_country']){
											$vendor_settings[$dbs_account]['service_code'] = $user_account['hits_dbs_def_inter'];
										}else{
											$vendor_settings[$dbs_account]['service_code'] = $user_account['hits_dbs_def_dom'];
										}

									}
								}
								unset($value['product_id']);
								$vendor_settings[$dbs_account]['products'][] = $value;
							}
						}

					}

				}

				if(empty($vendor_settings)){
					$custom_settings['default']['products'] = $pack_products;
				}else{
					$custom_settings = $vendor_settings;
				}

				$order_id = $order_data['id'];
	       		$order_currency = $order_data['currency'];

	       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
				// $order_shipping_last_name = $order_data['shipping']['last_name'];
				// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
				// $order_shipping_address_1 = $order_data['shipping']['address_1'];
				// $order_shipping_address_2 = $order_data['shipping']['address_2'];
				// $order_shipping_city = $order_data['shipping']['city'];
				// $order_shipping_state = $order_data['shipping']['state'];
				// $order_shipping_postcode = $order_data['shipping']['postcode'];
				// $order_shipping_country = $order_data['shipping']['country'];
				// $order_shipping_phone = $order_data['billing']['phone'];
				// $order_shipping_email = $order_data['billing']['email'];

				$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
                $order_shipping_first_name = $shipping_arr['first_name'];
                $order_shipping_last_name = $shipping_arr['last_name'];
                $order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
                $order_shipping_address_1 = $shipping_arr['address_1'];
                $order_shipping_address_2 = $shipping_arr['address_2'];
                $order_shipping_city = $shipping_arr['city'];
                $order_shipping_state = $shipping_arr['state'];
                $order_shipping_postcode = $shipping_arr['postcode'];
                $order_shipping_country = $shipping_arr['country'];
                $order_shipping_phone = $order_data['billing']['phone'];
                $order_shipping_email = $order_data['billing']['email'];
				if(!empty($general_settings) && isset($general_settings['hits_dbs_integration_key'])){
					$mode = 'live';
					if(isset($general_settings['hits_dbs_test']) && $general_settings['hits_dbs_test']== 'yes'){
						$mode = 'test';
					}
					$execution = 'manual';
					if(isset($general_settings['hits_dbs_label_automation']) && $general_settings['hits_dbs_label_automation']== 'yes'){
						$execution = 'auto';
					}

					$boxes_to_shipo = array();
					if (isset($general_settings['hits_dbs_packing_type']) && $general_settings['hits_dbs_packing_type'] == "box") {
						if (isset($general_settings['hits_dbs_boxes']) && !empty($general_settings['hits_dbs_boxes'])) {
							foreach ($general_settings['hits_dbs_boxes'] as $box) {
								if ($box['enabled'] != 1) {
									continue;
								}else {
									$boxes_to_shipo[] = $box;
								}
							}
						}
					}

					$pic_frm = $pic_to = $ves_arr = $ves_dep = date('c');

					if (isset($general_settings['hits_dbs_pic_ready_from']) && !empty($general_settings['hits_dbs_pic_ready_from']) && $general_settings['hits_dbs_pic_ready_from'] > 0) {
						$pic_frm = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_from'].' days'));
					}
					if (isset($general_settings['hits_dbs_pic_ready_to']) && !empty($general_settings['hits_dbs_pic_ready_to']) && $general_settings['hits_dbs_pic_ready_to'] > 0) {
						$pic_to = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_to'].' days'));
					}
					if (isset($general_settings['hits_dbs_ves_arr']) && !empty($general_settings['hits_dbs_ves_arr']) && $general_settings['hits_dbs_ves_arr'] > 0) {
						$ves_arr = date('c', strtotime('+'.$general_settings['hits_dbs_ves_arr'].' days'));
					}
					if (isset($general_settings['hits_dbs_ves_dep']) && !empty($general_settings['hits_dbs_ves_dep']) && $general_settings['hits_dbs_ves_dep'] > 0) {
						$ves_dep = date('c', strtotime('+'.$general_settings['hits_dbs_ves_dep'].' days'));
					}

					$cod = ( isset($general_settings['hits_dbs_cod']) && $general_settings['hits_dbs_cod'] == 'yes') ? "Y" : "N";
					if (isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes') {
						$cod = 'N';
					}

					foreach ($custom_settings as $key => $cvalue) {

						//For Automatic Label Generation						
						
						$data = array();
						$data['integrated_key'] = $general_settings['hits_dbs_integration_key'];
						$data['order_id'] = $order_id;
						$data['exec_type'] = $execution;
						$data['mode'] = $mode;
						$data['carrier_type'] = "dbs";
						$data['ship_price'] = $order_data['shipping_total'];
						$data['meta'] = array(
							"site_id" => $cvalue['hits_dbs_site_id'],
							"password"  => '',
							"accountnum" => '',
							"t_company" => $order_shipping_company,
							"t_address1" => str_replace('"', '', $order_shipping_address_1),
							"t_address2" => str_replace('"', '', $order_shipping_address_2),
							"t_city" => $order_shipping_city,
							"t_state" => $order_shipping_state,
							"t_postal" => $order_shipping_postcode,
							"t_country" => $order_shipping_country,
							"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
							"t_phone" => $order_shipping_phone,
							"t_email" => $order_shipping_email,
							"insurance" => $general_settings['hits_dbs_insure'],
							"pack_this" => "Y",
							"products" => $cvalue['products'],
							"pack_algorithm" => $general_settings['hits_dbs_packing_type'],
							"boxes" => $boxes_to_shipo,
							"max_weight" => $general_settings['hits_dbs_max_weight'],
							"cod" => $cod,
							"service_code" => $service_code,
							"shipment_content" => $ship_content,
							"email_alert" => ( isset($general_settings['hits_dbs_email_alert']) && ($general_settings['hits_dbs_email_alert'] == 'yes') ) ? "Y" : "N",
							"s_company" => $cvalue['hits_dbs_company'],
							"s_address1" => $cvalue['hits_dbs_address1'],
							"s_address2" => $cvalue['hits_dbs_address2'],
							"s_city" => $cvalue['hits_dbs_city'],
							"s_state" => $cvalue['hits_dbs_state'],
							"s_postal" => $cvalue['hits_dbs_zip'],
							"s_country" => $cvalue['hits_dbs_country'],
							"gstin" => $cvalue['hits_dbs_gstin'],
							"s_name" => $cvalue['hits_dbs_shipper_name'],
							"s_phone" => $cvalue['hits_dbs_mob_num'],
							"s_email" => $cvalue['hits_dbs_email'],
							"label_size" => $general_settings['hits_dbs_print_size'],
							"sent_email_to" => $cvalue['hits_dbs_label_email'],
				            "pic_from" => $pic_frm,
		    				"pic_to" => $pic_to,
		    				"ves_arr" => $ves_arr,
		    				"ves_dep" => $ves_dep,
							"label" => $key,
							"payment_con" => (isset($general_settings['hits_dbs_pay_con']) ? $general_settings['hits_dbs_pay_con'] : 'S'),
							"cus_payment_con" => (isset($general_settings['hits_dbs_cus_pay_con']) ? $general_settings['hits_dbs_cus_pay_con'] : ''),
							"translation" => ( (isset($general_settings['hits_dbs_translation']) && $general_settings['hits_dbs_translation'] == "yes" ) ? 'Y' : 'N'),
							"translation_key" => (isset($general_settings['hits_dbs_translation_key']) ? $general_settings['hits_dbs_translation_key'] : ''),
							"loc_type_sender" => (isset($general_settings['hits_dbs_loc_type_sender']) ? $general_settings['hits_dbs_loc_type_sender'] : ''),
							"loc_type_receiver" => (isset($general_settings['hits_dbs_loc_type_receiver']) ? $general_settings['hits_dbs_loc_type_receiver'] : ''),
							"con_type_sender" => (isset($general_settings['hits_dbs_con_per_type_sender']) ? $general_settings['hits_dbs_con_per_type_sender'] : ''),
							"con_type_receiver" => (isset($general_settings['hits_dbs_con_per_type_receiver']) ? $general_settings['hits_dbs_con_per_type_receiver'] : ''),
							"incoterm_air" => (isset($general_settings['hits_dbs_incoterm_air']) ? $general_settings['hits_dbs_incoterm_air'] : ''),
							"incoterm_ocean" => (isset($general_settings['hits_dbs_incoterm_ocean']) ? $general_settings['hits_dbs_incoterm_ocean'] : ''),
							"incoterm_land" => (isset($general_settings['hits_dbs_incoterm_land']) ? $general_settings['hits_dbs_incoterm_land'] : ''),
							"incoterm_loc_air" => (isset($general_settings['hits_dbs_incoterm_loc_air']) ? $general_settings['hits_dbs_incoterm_loc_air'] : ''),
							"incoterm_loc_ocean" => (isset($general_settings['hits_dbs_incoterm_loc_ocean']) ? $general_settings['hits_dbs_incoterm_loc_ocean'] : ''),
							"incoterm_loc_land" => (isset($general_settings['hits_dbs_incoterm_loc_land']) ? $general_settings['hits_dbs_incoterm_loc_land'] : ''),
							"ser_type_air" => (isset($general_settings['hits_dbs_ser_type_air']) ? $general_settings['hits_dbs_ser_type_air'] : ''),
							"ser_type_ocean" => (isset($general_settings['hits_dbs_ser_type_ocean']) ? $general_settings['hits_dbs_ser_type_ocean'] : ''),
							"ship_pack_type" => (isset($general_settings['hits_dbs_ship_pack_type']) ? $general_settings['hits_dbs_ship_pack_type'] : ''),
							"container_type" => (isset($general_settings['hits_dbs_container_type']) ? $general_settings['hits_dbs_container_type'] : ''),
							"food" => ( (isset($general_settings['hits_dbs_food']) && !empty($general_settings['hits_dbs_food']) && $general_settings['hits_dbs_food'] == 'yes') ? 'Y' : 'N'),
							"heat" => ( (isset($general_settings['hits_dbs_heat']) && !empty($general_settings['hits_dbs_heat']) && $general_settings['hits_dbs_heat'] == 'yes') ? 'Y' : 'N'),
							"wight_dim_unit" => (isset($general_settings['hits_dbs_weight_unit']) ? $general_settings['hits_dbs_weight_unit'] : 'KG_CM'),
							"ship_charge" => isset($order_data['total']) ? $order_data['total'] : 0,
							// "" => (isset($general_settings['']) ? $general_settings[''] : ''),
							
						);
						// echo"<pre>";print_r(json_encode($data));die();
						//For Automatic Label
				  		// $request_url = "http://localhost/hitshipo/label_api/create_shipment.php";		
						$request_url = "https://app.hitshipo.com/label_api/create_shipment.php";
						$result = wp_remote_post($request_url, array(
							'method' => 'POST',
							'timeout' => 60,
							'sslverify' => 0,
							'headers'     => array(),
    						'cookies'     => array(),
							'body' => json_encode($data),
							'sslverify'   => FALSE
						));
					}
	       		
				}	
		    }

		    // Save the data of the Meta field
			public function hits_create_dbs_shipping( $order_id ) {
				
				if ($this->hpos_enabled) {
					if ('shop_order' !== Automattic\WooCommerce\Utilities\OrderUtil::get_order_type($order_id)) {
						return;
					}
				} else {
					$post = get_post($order_id);
					if($post->post_type !='shop_order' ){
						return;
					}
				}
		    	
		    	if (  isset( $_POST[ 'hits_dbs_reset' ] ) ) {
		    		delete_option('hits_dbs_values_'.$order_id);
		    	}

		    	if (  isset( $_POST['hits_dbs_create_label']) ) {
		    		$create_shipment_for = sanitize_text_field($_POST['hits_dbs_create_label']);
		           $service_code = sanitize_text_field($_POST['hits_dbs_service_code_'.$create_shipment_for]);
		           $ship_content = !empty($_POST['hits_dbs_shipment_content_'.$create_shipment_for]) ? sanitize_text_field($_POST['hits_dbs_shipment_content_'.$create_shipment_for]) : 'Shipment Content';
		           $order = wc_get_order( $order_id );
			       if($order){
		       		$order_data = $order->get_data();
			       		$order_id = $order_data['id'];
			       		$order_currency = $order_data['currency'];

			       		// $order_shipping_first_name = $order_data['shipping']['first_name'];
						// $order_shipping_last_name = $order_data['shipping']['last_name'];
						// $order_shipping_company = empty($order_data['shipping']['company']) ? $order_data['shipping']['first_name'] :  $order_data['shipping']['company'];
						// $order_shipping_address_1 = $order_data['shipping']['address_1'];
						// $order_shipping_address_2 = $order_data['shipping']['address_2'];
						// $order_shipping_city = $order_data['shipping']['city'];
						// $order_shipping_state = $order_data['shipping']['state'];
						// $order_shipping_postcode = $order_data['shipping']['postcode'];
						// $order_shipping_country = $order_data['shipping']['country'];
						// $order_shipping_phone = $order_data['billing']['phone'];
						// $order_shipping_email = $order_data['billing']['email'];

						$shipping_arr = (isset($order_data['shipping']['first_name']) && $order_data['shipping']['first_name'] != "") ? $order_data['shipping'] : $order_data['billing'];
						$order_shipping_first_name = $shipping_arr['first_name'];
						$order_shipping_last_name = $shipping_arr['last_name'];
						$order_shipping_company = empty($shipping_arr['company']) ? $shipping_arr['first_name'] :  $shipping_arr['company'];
						$order_shipping_address_1 = $shipping_arr['address_1'];
						$order_shipping_address_2 = $shipping_arr['address_2'];
						$order_shipping_city = $shipping_arr['city'];
						$order_shipping_state = $shipping_arr['state'];
						$order_shipping_postcode = $shipping_arr['postcode'];
						$order_shipping_country = $shipping_arr['country'];
						$order_shipping_phone = $order_data['billing']['phone'];
						$order_shipping_email = $order_data['billing']['email'];

						$items = $order->get_items();
						$pack_products = array();
						$general_settings = get_option('hits_dbs_main_settings',array());

						foreach ( $items as $item ) {
							$product_data = $item->get_data();
						    $product = array();
						    $product['product_name'] = str_replace('"', '', $product_data['name']);
						    $product['product_quantity'] = $product_data['quantity'];
						   	$product['product_id'] = $product_data['product_id'];

						    $product_variation_id = $item->get_variation_id();
						    if(empty($product_variation_id)){
						    	$getproduct = wc_get_product( $product_data['product_id'] );
						    }else{
						    	$getproduct = wc_get_product( $product_variation_id );
						    }
						    
						    $woo_weight_unit = get_option('woocommerce_weight_unit');
							$woo_dimension_unit = get_option('woocommerce_dimension_unit');

							$hits_dbs_mod_weight_unit = $hits_dbs_mod_dim_unit = '';

							if(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM')
							{
								$hits_dbs_mod_weight_unit = 'kg';
								$hits_dbs_mod_dim_unit = 'cm';
							}elseif(!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'LB_IN')
							{
								$hits_dbs_mod_weight_unit = 'lbs';
								$hits_dbs_mod_dim_unit = 'in';
							}
							else
							{
								$hits_dbs_mod_weight_unit = 'kg';
								$hits_dbs_mod_dim_unit = 'cm';
							}

						    $product['price'] = $getproduct->get_price();

						    if(!$product['price']){
								$product['price'] = (isset($product_data['total']) && isset($product_data['quantity'])) ? number_format(($product_data['total'] / $product_data['quantity']), 2) : 0;
							}

						    if ($woo_dimension_unit != $hits_dbs_mod_dim_unit) {
					    	$prod_width = $getproduct->get_width();
					    	$prod_height = $getproduct->get_height();
					    	$prod_depth = $getproduct->get_length();

					    	//wc_get_dimension( $dimension, $to_unit, $from_unit );
					    	$product['width'] = (!empty($prod_width) && $prod_width > 0) ? round(wc_get_dimension( $prod_width, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;
					    	$product['height'] = (!empty($prod_height) && $prod_height > 0) ? round(wc_get_dimension( $prod_height, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;
							$product['depth'] = (!empty($prod_depth) && $prod_depth > 0) ? round(wc_get_dimension( $prod_depth, $hits_dbs_mod_dim_unit, $woo_dimension_unit ), 2) : 0.5;

						    }else {
						    	$product['width'] = (!empty($getproduct->get_width()) && $getproduct->get_width() > 0) ? round($getproduct->get_width(), 2) : 0.5;
						    	$product['height'] = (!empty($getproduct->get_height()) && $getproduct->get_height() > 0) ? round($getproduct->get_height(), 2) : 0.5;
						    	$product['depth'] = (!empty($getproduct->get_length()) && $getproduct->get_length() > 0) ? round($getproduct->get_length(), 2) : 0.5;
						    }
						    
						    if ($woo_weight_unit != $hits_dbs_mod_weight_unit) {
						    	$prod_weight = $getproduct->get_weight();
						    	$product['weight'] = (!empty($prod_weight) && $prod_weight > 0) ? round(wc_get_weight( $prod_weight, $hits_dbs_mod_weight_unit, $woo_weight_unit ), 2) : 0.1 ;
						    }else{
						    	$product['weight'] = (!empty($getproduct->get_weight()) && $getproduct->get_weight() > 0) ? round($getproduct->get_weight(), 2) : 0.1;
							}

						    $pack_products[] = $product;
						    
						}
						
						$custom_settings = array();
						$custom_settings['default'] = array(
											'hits_dbs_site_id' => $general_settings['hits_dbs_site_id'],
											'hits_dbs_shipper_name' => $general_settings['hits_dbs_shipper_name'],
											'hits_dbs_company' => $general_settings['hits_dbs_company'],
											'hits_dbs_mob_num' => $general_settings['hits_dbs_mob_num'],
											'hits_dbs_email' => $general_settings['hits_dbs_email'],
											'hits_dbs_address1' => $general_settings['hits_dbs_address1'],
											'hits_dbs_address2' => $general_settings['hits_dbs_address2'],
											'hits_dbs_city' => $general_settings['hits_dbs_city'],
											'hits_dbs_state' => $general_settings['hits_dbs_state'],
											'hits_dbs_zip' => $general_settings['hits_dbs_zip'],
											'hits_dbs_country' => $general_settings['hits_dbs_country'],
											'hits_dbs_gstin' => $general_settings['hits_dbs_gstin'],
											'hits_dbs_con_rate' => $general_settings['hits_dbs_con_rate'],
											'service_code' => $service_code,
											'hits_dbs_label_email' => $general_settings['hits_dbs_label_email'],
										);
						$vendor_settings = array();
						if(isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' && isset($general_settings['hits_dbs_v_labels']) && $general_settings['hits_dbs_v_labels'] == 'yes'){
						// Multi Vendor Enabled
						foreach ($pack_products as $key => $value) {
							$product_id = $value['product_id'];
							if ($this->hpos_enabled) {
								$hpos_prod_data = wc_get_product($product_id);
								$dbs_account = $hpos_prod_data->get_meta("hits_dbs_address");
							} else {
								$dbs_account = get_post_meta($product_id,'hits_dbs_address', true);
							}
							if(empty($dbs_account) || $dbs_account == 'default'){
								$dbs_account = 'default';
								if (!isset($vendor_settings[$dbs_account])) {
									$vendor_settings[$dbs_account] = $custom_settings['default'];
								}
								
								$vendor_settings[$dbs_account]['products'][] = $value;
							}

							if($dbs_account != 'default'){
								$user_account = get_post_meta($dbs_account,'hits_dbs_vendor_settings', true);
								$user_account = empty($user_account) ? array() : $user_account;
								if(!empty($user_account)){
									if(!isset($vendor_settings[$dbs_account])){

										$vendor_settings[$dbs_account] = $custom_settings['default'];
										
									if($user_account['hits_dbs_site_id'] != ''){
										
										$vendor_settings[$dbs_account]['hits_dbs_site_id'] = $user_account['hits_dbs_site_id'];

									}

									if ($user_account['hits_dbs_address1'] != '' && $user_account['hits_dbs_city'] != '' && $user_account['hits_dbs_state'] != '' && $user_account['hits_dbs_zip'] != '' && $user_account['hits_dbs_country'] != '' && $user_account['hits_dbs_shipper_name'] != '') {
										
										if($user_account['hits_dbs_shipper_name'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_shipper_name'] = $user_account['hits_dbs_shipper_name'];
										}

										if($user_account['hits_dbs_company'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_company'] = $user_account['hits_dbs_company'];
										}

										if($user_account['hits_dbs_mob_num'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_mob_num'] = $user_account['hits_dbs_mob_num'];
										}

										if($user_account['hits_dbs_email'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_email'] = $user_account['hits_dbs_email'];
										}

										if ($user_account['hits_dbs_address1'] != '') {
											$vendor_settings[$dbs_account]['hits_dbs_address1'] = $user_account['hits_dbs_address1'];
										}

										$vendor_settings[$dbs_account]['hits_dbs_address2'] = $user_account['hits_dbs_address2'];
										
										if($user_account['hits_dbs_city'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_city'] = $user_account['hits_dbs_city'];
										}

										if($user_account['hits_dbs_state'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_state'] = $user_account['hits_dbs_state'];
										}

										if($user_account['hits_dbs_zip'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_zip'] = $user_account['hits_dbs_zip'];
										}

										if($user_account['hits_dbs_country'] != ''){
											$vendor_settings[$dbs_account]['hits_dbs_country'] = $user_account['hits_dbs_country'];
										}

										$vendor_settings[$dbs_account]['hits_dbs_gstin'] = $user_account['hits_dbs_gstin'];
										$vendor_settings[$dbs_account]['hits_dbs_con_rate'] = $user_account['hits_dbs_con_rate'];

									}
										
										if(isset($general_settings['hits_dbs_v_email']) && $general_settings['hits_dbs_v_email'] == 'yes'){
											$user_dat = get_userdata($dbs_account);
											$vendor_settings[$dbs_account]['hits_dbs_label_email'] = $user_dat->data->user_email;
										}
										

										if($order_data['shipping']['country'] != $vendor_settings[$dbs_account]['hits_dbs_country']){
											$vendor_settings[$dbs_account]['service_code'] = empty($service_code) ? $user_account['hits_dbs_def_inter'] : $service_code;
										}else{
											$vendor_settings[$dbs_account]['service_code'] = empty($service_code) ? $user_account['hits_dbs_def_dom'] : $service_code;
										}
									}
									unset($value['product_id']);
									$vendor_settings[$dbs_account]['products'][] = $value;
								}
							}

						}

					}

					if(empty($vendor_settings)){
						$custom_settings['default']['products'] = $pack_products;
					}else{
						$custom_settings = $vendor_settings;
					}

					if(!empty($general_settings) && isset($general_settings['hits_dbs_integration_key']) && isset($custom_settings[$create_shipment_for])){
						$mode = 'live';
						if(isset($general_settings['hits_dbs_test']) && $general_settings['hits_dbs_test']== 'yes'){
							$mode = 'test';
						}

						$execution = 'manual';
						
						$boxes_to_shipo = array();
						if (isset($general_settings['hits_dbs_packing_type']) && $general_settings['hits_dbs_packing_type'] == "box") {
							if (isset($general_settings['hits_dbs_boxes']) && !empty($general_settings['hits_dbs_boxes'])) {
								foreach ($general_settings['hits_dbs_boxes'] as $box) {
									if ($box['enabled'] != 1) {
										continue;
									}else {
										$boxes_to_shipo[] = $box;
									}
								}
							}
						}

						$pic_frm = $pic_to = $ves_arr = $ves_dep = date('c');

						if (isset($general_settings['hits_dbs_pic_ready_from']) && !empty($general_settings['hits_dbs_pic_ready_from']) && $general_settings['hits_dbs_pic_ready_from'] > 0) {
							$pic_frm = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_from'].' days'));
						}

						if (isset($general_settings['hits_dbs_pic_ready_to']) && !empty($general_settings['hits_dbs_pic_ready_to']) && $general_settings['hits_dbs_pic_ready_to'] > 0) {
							$pic_to = date('c', strtotime('+'.$general_settings['hits_dbs_pic_ready_to'].' days'));
						}

						if (isset($general_settings['hits_dbs_ves_arr']) && !empty($general_settings['hits_dbs_ves_arr']) && $general_settings['hits_dbs_ves_arr'] > 0) {
							$ves_arr = date('c', strtotime('+'.$general_settings['hits_dbs_ves_arr'].' days'));
						}

						if (isset($general_settings['hits_dbs_ves_dep']) && !empty($general_settings['hits_dbs_ves_dep']) && $general_settings['hits_dbs_ves_dep'] > 0) {
							$ves_dep = date('c', strtotime('+'.$general_settings['hits_dbs_ves_dep'].' days'));
						}

						$cod = ( isset($general_settings['hits_dbs_cod']) && $general_settings['hits_dbs_cod'] == 'yes') ? "Y" : "N";
						if (isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes') {
							$cod = 'N';
						}
						
						$data = array();
						$data['integrated_key'] = $general_settings['hits_dbs_integration_key'];
						$data['order_id'] = $order_id;
						$data['exec_type'] = $execution;
						$data['mode'] = $mode;
						$data['carrier_type'] = "dbs";
						$data['ship_price'] = 0;
						$data['meta'] = array(
							"site_id" => $custom_settings[$create_shipment_for]['hits_dbs_site_id'],
							"t_company" => $order_shipping_company,
							"t_address1" => str_replace('"', '', $order_shipping_address_1),
							"t_address2" => str_replace('"', '', $order_shipping_address_2),
							"t_city" => $order_shipping_city,
							"t_state" => $order_shipping_state,
							"t_postal" => $order_shipping_postcode,
							"t_country" => $order_shipping_country,
							"t_name" => $order_shipping_first_name . ' '. $order_shipping_last_name,
							"t_phone" => $order_shipping_phone,
							"t_email" => $order_shipping_email,
							"insurance" => ( (isset($general_settings['hits_dbs_insure']) && !empty($general_settings['hits_dbs_insure']) && $general_settings['hits_dbs_insure'] == 'yes') ? 'Y' : 'N'),
							"pack_this" => "Y",
							"products" => $custom_settings[$create_shipment_for]['products'],
							"pack_algorithm" => $general_settings['hits_dbs_packing_type'],
							"boxes" => $boxes_to_shipo,
							"max_weight" => $general_settings['hits_dbs_max_weight'],
							"cod" => $cod,
							"service_code" => $custom_settings[$create_shipment_for]['service_code'],
							"shipment_content" => $ship_content,
							"email_alert" => ( isset($general_settings['hits_dbs_email_alert']) && ($general_settings['hits_dbs_email_alert'] == 'yes') ) ? "Y" : "N",
							"s_company" => $custom_settings[$create_shipment_for]['hits_dbs_company'],
							"s_address1" => $custom_settings[$create_shipment_for]['hits_dbs_address1'],
							"s_address2" => $custom_settings[$create_shipment_for]['hits_dbs_address2'],
							"s_city" => $custom_settings[$create_shipment_for]['hits_dbs_city'],
							"s_state" => $custom_settings[$create_shipment_for]['hits_dbs_state'],
							"s_postal" => $custom_settings[$create_shipment_for]['hits_dbs_zip'],
							"s_country" => $custom_settings[$create_shipment_for]['hits_dbs_country'],
							"gstin" => $custom_settings[$create_shipment_for]['hits_dbs_gstin'],
							"s_name" => $custom_settings[$create_shipment_for]['hits_dbs_shipper_name'],
							"s_phone" => $custom_settings[$create_shipment_for]['hits_dbs_mob_num'],
							"s_email" => $custom_settings[$create_shipment_for]['hits_dbs_email'],
							"label_size" => $general_settings['hits_dbs_print_size'],
							"sent_email_to" => $custom_settings[$create_shipment_for]['hits_dbs_label_email'],
				            "pic_from" => $pic_frm,
		    				"pic_to" => $pic_to,
		    				"ves_arr" => $ves_arr,
		    				"ves_dep" => $ves_dep,
		    				"payment_con" => (isset($general_settings['hits_dbs_pay_con']) ? $general_settings['hits_dbs_pay_con'] : 'S'),
							"cus_payment_con" => (isset($general_settings['hits_dbs_cus_pay_con']) ? $general_settings['hits_dbs_cus_pay_con'] : ''),
							"translation" => ( (isset($general_settings['hits_dbs_translation']) && $general_settings['hits_dbs_translation'] == "yes" ) ? 'Y' : 'N'),
							"translation_key" => (isset($general_settings['hits_dbs_translation_key']) ? $general_settings['hits_dbs_translation_key'] : ''),
							"loc_type_sender" => (isset($general_settings['hits_dbs_loc_type_sender']) ? $general_settings['hits_dbs_loc_type_sender'] : ''),
							"loc_type_receiver" => (isset($general_settings['hits_dbs_loc_type_receiver']) ? $general_settings['hits_dbs_loc_type_receiver'] : ''),
							"con_type_sender" => (isset($general_settings['hits_dbs_con_per_type_sender']) ? $general_settings['hits_dbs_con_per_type_sender'] : ''),
							"con_type_receiver" => (isset($general_settings['hits_dbs_con_per_type_receiver']) ? $general_settings['hits_dbs_con_per_type_receiver'] : ''),
							"incoterm_air" => (isset($general_settings['hits_dbs_incoterm_air']) ? $general_settings['hits_dbs_incoterm_air'] : ''),
							"incoterm_ocean" => (isset($general_settings['hits_dbs_incoterm_ocean']) ? $general_settings['hits_dbs_incoterm_ocean'] : ''),
							"incoterm_land" => (isset($general_settings['hits_dbs_incoterm_land']) ? $general_settings['hits_dbs_incoterm_land'] : ''),
							"incoterm_loc_air" => (isset($general_settings['hits_dbs_incoterm_loc_air']) ? $general_settings['hits_dbs_incoterm_loc_air'] : ''),
							"incoterm_loc_ocean" => (isset($general_settings['hits_dbs_incoterm_loc_ocean']) ? $general_settings['hits_dbs_incoterm_loc_ocean'] : ''),
							"incoterm_loc_land" => (isset($general_settings['hits_dbs_incoterm_loc_land']) ? $general_settings['hits_dbs_incoterm_loc_land'] : ''),
							"ser_type_air" => (isset($general_settings['hits_dbs_ser_type_air']) ? $general_settings['hits_dbs_ser_type_air'] : ''),
							"ser_type_ocean" => (isset($general_settings['hits_dbs_ser_type_ocean']) ? $general_settings['hits_dbs_ser_type_ocean'] : ''),
							"ship_pack_type" => (isset($general_settings['hits_dbs_ship_pack_type']) ? $general_settings['hits_dbs_ship_pack_type'] : ''),
							"container_type" => (isset($general_settings['hits_dbs_container_type']) ? $general_settings['hits_dbs_container_type'] : ''),
							"food" => ( (isset($general_settings['hits_dbs_food']) && !empty($general_settings['hits_dbs_food']) && $general_settings['hits_dbs_food'] == 'yes') ? 'Y' : 'N'),
							"heat" => ( (isset($general_settings['hits_dbs_heat']) && !empty($general_settings['hits_dbs_heat']) && $general_settings['hits_dbs_heat'] == 'yes') ? 'Y' : 'N'),
							"wight_dim_unit" => (isset($general_settings['hits_dbs_weight_unit']) ? $general_settings['hits_dbs_weight_unit'] : 'KG_CM'),
							"ship_charge" => (isset($order_data['total']) && !empty($order_data['total'])) ? $order_data['total'] : 0,
							"label" => $create_shipment_for
							// "" => (isset($general_settings['']) ? $general_settings[''] : ''),
						);

							// echo '<pre>';print_r(json_encode($data));die();
							//For Manual Label
							// $request_url = "http://localhost/hitshipo/label_api/create_shipment.php";		
							$request_url = "https://app.hitshipo.com/label_api/create_shipment.php";
							$result = wp_remote_post($request_url, array(
								'method' => 'POST',
								'timeout' => 60,
								'sslverify' => 0,
								'headers'     => array(),
    							'cookies'     => array(),
								'body' => json_encode($data),
								'sslverify'   => FALSE
							));
							
							if (is_array($result) && !isset($result['body'])) {
								update_option('hits_dbs_status_'.$order_id, 'Site not Connected with HITShipo. Contact HITShipo Team.');
								return;
							}
							$output = (is_array($result) && isset($result['body'])) ? json_decode($result['body'],true) : [];
							
							if($output){
								if(isset($output['status'])){
									if(isset($output['status'])&& $output['status'] != 'success'){
										   update_option('hits_dbs_status_'.$order_id, $output['status']);
									}else if(isset($output['status']) && $output['status'] == 'success'){										
										$output['user_id'] = $create_shipment_for;
										$val = get_option('hits_dbs_values_'.$order_id, []);
										$result_arr = array();
										if(!empty($val)){
											$result_arr = json_decode($val, true);
										}										
										$result_arr[] = $output;
										update_option('hits_dbs_values_'.$order_id, json_encode($result_arr));										
									}									
								}else{
									update_option('hits_dbs_status_'.$order_id, 'Site not Connected with HITShipo. Contact HITShipo Team.');
									}
							}else{
								update_option('hits_dbs_status_'.$order_id, 'Site not Connected with HITShipo. Contact HITShipo Team.');
							}
						}	
			       }
		        }
		    }

		    // Save the data of the Meta field
			
		}
		
	}
	$hitshipo_dbs = new hitshipo_dbs_parent();
}
