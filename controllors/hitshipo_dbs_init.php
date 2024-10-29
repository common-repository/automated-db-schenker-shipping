<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
use Google\Cloud\Translate\TranslateClient;
if (!class_exists('HITShipo_DBS')) {
	class HITShipo_DBS extends WC_Shipping_Method
	{
		/**
		 * Constructor for your shipping class
		 *
		 * @access public
		 * @return void
		 */
		public function __construct()
		{
			$this->id                 = 'hits_dbs';
			$this->method_title       = __('Configure DB Schenker');  // Title shown in admin
			$this->title       = __('DB Schenker Shipping');
			$this->method_description = __('Real Time Rates, Premium Supports Label'); // 
			$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
			$this->init();
		}

		/**
		 * Init your settings
		 *
		 * @access public
		 * @return void
		 */
		function init()
		{
			// Load the settings API
			$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
			$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

			// Save settings in admin if you have any defined
			add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
		}

		/**
		 * calculate_shipping function.
		 *
		 * @access public
		 * @param mixed $package
		 * @return void
		 */
		public function calculate_shipping($package = array())
		{
			$pack_aft_hook = apply_filters('hits_dbs_rate_packages', $package);

			if (empty($pack_aft_hook)) {
				return;
			}

			$general_settings = get_option('hits_dbs_main_settings');
			$general_settings = empty($general_settings) ? array() : $general_settings;

			if (!is_array($general_settings)) {
				return;
			}

			if (!isset($general_settings['hits_dbs_rates']) || $general_settings['hits_dbs_rates'] != "yes") {
				return;
			}

			//excluded Countries
			if(isset($general_settings['hits_dbs_exclude_countries'])){
				if(in_array($pack_aft_hook['destination']['country'],$general_settings['hits_dbs_exclude_countries'])){
					return;
				}
			}

			$dbs_core = array();
			$dbs_core['AD'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['AE'] = array('region' => 'AP', 'currency' => 'AED', 'weight' => 'KG_CM');
			$dbs_core['AF'] = array('region' => 'AP', 'currency' => 'AFN', 'weight' => 'KG_CM');
			$dbs_core['AG'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['AI'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['AL'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['AM'] = array('region' => 'AP', 'currency' => 'AMD', 'weight' => 'KG_CM');
			$dbs_core['AN'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'KG_CM');
			$dbs_core['AO'] = array('region' => 'AP', 'currency' => 'AOA', 'weight' => 'KG_CM');
			$dbs_core['AR'] = array('region' => 'AM', 'currency' => 'ARS', 'weight' => 'KG_CM');
			$dbs_core['AS'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['AT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['AU'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dbs_core['AW'] = array('region' => 'AM', 'currency' => 'AWG', 'weight' => 'LB_IN');
			$dbs_core['AZ'] = array('region' => 'AM', 'currency' => 'AZN', 'weight' => 'KG_CM');
			$dbs_core['AZ'] = array('region' => 'AM', 'currency' => 'AZN', 'weight' => 'KG_CM');
			$dbs_core['GB'] = array('region' => 'EU', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['BA'] = array('region' => 'AP', 'currency' => 'BAM', 'weight' => 'KG_CM');
			$dbs_core['BB'] = array('region' => 'AM', 'currency' => 'BBD', 'weight' => 'LB_IN');
			$dbs_core['BD'] = array('region' => 'AP', 'currency' => 'BDT', 'weight' => 'KG_CM');
			$dbs_core['BE'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['BF'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['BG'] = array('region' => 'EU', 'currency' => 'BGN', 'weight' => 'KG_CM');
			$dbs_core['BH'] = array('region' => 'AP', 'currency' => 'BHD', 'weight' => 'KG_CM');
			$dbs_core['BI'] = array('region' => 'AP', 'currency' => 'BIF', 'weight' => 'KG_CM');
			$dbs_core['BJ'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['BM'] = array('region' => 'AM', 'currency' => 'BMD', 'weight' => 'LB_IN');
			$dbs_core['BN'] = array('region' => 'AP', 'currency' => 'BND', 'weight' => 'KG_CM');
			$dbs_core['BO'] = array('region' => 'AM', 'currency' => 'BOB', 'weight' => 'KG_CM');
			$dbs_core['BR'] = array('region' => 'AM', 'currency' => 'BRL', 'weight' => 'KG_CM');
			$dbs_core['BS'] = array('region' => 'AM', 'currency' => 'BSD', 'weight' => 'LB_IN');
			$dbs_core['BT'] = array('region' => 'AP', 'currency' => 'BTN', 'weight' => 'KG_CM');
			$dbs_core['BW'] = array('region' => 'AP', 'currency' => 'BWP', 'weight' => 'KG_CM');
			$dbs_core['BY'] = array('region' => 'AP', 'currency' => 'BYR', 'weight' => 'KG_CM');
			$dbs_core['BZ'] = array('region' => 'AM', 'currency' => 'BZD', 'weight' => 'KG_CM');
			$dbs_core['CA'] = array('region' => 'AM', 'currency' => 'CAD', 'weight' => 'LB_IN');
			$dbs_core['CF'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['CG'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['CH'] = array('region' => 'EU', 'currency' => 'CHF', 'weight' => 'KG_CM');
			$dbs_core['CI'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['CK'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dbs_core['CL'] = array('region' => 'AM', 'currency' => 'CLP', 'weight' => 'KG_CM');
			$dbs_core['CM'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['CN'] = array('region' => 'AP', 'currency' => 'CNY', 'weight' => 'KG_CM');
			$dbs_core['CO'] = array('region' => 'AM', 'currency' => 'COP', 'weight' => 'KG_CM');
			$dbs_core['CR'] = array('region' => 'AM', 'currency' => 'CRC', 'weight' => 'KG_CM');
			$dbs_core['CU'] = array('region' => 'AM', 'currency' => 'CUC', 'weight' => 'KG_CM');
			$dbs_core['CV'] = array('region' => 'AP', 'currency' => 'CVE', 'weight' => 'KG_CM');
			$dbs_core['CY'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['CZ'] = array('region' => 'EU', 'currency' => 'CZK', 'weight' => 'KG_CM');
			$dbs_core['DE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['DJ'] = array('region' => 'EU', 'currency' => 'DJF', 'weight' => 'KG_CM');
			$dbs_core['DK'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dbs_core['DM'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['DO'] = array('region' => 'AP', 'currency' => 'DOP', 'weight' => 'LB_IN');
			$dbs_core['DZ'] = array('region' => 'AM', 'currency' => 'DZD', 'weight' => 'KG_CM');
			$dbs_core['EC'] = array('region' => 'EU', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['EE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['EG'] = array('region' => 'AP', 'currency' => 'EGP', 'weight' => 'KG_CM');
			$dbs_core['ER'] = array('region' => 'EU', 'currency' => 'ERN', 'weight' => 'KG_CM');
			$dbs_core['ES'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['ET'] = array('region' => 'AU', 'currency' => 'ETB', 'weight' => 'KG_CM');
			$dbs_core['FI'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['FJ'] = array('region' => 'AP', 'currency' => 'FJD', 'weight' => 'KG_CM');
			$dbs_core['FK'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['FM'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['FO'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dbs_core['FR'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['GA'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['GB'] = array('region' => 'EU', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['GD'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['GE'] = array('region' => 'AM', 'currency' => 'GEL', 'weight' => 'KG_CM');
			$dbs_core['GF'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['GG'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['GH'] = array('region' => 'AP', 'currency' => 'GHS', 'weight' => 'KG_CM');
			$dbs_core['GI'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['GL'] = array('region' => 'AM', 'currency' => 'DKK', 'weight' => 'KG_CM');
			$dbs_core['GM'] = array('region' => 'AP', 'currency' => 'GMD', 'weight' => 'KG_CM');
			$dbs_core['GN'] = array('region' => 'AP', 'currency' => 'GNF', 'weight' => 'KG_CM');
			$dbs_core['GP'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['GQ'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['GR'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['GT'] = array('region' => 'AM', 'currency' => 'GTQ', 'weight' => 'KG_CM');
			$dbs_core['GU'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['GW'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['GY'] = array('region' => 'AP', 'currency' => 'GYD', 'weight' => 'LB_IN');
			$dbs_core['HK'] = array('region' => 'AM', 'currency' => 'HKD', 'weight' => 'KG_CM');
			$dbs_core['HN'] = array('region' => 'AM', 'currency' => 'HNL', 'weight' => 'KG_CM');
			$dbs_core['HR'] = array('region' => 'AP', 'currency' => 'HRK', 'weight' => 'KG_CM');
			$dbs_core['HT'] = array('region' => 'AM', 'currency' => 'HTG', 'weight' => 'LB_IN');
			$dbs_core['HU'] = array('region' => 'EU', 'currency' => 'HUF', 'weight' => 'KG_CM');
			$dbs_core['IC'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['ID'] = array('region' => 'AP', 'currency' => 'IDR', 'weight' => 'KG_CM');
			$dbs_core['IE'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['IL'] = array('region' => 'AP', 'currency' => 'ILS', 'weight' => 'KG_CM');
			$dbs_core['IN'] = array('region' => 'AP', 'currency' => 'INR', 'weight' => 'KG_CM');
			$dbs_core['IQ'] = array('region' => 'AP', 'currency' => 'IQD', 'weight' => 'KG_CM');
			$dbs_core['IR'] = array('region' => 'AP', 'currency' => 'IRR', 'weight' => 'KG_CM');
			$dbs_core['IS'] = array('region' => 'EU', 'currency' => 'ISK', 'weight' => 'KG_CM');
			$dbs_core['IT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['JE'] = array('region' => 'AM', 'currency' => 'GBP', 'weight' => 'KG_CM');
			$dbs_core['JM'] = array('region' => 'AM', 'currency' => 'JMD', 'weight' => 'KG_CM');
			$dbs_core['JO'] = array('region' => 'AP', 'currency' => 'JOD', 'weight' => 'KG_CM');
			$dbs_core['JP'] = array('region' => 'AP', 'currency' => 'JPY', 'weight' => 'KG_CM');
			$dbs_core['KE'] = array('region' => 'AP', 'currency' => 'KES', 'weight' => 'KG_CM');
			$dbs_core['KG'] = array('region' => 'AP', 'currency' => 'KGS', 'weight' => 'KG_CM');
			$dbs_core['KH'] = array('region' => 'AP', 'currency' => 'KHR', 'weight' => 'KG_CM');
			$dbs_core['KI'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dbs_core['KM'] = array('region' => 'AP', 'currency' => 'KMF', 'weight' => 'KG_CM');
			$dbs_core['KN'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['KP'] = array('region' => 'AP', 'currency' => 'KPW', 'weight' => 'LB_IN');
			$dbs_core['KR'] = array('region' => 'AP', 'currency' => 'KRW', 'weight' => 'KG_CM');
			$dbs_core['KV'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['KW'] = array('region' => 'AP', 'currency' => 'KWD', 'weight' => 'KG_CM');
			$dbs_core['KY'] = array('region' => 'AM', 'currency' => 'KYD', 'weight' => 'KG_CM');
			$dbs_core['KZ'] = array('region' => 'AP', 'currency' => 'KZF', 'weight' => 'LB_IN');
			$dbs_core['LA'] = array('region' => 'AP', 'currency' => 'LAK', 'weight' => 'KG_CM');
			$dbs_core['LB'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['LC'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'KG_CM');
			$dbs_core['LI'] = array('region' => 'AM', 'currency' => 'CHF', 'weight' => 'LB_IN');
			$dbs_core['LK'] = array('region' => 'AP', 'currency' => 'LKR', 'weight' => 'KG_CM');
			$dbs_core['LR'] = array('region' => 'AP', 'currency' => 'LRD', 'weight' => 'KG_CM');
			$dbs_core['LS'] = array('region' => 'AP', 'currency' => 'LSL', 'weight' => 'KG_CM');
			$dbs_core['LT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['LU'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['LV'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['LY'] = array('region' => 'AP', 'currency' => 'LYD', 'weight' => 'KG_CM');
			$dbs_core['MA'] = array('region' => 'AP', 'currency' => 'MAD', 'weight' => 'KG_CM');
			$dbs_core['MC'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['MD'] = array('region' => 'AP', 'currency' => 'MDL', 'weight' => 'KG_CM');
			$dbs_core['ME'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['MG'] = array('region' => 'AP', 'currency' => 'MGA', 'weight' => 'KG_CM');
			$dbs_core['MH'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['MK'] = array('region' => 'AP', 'currency' => 'MKD', 'weight' => 'KG_CM');
			$dbs_core['ML'] = array('region' => 'AP', 'currency' => 'COF', 'weight' => 'KG_CM');
			$dbs_core['MM'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['MN'] = array('region' => 'AP', 'currency' => 'MNT', 'weight' => 'KG_CM');
			$dbs_core['MO'] = array('region' => 'AP', 'currency' => 'MOP', 'weight' => 'KG_CM');
			$dbs_core['MP'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['MQ'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['MR'] = array('region' => 'AP', 'currency' => 'MRO', 'weight' => 'KG_CM');
			$dbs_core['MS'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['MT'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['MU'] = array('region' => 'AP', 'currency' => 'MUR', 'weight' => 'KG_CM');
			$dbs_core['MV'] = array('region' => 'AP', 'currency' => 'MVR', 'weight' => 'KG_CM');
			$dbs_core['MW'] = array('region' => 'AP', 'currency' => 'MWK', 'weight' => 'KG_CM');
			$dbs_core['MX'] = array('region' => 'AM', 'currency' => 'MXN', 'weight' => 'KG_CM');
			$dbs_core['MY'] = array('region' => 'AP', 'currency' => 'MYR', 'weight' => 'KG_CM');
			$dbs_core['MZ'] = array('region' => 'AP', 'currency' => 'MZN', 'weight' => 'KG_CM');
			$dbs_core['NA'] = array('region' => 'AP', 'currency' => 'NAD', 'weight' => 'KG_CM');
			$dbs_core['NC'] = array('region' => 'AP', 'currency' => 'XPF', 'weight' => 'KG_CM');
			$dbs_core['NE'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['NG'] = array('region' => 'AP', 'currency' => 'NGN', 'weight' => 'KG_CM');
			$dbs_core['NI'] = array('region' => 'AM', 'currency' => 'NIO', 'weight' => 'KG_CM');
			$dbs_core['NL'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['NO'] = array('region' => 'EU', 'currency' => 'NOK', 'weight' => 'KG_CM');
			$dbs_core['NP'] = array('region' => 'AP', 'currency' => 'NPR', 'weight' => 'KG_CM');
			$dbs_core['NR'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dbs_core['NU'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dbs_core['NZ'] = array('region' => 'AP', 'currency' => 'NZD', 'weight' => 'KG_CM');
			$dbs_core['OM'] = array('region' => 'AP', 'currency' => 'OMR', 'weight' => 'KG_CM');
			$dbs_core['PA'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['PE'] = array('region' => 'AM', 'currency' => 'PEN', 'weight' => 'KG_CM');
			$dbs_core['PF'] = array('region' => 'AP', 'currency' => 'XPF', 'weight' => 'KG_CM');
			$dbs_core['PG'] = array('region' => 'AP', 'currency' => 'PGK', 'weight' => 'KG_CM');
			$dbs_core['PH'] = array('region' => 'AP', 'currency' => 'PHP', 'weight' => 'KG_CM');
			$dbs_core['PK'] = array('region' => 'AP', 'currency' => 'PKR', 'weight' => 'KG_CM');
			$dbs_core['PL'] = array('region' => 'EU', 'currency' => 'PLN', 'weight' => 'KG_CM');
			$dbs_core['PR'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['PT'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['PW'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['PY'] = array('region' => 'AM', 'currency' => 'PYG', 'weight' => 'KG_CM');
			$dbs_core['QA'] = array('region' => 'AP', 'currency' => 'QAR', 'weight' => 'KG_CM');
			$dbs_core['RE'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['RO'] = array('region' => 'EU', 'currency' => 'RON', 'weight' => 'KG_CM');
			$dbs_core['RS'] = array('region' => 'AP', 'currency' => 'RSD', 'weight' => 'KG_CM');
			$dbs_core['RU'] = array('region' => 'AP', 'currency' => 'RUB', 'weight' => 'KG_CM');
			$dbs_core['RW'] = array('region' => 'AP', 'currency' => 'RWF', 'weight' => 'KG_CM');
			$dbs_core['SA'] = array('region' => 'AP', 'currency' => 'SAR', 'weight' => 'KG_CM');
			$dbs_core['SB'] = array('region' => 'AP', 'currency' => 'SBD', 'weight' => 'KG_CM');
			$dbs_core['SC'] = array('region' => 'AP', 'currency' => 'SCR', 'weight' => 'KG_CM');
			$dbs_core['SD'] = array('region' => 'AP', 'currency' => 'SDG', 'weight' => 'KG_CM');
			$dbs_core['SE'] = array('region' => 'EU', 'currency' => 'SEK', 'weight' => 'KG_CM');
			$dbs_core['SG'] = array('region' => 'AP', 'currency' => 'SGD', 'weight' => 'KG_CM');
			$dbs_core['SH'] = array('region' => 'AP', 'currency' => 'SHP', 'weight' => 'KG_CM');
			$dbs_core['SI'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['SK'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['SL'] = array('region' => 'AP', 'currency' => 'SLL', 'weight' => 'KG_CM');
			$dbs_core['SM'] = array('region' => 'EU', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['SN'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['SO'] = array('region' => 'AM', 'currency' => 'SOS', 'weight' => 'KG_CM');
			$dbs_core['SR'] = array('region' => 'AM', 'currency' => 'SRD', 'weight' => 'KG_CM');
			$dbs_core['SS'] = array('region' => 'AP', 'currency' => 'SSP', 'weight' => 'KG_CM');
			$dbs_core['ST'] = array('region' => 'AP', 'currency' => 'STD', 'weight' => 'KG_CM');
			$dbs_core['SV'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['SY'] = array('region' => 'AP', 'currency' => 'SYP', 'weight' => 'KG_CM');
			$dbs_core['SZ'] = array('region' => 'AP', 'currency' => 'SZL', 'weight' => 'KG_CM');
			$dbs_core['TC'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['TD'] = array('region' => 'AP', 'currency' => 'XAF', 'weight' => 'KG_CM');
			$dbs_core['TG'] = array('region' => 'AP', 'currency' => 'XOF', 'weight' => 'KG_CM');
			$dbs_core['TH'] = array('region' => 'AP', 'currency' => 'THB', 'weight' => 'KG_CM');
			$dbs_core['TJ'] = array('region' => 'AP', 'currency' => 'TJS', 'weight' => 'KG_CM');
			$dbs_core['TL'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['TN'] = array('region' => 'AP', 'currency' => 'TND', 'weight' => 'KG_CM');
			$dbs_core['TO'] = array('region' => 'AP', 'currency' => 'TOP', 'weight' => 'KG_CM');
			$dbs_core['TR'] = array('region' => 'AP', 'currency' => 'TRY', 'weight' => 'KG_CM');
			$dbs_core['TT'] = array('region' => 'AM', 'currency' => 'TTD', 'weight' => 'LB_IN');
			$dbs_core['TV'] = array('region' => 'AP', 'currency' => 'AUD', 'weight' => 'KG_CM');
			$dbs_core['TW'] = array('region' => 'AP', 'currency' => 'TWD', 'weight' => 'KG_CM');
			$dbs_core['TZ'] = array('region' => 'AP', 'currency' => 'TZS', 'weight' => 'KG_CM');
			$dbs_core['UA'] = array('region' => 'AP', 'currency' => 'UAH', 'weight' => 'KG_CM');
			$dbs_core['UG'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');
			$dbs_core['US'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['UY'] = array('region' => 'AM', 'currency' => 'UYU', 'weight' => 'KG_CM');
			$dbs_core['UZ'] = array('region' => 'AP', 'currency' => 'UZS', 'weight' => 'KG_CM');
			$dbs_core['VC'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['VE'] = array('region' => 'AM', 'currency' => 'VEF', 'weight' => 'KG_CM');
			$dbs_core['VG'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['VI'] = array('region' => 'AM', 'currency' => 'USD', 'weight' => 'LB_IN');
			$dbs_core['VN'] = array('region' => 'AP', 'currency' => 'VND', 'weight' => 'KG_CM');
			$dbs_core['VU'] = array('region' => 'AP', 'currency' => 'VUV', 'weight' => 'KG_CM');
			$dbs_core['WS'] = array('region' => 'AP', 'currency' => 'WST', 'weight' => 'KG_CM');
			$dbs_core['XB'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dbs_core['XC'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dbs_core['XE'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'LB_IN');
			$dbs_core['XM'] = array('region' => 'AM', 'currency' => 'EUR', 'weight' => 'LB_IN');
			$dbs_core['XN'] = array('region' => 'AM', 'currency' => 'XCD', 'weight' => 'LB_IN');
			$dbs_core['XS'] = array('region' => 'AP', 'currency' => 'SIS', 'weight' => 'KG_CM');
			$dbs_core['XY'] = array('region' => 'AM', 'currency' => 'ANG', 'weight' => 'LB_IN');
			$dbs_core['YE'] = array('region' => 'AP', 'currency' => 'YER', 'weight' => 'KG_CM');
			$dbs_core['YT'] = array('region' => 'AP', 'currency' => 'EUR', 'weight' => 'KG_CM');
			$dbs_core['ZA'] = array('region' => 'AP', 'currency' => 'ZAR', 'weight' => 'KG_CM');
			$dbs_core['ZM'] = array('region' => 'AP', 'currency' => 'ZMW', 'weight' => 'KG_CM');
			$dbs_core['ZW'] = array('region' => 'AP', 'currency' => 'USD', 'weight' => 'KG_CM');

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
			);
			$vendor_settings = array();

			if (isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes' && isset($general_settings['hits_dbs_v_rates']) && $general_settings['hits_dbs_v_rates'] == 'yes') {
				// Multi Vendor Enabled
				foreach ($pack_aft_hook['contents'] as $key => $value) {
					$product_id = $value['product_id'];
					$dbs_account = get_post_meta($product_id, 'hits_dbs_address', true);
					if (empty($dbs_account) || $dbs_account == 'default') {
						$dbs_account = 'default';
						if (!isset($vendor_settings[$dbs_account])) {
							$vendor_settings[$dbs_account] = $custom_settings['default'];
						}

						$vendor_settings[$dbs_account]['products'][] = $value;
					}

					if ($dbs_account != 'default') {
						$user_account = get_post_meta($dbs_account, 'hits_dbs_vendor_settings', true);
						$user_account = empty($user_account) ? array() : $user_account;
						if (!empty($user_account)) {
							if (!isset($vendor_settings[$dbs_account])) {

								$vendor_settings[$dbs_account] = $custom_settings['default'];

								if ($user_account['hits_dbs_site_id'] != '') {

									$vendor_settings[$dbs_account]['hits_dbs_site_id'] = $user_account['hits_dbs_site_id'];

								}

								if ($user_account['hits_dbs_address1'] != '' && $user_account['hits_dbs_city'] != '' && $user_account['hits_dbs_state'] != '' && $user_account['hits_dbs_zip'] != '' && $user_account['hits_dbs_country'] != '' && $user_account['hits_dbs_shipper_name'] != '') {

									if ($user_account['hits_dbs_shipper_name'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_shipper_name'] = $user_account['hits_dbs_shipper_name'];
									}

									if ($user_account['hits_dbs_company'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_company'] = $user_account['hits_dbs_company'];
									}

									if ($user_account['hits_dbs_mob_num'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_mob_num'] = $user_account['hits_dbs_mob_num'];
									}

									if ($user_account['hits_dbs_email'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_email'] = $user_account['hits_dbs_email'];
									}

									if ($user_account['hits_dbs_address1'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_address1'] = $user_account['hits_dbs_address1'];
									}

									$vendor_settings[$dbs_account]['hits_dbs_address2'] = $user_account['hits_dbs_address2'];

									if ($user_account['hits_dbs_city'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_city'] = $user_account['hits_dbs_city'];
									}

									if ($user_account['hits_dbs_state'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_state'] = $user_account['hits_dbs_state'];
									}

									if ($user_account['hits_dbs_zip'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_zip'] = $user_account['hits_dbs_zip'];
									}

									if ($user_account['hits_dbs_country'] != '') {
										$vendor_settings[$dbs_account]['hits_dbs_country'] = $user_account['hits_dbs_country'];
									}

									$vendor_settings[$dbs_account]['hits_dbs_gstin'] = $user_account['hits_dbs_gstin'];
									$vendor_settings[$dbs_account]['hits_dbs_con_rate'] = $user_account['hits_dbs_con_rate'];
								}
							}

							$vendor_settings[$dbs_account]['products'][] = $value;
						}
					}
				}
			}

			if (empty($vendor_settings)) {
				$custom_settings['default']['products'] = $pack_aft_hook['contents'];
			} else {
				$custom_settings = $vendor_settings;
			}

			$mesage_time = date('c');
			$message_date = date('Y-m-d');
			$weight_unit = $dim_unit = '';
			if (!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM') {
				$weight_unit = 'KG';
				$dim_unit = 'CM';
			} else {
				$weight_unit = 'LB';
				$dim_unit = 'IN';
			}

			if (!isset($general_settings['hits_dbs_packing_type'])) {
				return;
			}


			$woo_weight_unit = get_option('woocommerce_weight_unit');
			$woo_dimension_unit = get_option('woocommerce_dimension_unit');

			$dbs_mod_weight_unit = $dbs_mod_dim_unit = '';

			if (!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM') {
				$dbs_mod_weight_unit = 'kg';
				$dbs_mod_dim_unit = 'cm';
			} elseif (!empty($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'LB_IN') {
				$dbs_mod_weight_unit = 'lbs';
				$dbs_mod_dim_unit = 'in';
			} else {
				$dbs_mod_weight_unit = 'kg';
				$dbs_mod_dim_unit = 'cm';
			}

			$shipping_rates = array();
			if (isset($general_settings['hits_dbs_developer_rate']) && $general_settings['hits_dbs_developer_rate'] == 'yes') {
				echo "<pre>";
			}

			foreach ($custom_settings as $key => $value) {

			if (isset($general_settings['hits_dbs_auto_con_rate']) && $general_settings['hits_dbs_auto_con_rate'] == "yes") {
				$current_date = date('m-d-Y', time());
				$ex_rate_data = get_option('hits_dbs_ex_rate'.$key);
				$ex_rate_data = !empty($ex_rate_data) ? $ex_rate_data : array();
				if (empty($ex_rate_data) || (isset($ex_rate_data['date']) && $ex_rate_data['date'] != $current_date) ) {
					if (isset($general_settings['hits_dbs_country']) && !empty($general_settings['hits_dbs_country']) && isset($general_settings['hits_dbs_integration_key']) && !empty($general_settings['hits_dbs_integration_key'])) {
						$frm_curr = get_option('woocommerce_currency');
						$to_curr = isset($dbs_core[$general_settings['hits_dbs_country']]) ? $dbs_core[$general_settings['hits_dbs_country']]['currency'] : '';
						$ex_rate_Request = json_encode(array('integrated_key' => $general_settings['hits_dbs_integration_key'],
											'from_curr' => $frm_curr,
											'to_curr' => $to_curr));

						// $request_url = "http://localhost/hitshippo/get_exchange_rate.php";
						$request_url = "https://app.hitshipo.com/get_exchange_rate.php";
						$result = wp_remote_post($request_url, array(
							'method' => 'POST',
							'timeout' => 60,
							'sslverify' => 0,
							'headers'     => array(),
    						'cookies'     => array(),
							'body' => $ex_rate_Request,
							'sslverify'   => FALSE
						));

						$ex_rate_result = isset($result['body']) ? json_decode($result['body'], true) : '';

						if ( !empty($ex_rate_result) && isset($ex_rate_result['ex_rate']) && $ex_rate_result['ex_rate'] != "Not Found" ) {
							$ex_rate_result['date'] = $current_date;
							update_option('hits_dbs_ex_rate'.$key, $ex_rate_result);
						}else {
							if (!empty($ex_rate_data)) {
								$ex_rate_data['date'] = $current_date;
								update_option('hits_dbs_ex_rate'.$key, $ex_rate_data);
							}
						}
					}
				}
			}
				$to_city = $pack_aft_hook['destination']['city'];
				if (isset($general_settings['hits_dbs_translation']) && $general_settings['hits_dbs_translation'] == "yes" ) {
					if (isset($general_settings['hits_dbs_translation_key']) && !empty($general_settings['hits_dbs_translation_key'])) {
						include_once('classes/gtrans/vendor/autoload.php');
						if (!empty($to_city)) {
			                if (!preg_match('%^[ -~]+$%', $to_city))      //Cheks english or not  /[^A-Za-z0-9]+/ 
			                {
			                  $response =array();
			                  try{
			                    $translate = new TranslateClient(['key' => $general_settings['hits_dbs_translation_key']]);
			                    // Tranlate text
			                    $response = $translate->translate($to_city, [
			                        'target' => 'en',
			                    ]);
			                  }catch(exception $e){
			                    // echo "\n Exception Caught" . $e->getMessage(); //Error handling
			                  }
			                  if (!empty($response) && isset($response['text']) && !empty($response['text'])) {
			                    $to_city = $response['text'];
			                  }
			                }
			            }
					}
				}

				$shipping_rates[$key] = array();

				// $orgin_postalcode_or_city = $this->a2z_get_zipcode_or_city($value['hits_dbs_country'], $value['hits_dbs_city'], $value['hits_dbs_zip']);

				// $destination_postcode_city = $this->a2z_get_zipcode_or_city($pack_aft_hook['destination']['country'], $to_city, $pack_aft_hook['destination']['postcode']);

				$general_settings['hits_dbs_currency'] = isset($dbs_core[(isset($value['hits_dbs_country']) ? $value['hits_dbs_country'] : 'A2Z')]) ? $dbs_core[$value['hits_dbs_country']]['currency'] : '';

				// $dbs_packs = $this->hits_get_dbs_packages($value['products'], $general_settings, $general_settings['hits_dbs_currency']);

				$cart_total = 0;

				if (isset($pack_aft_hook['cart_subtotal'])) {
					$cart_total += $pack_aft_hook['cart_subtotal'];
				}else{
					foreach ($pack_aft_hook['contents'] as $item_id => $values) {
						$cart_total += (float) $values['line_subtotal'];
					}
				}

				$prod_total_weg = 0;
				foreach ($value['products'] as $item_id => $prod) {
					$product = $prod['data'];
					$product_data = $product->get_data();
					$get_prod = wc_get_product($prod['product_id']);
					if (!isset($product_data['weight']) || empty($product_data['weight'])) {
						if ($get_prod->is_type('variable')) {
							$parent_prod_data = $product->get_parent_data();
							if (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) {
								$product_data['weight'] = !empty($parent_prod_data['weight'] ? $parent_prod_data['weight'] : 0.001);
							} else {
								$product_data['weight'] = 0.001;
							}
						} else {
							$product_data['weight'] = 0.001;
						}
					}
					$prod_total_weg += ($product_data['weight'] * $prod['quantity']);
				}

				if ($general_settings['hits_dbs_currency'] != get_option('woocommerce_currency')) {
					if (isset($general_settings['hits_dbs_auto_con_rate']) && $general_settings['hits_dbs_auto_con_rate'] == "yes") {
						$get_ex_rate = get_option('hits_dbs_ex_rate'.$key, '');
						$get_ex_rate = !empty($get_ex_rate) ? $get_ex_rate : array();
						$exchange_rate = ( !empty($get_ex_rate) && isset($get_ex_rate['ex_rate']) ) ? $get_ex_rate['ex_rate'] : 0;
					}else{
						$exchange_rate = $value['hits_dbs_con_rate'];
					}

					if ($exchange_rate && $exchange_rate > 0) {
						$cart_total *= $exchange_rate;
					}
				}

				$order_total = 0;
				foreach ($pack_aft_hook['contents'] as $item_id => $values) {
					$order_total += (float) $values['line_subtotal'];
				}
				
				// $result = wp_remote_post($request_url, array(
				// 	'method' => 'POST',
				// 	'timeout' => 70,
				// 	'sslverify' => 0,
				// 	'body' => $xmlRequest
				// ));

				$carriers_n_rates = apply_filters('hits_dbs_flat_rates', [], $pack_aft_hook, $key, $prod_total_weg);

				if (isset($general_settings['hits_dbs_developer_rate']) && $general_settings['hits_dbs_developer_rate'] == 'yes') {
					echo "<h1> Request </h1><br/>";
					print_r("No Req/Res, it's not realtime rates.");
					echo "<br/><h1> Response </h1><br/>";
					print_r("No Req/Res, it's not realtime rates.");
				}

				if ($carriers_n_rates && !empty($carriers_n_rates)) {
					$rate = array();
					foreach ($carriers_n_rates as $carrier_n_rate) {
						$rate_code = $carrier_n_rate['code'];
						$rate_cost = $carrier_n_rate['cost'];

						if ($general_settings['hits_dbs_currency'] != get_option('woocommerce_currency')) {
							if (isset($general_settings['hits_dbs_auto_con_rate']) && $general_settings['hits_dbs_auto_con_rate'] == "yes") {
								$get_ex_rate = get_option('hits_dbs_ex_rate'.$key, '');
								$get_ex_rate = !empty($get_ex_rate) ? $get_ex_rate : array();
								$exchange_rate = ( !empty($get_ex_rate) && isset($get_ex_rate['ex_rate']) ) ? $get_ex_rate['ex_rate'] : 0;
							}else{
								$exchange_rate = $value['hits_dbs_con_rate'];
							}
								if ($exchange_rate && $exchange_rate > 0) {
									$rate_cost /= $exchange_rate;
								}
							
						}

						$rate[$rate_code] = $rate_cost;
						
					}

					$shipping_rates[$key] = $rate;
				}
			}

			if (isset($general_settings['hits_dbs_developer_rate']) && $general_settings['hits_dbs_developer_rate'] == 'yes') {
				die();
			}

			// Rate Processing



			if (!empty($shipping_rates)) {
				$i = 0;
				$final_price = array();
				foreach ($shipping_rates as $mkey => $rate) {
					$cheap_p = 0;
					$cheap_s = '';
					foreach ($rate as $key => $cvalue) {
						if ($i > 0) {

							if (!in_array($key, array('C', 'Q'))) {
								if ($cheap_p == 0 && $cheap_s == '') {
									$cheap_p = $cvalue;
									$cheap_s = $key;
								} else if ($cheap_p > $cvalue) {
									$cheap_p = $cvalue;
									$cheap_s = $key;
								}
							}
						} else {
							$final_price[] = array('price' => $cvalue, 'code' => $key, 'multi_v' => $mkey . '_' . $key);
						}
					}

					if ($cheap_p != 0 && $cheap_s != '') {
						foreach ($final_price as $key => $value) {
							$value['price'] = $value['price'] + $cheap_p;
							$value['multi_v'] = $value['multi_v'] . '|' . $mkey . '_' . $cheap_s;
							$final_price[$key] = $value;
						}
					}

					$i++;
				}

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

				foreach ($final_price as $key => $value) {

					$rate_cost = $value['price'];
					$rate_code = $value['code'];
					$multi_ven = $value['multi_v'];

					if (!empty($general_settings['hits_dbs_carrier_adj_percentage'][$rate_code])) {
						$rate_cost += $rate_cost * ($general_settings['hits_dbs_carrier_adj_percentage'][$rate_code] / 100);
					}
					if (!empty($general_settings['hits_dbs_carrier_adj'][$rate_code])) {
						$rate_cost += $general_settings['hits_dbs_carrier_adj'][$rate_code];
					}

					$rate_cost = round($rate_cost, 2);

					$carriers_available = isset($general_settings['hits_dbs_carrier']) && is_array($general_settings['hits_dbs_carrier']) ? $general_settings['hits_dbs_carrier'] : array();

					$carriers_name_available = isset($general_settings['hits_dbs_carrier_name']) && is_array($general_settings['hits_dbs_carrier']) ? $general_settings['hits_dbs_carrier_name'] : array();

					if (array_key_exists($rate_code, $carriers_available)) {
						$name = isset($carriers_name_available[$rate_code]) && !empty($carriers_name_available[$rate_code]) ? $carriers_name_available[$rate_code] : $_dbs_carriers[$rate_code];

						$rate_cost = apply_filters('hits_dbs_rate_cost', $rate_cost, $rate_code, $order_total, $pack_aft_hook['destination']['country']);
						if ($rate_cost < 1) {
							$name .= ' - Free';
						}

						$hide_service = apply_filters('hits_dbs_hide_service', $rate_cost, $rate_code, $order_total, $pack_aft_hook['destination']['country']);
						if (!empty($hide_service) && $hide_service == "hide") {
							continue;
						}

						if (!isset($general_settings['hits_dbs_v_rates']) || $general_settings['hits_dbs_v_rates'] != 'yes') {
							$multi_ven = '';
						}
										
				
						// This is where you'll add your rates
						$rate = array(
							'id'       => 'a2z' . $rate_code,
							'label'    => $name,
							'cost'     => apply_filters("hitstacks_shipping_cost_conversion", $rate_cost, $rate_code, $order_total, $pack_aft_hook['destination']['country']),
							'meta_data' => array('hits_dbs_multi_ven' => $multi_ven, 'hits_dbs_service' => $rate_code)
						);

						// Register the rate

						$this->add_rate($rate);
					}
				}
			}
		}

		public function hits_get_dbs_packages($package, $general_settings, $orderCurrency, $chk = false)
		{
			switch ($general_settings['hits_dbs_packing_type']) {
				case 'box':
					return $this->box_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
				case 'weight_based':
					return $this->weight_based_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
				case 'per_item':
				default:
					return $this->per_item_shipping($package, $general_settings, $orderCurrency, $chk);
					break;
			}
		}
		private function weight_based_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			// echo '<pre>';
			// print_r($package);
			// die();
			if (!class_exists('WeightPack')) {
				include_once 'classes/weight_pack/class-hit-weight-packing.php';
			}
			$max_weight = isset($general_settings['hits_dbs_max_weight']) && $general_settings['hits_dbs_max_weight'] != ''  ? $general_settings['hits_dbs_max_weight'] : 10;
			$weight_pack = new WeightPack('pack_ascending');
			$weight_pack->set_max_weight($max_weight);

			$package_total_weight = 0;
			$insured_value = 0;

			$ctr = 0;
			foreach ($package as $item_id => $values) {
				$ctr++;
				$product = $values['data'];
				$product_data = $product->get_data();

				$get_prod = wc_get_product($values['product_id']);

				if (!isset($product_data['weight']) || empty($product_data['weight'])) {

					if ($get_prod->is_type('variable')) {
						$parent_prod_data = $product->get_parent_data();

						if (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) {
							$product_data['weight'] = !empty($parent_prod_data['weight'] ? $parent_prod_data['weight'] : 0.001);
						} else {
							$product_data['weight'] = 0.001;
						}
					} else {
						$product_data['weight'] = 0.001;
					}
				}

				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				$weight_pack->add_item($product_data['weight'], $values, $chk_qty);
			}

			$pack   =   $weight_pack->pack_items();
			$errors =   $pack->get_errors();
			if (!empty($errors)) {
				//do nothing
				return;
			} else {
				$boxes    =   $pack->get_packed_boxes();
				$unpacked_items =   $pack->get_unpacked_items();

				$insured_value        =   0;

				$packages      =   array_merge($boxes, $unpacked_items); // merge items if unpacked are allowed
				$package_count  =   sizeof($packages);
				// get all items to pass if item info in box is not distinguished
				$packable_items =   $weight_pack->get_packable_items();
				$all_items    =   array();
				if (is_array($packable_items)) {
					foreach ($packable_items as $packable_item) {
						$all_items[]    =   $packable_item['data'];
					}
				}
				//pre($packable_items);
				$order_total = '';

				$to_ship  = array();
				$group_id = 1;
				foreach ($packages as $package) { //pre($package);
					$packed_products = array();
					if (($package_count  ==  1) && isset($order_total)) {
						$insured_value  =  (isset($product_data['product_price']) ? $product_data['product_price'] : $product_data['price']) * (isset($values['product_quantity']) ? $values['product_quantity'] : $values['quantity']);
					} else {
						$insured_value  =   0;
						if (!empty($package['items'])) {
							foreach ($package['items'] as $item) {

								$insured_value        =   $insured_value; //+ $item->price;
							}
						} else {
							if (isset($order_total) && $package_count) {
								$insured_value  =   $order_total / $package_count;
							}
						}
					}
					$packed_products    =   isset($package['items']) ? $package['items'] : $all_items;
					// Creating package request
					$package_total_weight   = $package['weight'];

					$insurance_array = array(
						'Amount' => $insured_value,
						'Currency' => $orderCurrency
					);

					$group = array(
						'GroupNumber' => $group_id,
						'GroupPackageCount' => 1,
						'Weight' => array(
							'Value' => round($package_total_weight, 3),
							'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'KG' : 'LBS'
						),
						'packed_products' => $packed_products,
					);
					$group['InsuredValue'] = $insurance_array;
					$group['packtype'] = 'BOX';

					$to_ship[] = $group;
					$group_id++;
				}
			}
			return $to_ship;
		}
		private function box_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			if (!class_exists('HIT_Boxpack')) {
				include_once 'classes/hit-box-packing.php';
			}
			$boxpack = new HIT_Boxpack();
			$boxes = isset($general_settings['hits_dbs_boxes']) ? $general_settings['hits_dbs_boxes'] : array();
			if (empty($boxes)) {
				return false;
			}
			// $boxes = unserialize($boxes);
			// Define boxes
			foreach ($boxes as $key => $box) {
				if (!$box['enabled']) {
					continue;
				}
				$box['pack_type'] = !empty($box['pack_type']) ? $box['pack_type'] : 'BOX';

				$newbox = $boxpack->add_box($box['length'], $box['width'], $box['height'], $box['box_weight'], $box['pack_type']);

				if (isset($box['id'])) {
					$newbox->set_id(current(explode(':', $box['id'])));
				}

				if ($box['max_weight']) {
					$newbox->set_max_weight($box['max_weight']);
				}

				if ($box['pack_type']) {
					$newbox->set_packtype($box['pack_type']);
				}
			}

			// Add items
			foreach ($package as $item_id => $values) {

				$product = $values['data'];
				$product_data = $product->get_data();
				$get_prod = wc_get_product($values['product_id']);
				$parent_prod_data = [];

				if ($get_prod->is_type('variable')) {
					$parent_prod_data = $product->get_parent_data();
				}

				if (isset($product_data['weight']) && !empty($product_data['weight'])) {
					$item_weight = round($product_data['weight'] > 0.001 ? $product_data['weight'] : 0.001, 3);
				} else {
					$item_weight = (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) ? (round($parent_prod_data['weight'] > 0.001 ? $parent_prod_data['weight'] : 0.001, 3)) : 0.001;
				}

				if (isset($product_data['width']) && isset($product_data['height']) && isset($product_data['length']) && !empty($product_data['width']) && !empty($product_data['height']) && !empty($product_data['length'])) {
					$item_dimension = array(
						'Length' => max(1, round($product_data['length'], 3)),
						'Width' => max(1, round($product_data['width'], 3)),
						'Height' => max(1, round($product_data['height'], 3))
					);
				} elseif (isset($parent_prod_data['width']) && isset($parent_prod_data['height']) && isset($parent_prod_data['length']) && !empty($parent_prod_data['width']) && !empty($parent_prod_data['height']) && !empty($parent_prod_data['length'])) {
					$item_dimension = array(
						'Length' => max(1, round($parent_prod_data['length'], 3)),
						'Width' => max(1, round($parent_prod_data['width'], 3)),
						'Height' => max(1, round($parent_prod_data['height'], 3))
					);
				}

				if (isset($item_weight) && isset($item_dimension)) {

					// $dimensions = array($values['depth'], $values['height'], $values['width']);
					$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];
					for ($i = 0; $i < $chk_qty; $i++) {
						$boxpack->add_item($item_dimension['Width'], $item_dimension['Height'], $item_dimension['Length'], $item_weight, round($product_data['price']), array(
							'data' => $values
						));
					}
				} else {
					
					return;
				}
			}

			// Pack it
			$boxpack->pack();
			$packages = $boxpack->get_packages();
			$to_ship = array();
			$group_id = 1;
			foreach ($packages as $package) {
				if ($package->unpacked === true) {
					//$this->debug('Unpacked Item');
				} else {
					//$this->debug('Packed ' . $package->id);
				}

				$dimensions = array($package->length, $package->width, $package->height);

				sort($dimensions);
				$insurance_array = array(
					'Amount' => round($package->value),
					'Currency' => $orderCurrency
				);


				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => round($package->weight, 3),
						'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'KG' : 'LBS'
					),
					'Dimensions' => array(
						'Length' => max(1, round($dimensions[2], 3)),
						'Width' => max(1, round($dimensions[1], 3)),
						'Height' => max(1, round($dimensions[0], 3)),
						'Units' => (isset($general_settings['weg_dim']) && $general_settings['weg_dim'] === 'yes') ? 'CM' : 'IN'
					),
					'InsuredValue' => $insurance_array,
					'packed_products' => array(),
					'package_id' => $package->id,
					'packtype' => 'BOX'
				);

				if (!empty($package->packed) && is_array($package->packed)) {
					foreach ($package->packed as $packed) {
						$group['packed_products'][] = $packed->get_meta('data');
					}
				}

				if (!$package->packed) {
					foreach ($package->unpacked as $unpacked) {
						$group['packed_products'][] = $unpacked->get_meta('data');
					}
				}

				$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
		private function per_item_shipping($package, $general_settings, $orderCurrency, $chk = false)
		{
			$to_ship = array();
			$group_id = 1;

			// Get weight of order
			foreach ($package as $item_id => $values) {
				$product = $values['data'];
				$product_data = $product->get_data();
				$get_prod = wc_get_product($values['product_id']);
				$parent_prod_data = [];

				if ($get_prod->is_type('variable')) {
					$parent_prod_data = $product->get_parent_data();
				}

				$group = array();
				$insurance_array = array(
					'Amount' => round($product_data['price']),
					'Currency' => $orderCurrency
				);

				if (isset($product_data['weight']) && !empty($product_data['weight'])) {
					$dbs_per_item_weight = round($product_data['weight'] > 0.001 ? $product_data['weight'] : 0.001, 3);
				} else {
					$dbs_per_item_weight = (isset($parent_prod_data['weight']) && !empty($parent_prod_data['weight'])) ? (round($parent_prod_data['weight'] > 0.001 ? $parent_prod_data['weight'] : 0.001, 3)) : 0.001;
				}

				$group = array(
					'GroupNumber' => $group_id,
					'GroupPackageCount' => 1,
					'Weight' => array(
						'Value' => $dbs_per_item_weight,
						'Units' => (isset($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM') ? 'KG' : 'LBS'
					),
					'packed_products' => $product
				);

				if (isset($product_data['width']) && isset($product_data['height']) && isset($product_data['length']) && !empty($product_data['width']) && !empty($product_data['height']) && !empty($product_data['length'])) {

					$group['Dimensions'] = array(
						'Length' => max(1, round($product_data['length'], 3)),
						'Width' => max(1, round($product_data['width'], 3)),
						'Height' => max(1, round($product_data['height'], 3)),
						'Units' => (isset($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM') ? 'CM' : 'IN'
					);
				} elseif (isset($parent_prod_data['width']) && isset($parent_prod_data['height']) && isset($parent_prod_data['length']) && !empty($parent_prod_data['width']) && !empty($parent_prod_data['height']) && !empty($parent_prod_data['length'])) {
					$group['Dimensions'] = array(
						'Length' => max(1, round($parent_prod_data['length'], 3)),
						'Width' => max(1, round($parent_prod_data['width'], 3)),
						'Height' => max(1, round($parent_prod_data['height'], 3)),
						'Units' => (isset($general_settings['hits_dbs_weight_unit']) && $general_settings['hits_dbs_weight_unit'] == 'KG_CM') ? 'CM' : 'IN'
					);
				}

				$group['packtype'] = 'BOX';

				$group['InsuredValue'] = $insurance_array;

				$chk_qty = $chk ? $values['product_quantity'] : $values['quantity'];

				for ($i = 0; $i < $chk_qty; $i++)
					$to_ship[] = $group;

				$group_id++;
			}

			return $to_ship;
		}
		private function a2z_get_zipcode_or_city($country, $city, $postcode)
		{
			$no_postcode_country = array(
				'AE', 'AF', 'AG', 'AI', 'AL', 'AN', 'AO', 'AW', 'BB', 'BF', 'BH', 'BI', 'BJ', 'BM', 'BO', 'BS', 'BT', 'BW', 'BZ', 'CD', 'CF', 'CG', 'CI', 'CK',
				'CL', 'CM', 'CR', 'CV', 'DJ', 'DM', 'DO', 'EC', 'EG', 'ER', 'ET', 'FJ', 'FK', 'GA', 'GD', 'GH', 'GI', 'GM', 'GN', 'GQ', 'GT', 'GW', 'GY', 'HK', 'HN', 'HT', 'IE', 'IQ', 'IR',
				'JM', 'JO', 'KE', 'KH', 'KI', 'KM', 'KN', 'KP', 'KW', 'KY', 'LA', 'LB', 'LC', 'LK', 'LR', 'LS', 'LY', 'ML', 'MM', 'MO', 'MR', 'MS', 'MT', 'MU', 'MW', 'MZ', 'NA', 'NE', 'NG', 'NI',
				'NP', 'NR', 'NU', 'OM', 'PA', 'PE', 'PF', 'PY', 'QA', 'RW', 'SA', 'SB', 'SC', 'SD', 'SL', 'SN', 'SO', 'SR', 'SS', 'ST', 'SV', 'SY', 'TC', 'TD', 'TG', 'TL', 'TO', 'TT', 'TV', 'TZ',
				'UG', 'UY', 'VC', 'VE', 'VG', 'VN', 'VU', 'WS', 'XA', 'XB', 'XC', 'XE', 'XL', 'XM', 'XN', 'XS', 'YE', 'ZM', 'ZW'
			);

			$postcode_city = !in_array($country, $no_postcode_country) ? $postcode_city = "<Postalcode>{$postcode}</Postalcode>" : '';
			if (!empty($city)) {
				$postcode_city .= "<City>{$city}</City>";
			}
			return $postcode_city;
		}
		/**
		 * Initialise Gateway Settings Form Fields
		 */
		public function init_form_fields()
		{
			$this->form_fields = array('hits_dbs' => array('type' => 'hits_dbs'));
		}
		public function generate_hits_dbs_html()
		{
			include('views/hitshipo_dbs_settings_view.php');
		}
	}
}
