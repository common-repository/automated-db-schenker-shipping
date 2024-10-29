<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$this->init_settings(); 
global $woocommerce, $wp_roles;

$_carriers = array(
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
$air_carriers = array('f' => 'Jet cargo first (Air)', 's' => 'Jetcargo special (Air)', 'b' => 'Jetcargo business (Air)', 'e' => 'Jetcargo economy (Air)', 'eagd' => 'Jetexpress gold (Air)', 'easv' => 'Jetexpress silver (Air)' );
$land_carriers = array('CON' => 'Concepts (Land)', 'DIR' => 'Directs (Land)', 'LPA' => 'Logistics Parcel (Land)', 'PAL' => 'Pallets (Land)', 'PRI' => 'Privpark (Land)', 'auc0' => 'System premium 10 (Land)', 'auc2' => 'System premium 13 (Land)', 'auc8' => 'System premium 8 (Land)', 'aucc' => 'System premium (Land)', 'auco' => 'System (Land)', 'ecsp' => 'System-plus (Land)', 'ect1' => 'Speed 10 (Land)', 'ect2' => 'Speed 12 (Land)', 'sch2' => 'Top 12 (Land)', 'schs' => 'System international (Land)', 'sysd' => 'System domestic (Land)', 'scht' => 'Top (Land)', 'schx' => 'System fix (Land)', 'ecpa' => 'Parcel (Land)', 'ect8' => 'Speed 8 (Land)', 'ectn' => 'Speed (Land)', '40' => 'System classic (Land)', '41' => 'System speed (Land)', '42' => 'System fixday (Land)', '43' => 'System (Land)', '44' => 'System Premium (Land)', '71' => 'Full load (Land)', '72' => 'Part load (Land)' );
$ocean_carriers = array('fcl' => 'Complete - FCL (Ocean)', 'lcl' => 'Combine - LCL (Ocean)',);
$print_size = array('A4'=>'A4','A6'=>'A6');
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
$payment_country = array('S' =>'Shipper','R' =>'Recipient', 'C' => 'Custom');
		$value = array();
		$value['AD'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AE'] = array('region' => 'AP', 'currency' =>'AED', 'weight' => 'KG_CM');
		$value['AF'] = array('region' => 'AP', 'currency' =>'AFN', 'weight' => 'KG_CM');
		$value['AG'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AI'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['AL'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AM'] = array('region' => 'AP', 'currency' =>'AMD', 'weight' => 'KG_CM');
		$value['AN'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'KG_CM');
		$value['AO'] = array('region' => 'AP', 'currency' =>'AOA', 'weight' => 'KG_CM');
		$value['AR'] = array('region' => 'AM', 'currency' =>'ARS', 'weight' => 'KG_CM');
		$value['AS'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['AT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['AU'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['AW'] = array('region' => 'AM', 'currency' =>'AWG', 'weight' => 'LB_IN');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['AZ'] = array('region' => 'AM', 'currency' =>'AZN', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['BA'] = array('region' => 'AP', 'currency' =>'BAM', 'weight' => 'KG_CM');
		$value['BB'] = array('region' => 'AM', 'currency' =>'BBD', 'weight' => 'LB_IN');
		$value['BD'] = array('region' => 'AP', 'currency' =>'BDT', 'weight' => 'KG_CM');
		$value['BE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['BF'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BG'] = array('region' => 'EU', 'currency' =>'BGN', 'weight' => 'KG_CM');
		$value['BH'] = array('region' => 'AP', 'currency' =>'BHD', 'weight' => 'KG_CM');
		$value['BI'] = array('region' => 'AP', 'currency' =>'BIF', 'weight' => 'KG_CM');
		$value['BJ'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['BM'] = array('region' => 'AM', 'currency' =>'BMD', 'weight' => 'LB_IN');
		$value['BN'] = array('region' => 'AP', 'currency' =>'BND', 'weight' => 'KG_CM');
		$value['BO'] = array('region' => 'AM', 'currency' =>'BOB', 'weight' => 'KG_CM');
		$value['BR'] = array('region' => 'AM', 'currency' =>'BRL', 'weight' => 'KG_CM');
		$value['BS'] = array('region' => 'AM', 'currency' =>'BSD', 'weight' => 'LB_IN');
		$value['BT'] = array('region' => 'AP', 'currency' =>'BTN', 'weight' => 'KG_CM');
		$value['BW'] = array('region' => 'AP', 'currency' =>'BWP', 'weight' => 'KG_CM');
		$value['BY'] = array('region' => 'AP', 'currency' =>'BYR', 'weight' => 'KG_CM');
		$value['BZ'] = array('region' => 'AM', 'currency' =>'BZD', 'weight' => 'KG_CM');
		$value['CA'] = array('region' => 'AM', 'currency' =>'CAD', 'weight' => 'LB_IN');
		$value['CF'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CG'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CH'] = array('region' => 'EU', 'currency' =>'CHF', 'weight' => 'KG_CM');
		$value['CI'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['CK'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['CL'] = array('region' => 'AM', 'currency' =>'CLP', 'weight' => 'KG_CM');
		$value['CM'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['CN'] = array('region' => 'AP', 'currency' =>'CNY', 'weight' => 'KG_CM');
		$value['CO'] = array('region' => 'AM', 'currency' =>'COP', 'weight' => 'KG_CM');
		$value['CR'] = array('region' => 'AM', 'currency' =>'CRC', 'weight' => 'KG_CM');
		$value['CU'] = array('region' => 'AM', 'currency' =>'CUC', 'weight' => 'KG_CM');
		$value['CV'] = array('region' => 'AP', 'currency' =>'CVE', 'weight' => 'KG_CM');
		$value['CY'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['CZ'] = array('region' => 'EU', 'currency' =>'CZK', 'weight' => 'KG_CM');
		$value['DE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['DJ'] = array('region' => 'EU', 'currency' =>'DJF', 'weight' => 'KG_CM');
		$value['DK'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['DM'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['DO'] = array('region' => 'AP', 'currency' =>'DOP', 'weight' => 'LB_IN');
		$value['DZ'] = array('region' => 'AM', 'currency' =>'DZD', 'weight' => 'KG_CM');
		$value['EC'] = array('region' => 'EU', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['EE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['EG'] = array('region' => 'AP', 'currency' =>'EGP', 'weight' => 'KG_CM');
		$value['ER'] = array('region' => 'EU', 'currency' =>'ERN', 'weight' => 'KG_CM');
		$value['ES'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ET'] = array('region' => 'AU', 'currency' =>'ETB', 'weight' => 'KG_CM');
		$value['FI'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['FJ'] = array('region' => 'AP', 'currency' =>'FJD', 'weight' => 'KG_CM');
		$value['FK'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['FM'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['FO'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['FR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GA'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GB'] = array('region' => 'EU', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GD'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['GE'] = array('region' => 'AM', 'currency' =>'GEL', 'weight' => 'KG_CM');
		$value['GF'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GG'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GH'] = array('region' => 'AP', 'currency' =>'GHS', 'weight' => 'KG_CM');
		$value['GI'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['GL'] = array('region' => 'AM', 'currency' =>'DKK', 'weight' => 'KG_CM');
		$value['GM'] = array('region' => 'AP', 'currency' =>'GMD', 'weight' => 'KG_CM');
		$value['GN'] = array('region' => 'AP', 'currency' =>'GNF', 'weight' => 'KG_CM');
		$value['GP'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GQ'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['GR'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['GT'] = array('region' => 'AM', 'currency' =>'GTQ', 'weight' => 'KG_CM');
		$value['GU'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['GW'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['GY'] = array('region' => 'AP', 'currency' =>'GYD', 'weight' => 'LB_IN');
		$value['HK'] = array('region' => 'AM', 'currency' =>'HKD', 'weight' => 'KG_CM');
		$value['HN'] = array('region' => 'AM', 'currency' =>'HNL', 'weight' => 'KG_CM');
		$value['HR'] = array('region' => 'AP', 'currency' =>'HRK', 'weight' => 'KG_CM');
		$value['HT'] = array('region' => 'AM', 'currency' =>'HTG', 'weight' => 'LB_IN');
		$value['HU'] = array('region' => 'EU', 'currency' =>'HUF', 'weight' => 'KG_CM');
		$value['IC'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ID'] = array('region' => 'AP', 'currency' =>'IDR', 'weight' => 'KG_CM');
		$value['IE'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['IL'] = array('region' => 'AP', 'currency' =>'ILS', 'weight' => 'KG_CM');
		$value['IN'] = array('region' => 'AP', 'currency' =>'INR', 'weight' => 'KG_CM');
		$value['IQ'] = array('region' => 'AP', 'currency' =>'IQD', 'weight' => 'KG_CM');
		$value['IR'] = array('region' => 'AP', 'currency' =>'IRR', 'weight' => 'KG_CM');
		$value['IS'] = array('region' => 'EU', 'currency' =>'ISK', 'weight' => 'KG_CM');
		$value['IT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['JE'] = array('region' => 'AM', 'currency' =>'GBP', 'weight' => 'KG_CM');
		$value['JM'] = array('region' => 'AM', 'currency' =>'JMD', 'weight' => 'KG_CM');
		$value['JO'] = array('region' => 'AP', 'currency' =>'JOD', 'weight' => 'KG_CM');
		$value['JP'] = array('region' => 'AP', 'currency' =>'JPY', 'weight' => 'KG_CM');
		$value['KE'] = array('region' => 'AP', 'currency' =>'KES', 'weight' => 'KG_CM');
		$value['KG'] = array('region' => 'AP', 'currency' =>'KGS', 'weight' => 'KG_CM');
		$value['KH'] = array('region' => 'AP', 'currency' =>'KHR', 'weight' => 'KG_CM');
		$value['KI'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['KM'] = array('region' => 'AP', 'currency' =>'KMF', 'weight' => 'KG_CM');
		$value['KN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['KP'] = array('region' => 'AP', 'currency' =>'KPW', 'weight' => 'LB_IN');
		$value['KR'] = array('region' => 'AP', 'currency' =>'KRW', 'weight' => 'KG_CM');
		$value['KV'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['KW'] = array('region' => 'AP', 'currency' =>'KWD', 'weight' => 'KG_CM');
		$value['KY'] = array('region' => 'AM', 'currency' =>'KYD', 'weight' => 'KG_CM');
		$value['KZ'] = array('region' => 'AP', 'currency' =>'KZF', 'weight' => 'LB_IN');
		$value['LA'] = array('region' => 'AP', 'currency' =>'LAK', 'weight' => 'KG_CM');
		$value['LB'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['LC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'KG_CM');
		$value['LI'] = array('region' => 'AM', 'currency' =>'CHF', 'weight' => 'LB_IN');
		$value['LK'] = array('region' => 'AP', 'currency' =>'LKR', 'weight' => 'KG_CM');
		$value['LR'] = array('region' => 'AP', 'currency' =>'LRD', 'weight' => 'KG_CM');
		$value['LS'] = array('region' => 'AP', 'currency' =>'LSL', 'weight' => 'KG_CM');
		$value['LT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LU'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LV'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['LY'] = array('region' => 'AP', 'currency' =>'LYD', 'weight' => 'KG_CM');
		$value['MA'] = array('region' => 'AP', 'currency' =>'MAD', 'weight' => 'KG_CM');
		$value['MC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MD'] = array('region' => 'AP', 'currency' =>'MDL', 'weight' => 'KG_CM');
		$value['ME'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MG'] = array('region' => 'AP', 'currency' =>'MGA', 'weight' => 'KG_CM');
		$value['MH'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MK'] = array('region' => 'AP', 'currency' =>'MKD', 'weight' => 'KG_CM');
		$value['ML'] = array('region' => 'AP', 'currency' =>'COF', 'weight' => 'KG_CM');
		$value['MM'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['MN'] = array('region' => 'AP', 'currency' =>'MNT', 'weight' => 'KG_CM');
		$value['MO'] = array('region' => 'AP', 'currency' =>'MOP', 'weight' => 'KG_CM');
		$value['MP'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['MQ'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MR'] = array('region' => 'AP', 'currency' =>'MRO', 'weight' => 'KG_CM');
		$value['MS'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['MT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['MU'] = array('region' => 'AP', 'currency' =>'MUR', 'weight' => 'KG_CM');
		$value['MV'] = array('region' => 'AP', 'currency' =>'MVR', 'weight' => 'KG_CM');
		$value['MW'] = array('region' => 'AP', 'currency' =>'MWK', 'weight' => 'KG_CM');
		$value['MX'] = array('region' => 'AM', 'currency' =>'MXN', 'weight' => 'KG_CM');
		$value['MY'] = array('region' => 'AP', 'currency' =>'MYR', 'weight' => 'KG_CM');
		$value['MZ'] = array('region' => 'AP', 'currency' =>'MZN', 'weight' => 'KG_CM');
		$value['NA'] = array('region' => 'AP', 'currency' =>'NAD', 'weight' => 'KG_CM');
		$value['NC'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['NE'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['NG'] = array('region' => 'AP', 'currency' =>'NGN', 'weight' => 'KG_CM');
		$value['NI'] = array('region' => 'AM', 'currency' =>'NIO', 'weight' => 'KG_CM');
		$value['NL'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['NO'] = array('region' => 'EU', 'currency' =>'NOK', 'weight' => 'KG_CM');
		$value['NP'] = array('region' => 'AP', 'currency' =>'NPR', 'weight' => 'KG_CM');
		$value['NR'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['NU'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['NZ'] = array('region' => 'AP', 'currency' =>'NZD', 'weight' => 'KG_CM');
		$value['OM'] = array('region' => 'AP', 'currency' =>'OMR', 'weight' => 'KG_CM');
		$value['PA'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PE'] = array('region' => 'AM', 'currency' =>'PEN', 'weight' => 'KG_CM');
		$value['PF'] = array('region' => 'AP', 'currency' =>'XPF', 'weight' => 'KG_CM');
		$value['PG'] = array('region' => 'AP', 'currency' =>'PGK', 'weight' => 'KG_CM');
		$value['PH'] = array('region' => 'AP', 'currency' =>'PHP', 'weight' => 'KG_CM');
		$value['PK'] = array('region' => 'AP', 'currency' =>'PKR', 'weight' => 'KG_CM');
		$value['PL'] = array('region' => 'EU', 'currency' =>'PLN', 'weight' => 'KG_CM');
		$value['PR'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['PT'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['PW'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['PY'] = array('region' => 'AM', 'currency' =>'PYG', 'weight' => 'KG_CM');
		$value['QA'] = array('region' => 'AP', 'currency' =>'QAR', 'weight' => 'KG_CM');
		$value['RE'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['RO'] = array('region' => 'EU', 'currency' =>'RON', 'weight' => 'KG_CM');
		$value['RS'] = array('region' => 'AP', 'currency' =>'RSD', 'weight' => 'KG_CM');
		$value['RU'] = array('region' => 'AP', 'currency' =>'RUB', 'weight' => 'KG_CM');
		$value['RW'] = array('region' => 'AP', 'currency' =>'RWF', 'weight' => 'KG_CM');
		$value['SA'] = array('region' => 'AP', 'currency' =>'SAR', 'weight' => 'KG_CM');
		$value['SB'] = array('region' => 'AP', 'currency' =>'SBD', 'weight' => 'KG_CM');
		$value['SC'] = array('region' => 'AP', 'currency' =>'SCR', 'weight' => 'KG_CM');
		$value['SD'] = array('region' => 'AP', 'currency' =>'SDG', 'weight' => 'KG_CM');
		$value['SE'] = array('region' => 'EU', 'currency' =>'SEK', 'weight' => 'KG_CM');
		$value['SG'] = array('region' => 'AP', 'currency' =>'SGD', 'weight' => 'KG_CM');
		$value['SH'] = array('region' => 'AP', 'currency' =>'SHP', 'weight' => 'KG_CM');
		$value['SI'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SK'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SL'] = array('region' => 'AP', 'currency' =>'SLL', 'weight' => 'KG_CM');
		$value['SM'] = array('region' => 'EU', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['SN'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['SO'] = array('region' => 'AM', 'currency' =>'SOS', 'weight' => 'KG_CM');
		$value['SR'] = array('region' => 'AM', 'currency' =>'SRD', 'weight' => 'KG_CM');
		$value['SS'] = array('region' => 'AP', 'currency' =>'SSP', 'weight' => 'KG_CM');
		$value['ST'] = array('region' => 'AP', 'currency' =>'STD', 'weight' => 'KG_CM');
		$value['SV'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['SY'] = array('region' => 'AP', 'currency' =>'SYP', 'weight' => 'KG_CM');
		$value['SZ'] = array('region' => 'AP', 'currency' =>'SZL', 'weight' => 'KG_CM');
		$value['TC'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['TD'] = array('region' => 'AP', 'currency' =>'XAF', 'weight' => 'KG_CM');
		$value['TG'] = array('region' => 'AP', 'currency' =>'XOF', 'weight' => 'KG_CM');
		$value['TH'] = array('region' => 'AP', 'currency' =>'THB', 'weight' => 'KG_CM');
		$value['TJ'] = array('region' => 'AP', 'currency' =>'TJS', 'weight' => 'KG_CM');
		$value['TL'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['TN'] = array('region' => 'AP', 'currency' =>'TND', 'weight' => 'KG_CM');
		$value['TO'] = array('region' => 'AP', 'currency' =>'TOP', 'weight' => 'KG_CM');
		$value['TR'] = array('region' => 'AP', 'currency' =>'TRY', 'weight' => 'KG_CM');
		$value['TT'] = array('region' => 'AM', 'currency' =>'TTD', 'weight' => 'LB_IN');
		$value['TV'] = array('region' => 'AP', 'currency' =>'AUD', 'weight' => 'KG_CM');
		$value['TW'] = array('region' => 'AP', 'currency' =>'TWD', 'weight' => 'KG_CM');
		$value['TZ'] = array('region' => 'AP', 'currency' =>'TZS', 'weight' => 'KG_CM');
		$value['UA'] = array('region' => 'AP', 'currency' =>'UAH', 'weight' => 'KG_CM');
		$value['UG'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
		$value['US'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['UY'] = array('region' => 'AM', 'currency' =>'UYU', 'weight' => 'KG_CM');
		$value['UZ'] = array('region' => 'AP', 'currency' =>'UZS', 'weight' => 'KG_CM');
		$value['VC'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['VE'] = array('region' => 'AM', 'currency' =>'VEF', 'weight' => 'KG_CM');
		$value['VG'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VI'] = array('region' => 'AM', 'currency' =>'USD', 'weight' => 'LB_IN');
		$value['VN'] = array('region' => 'AP', 'currency' =>'VND', 'weight' => 'KG_CM');
		$value['VU'] = array('region' => 'AP', 'currency' =>'VUV', 'weight' => 'KG_CM');
		$value['WS'] = array('region' => 'AP', 'currency' =>'WST', 'weight' => 'KG_CM');
		$value['XB'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XC'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XE'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['XM'] = array('region' => 'AM', 'currency' =>'EUR', 'weight' => 'LB_IN');
		$value['XN'] = array('region' => 'AM', 'currency' =>'XCD', 'weight' => 'LB_IN');
		$value['XS'] = array('region' => 'AP', 'currency' =>'SIS', 'weight' => 'KG_CM');
		$value['XY'] = array('region' => 'AM', 'currency' =>'ANG', 'weight' => 'LB_IN');
		$value['YE'] = array('region' => 'AP', 'currency' =>'YER', 'weight' => 'KG_CM');
		$value['YT'] = array('region' => 'AP', 'currency' =>'EUR', 'weight' => 'KG_CM');
		$value['ZA'] = array('region' => 'AP', 'currency' =>'ZAR', 'weight' => 'KG_CM');
		$value['ZM'] = array('region' => 'AP', 'currency' =>'ZMW', 'weight' => 'KG_CM');
		$value['ZW'] = array('region' => 'AP', 'currency' =>'USD', 'weight' => 'KG_CM');
	
	$packing_type = array("per_item" => "Pack Items Induviually", "weight_based" => "Weight Based Packing", "box" => "Box Packing");
	$location_type = array("PHYSICAL" => "PHYSICAL", "POSTAL" => "POSTAL");
	$person_type = array("PERSON" => "PERSON", "COMPANY" => "COMPANY");
	$incoterm_air = array("EXW" => "Ex Works", "FCA" => "Free Carrier", "CPT" => "Carriage Paid To", "CIP" => "Carriage Insurance Paid", "DAP" => "Delivered At Place", "DAT" => "Delivered At Terminal", "DDP" => "Delivered Duty Paid", "CFR" => "Cost and Freight");
	$incoterm_ocean = array("EXW" => "Ex Works", "FCA" => "Free Carrier", "CPT" => "Carriage Paid To", "CIP" => "Carriage Insurance Paid", "DAP" => "Delivered At Place", "DAT" => "Delivered At Terminal", "DDP" => "Delivered Duty Paid", "FAS" => "Free Alongside Ship", "FOB" => "Free On Board", "CFR" => "Cost and Freight");
	$incoterm_land = array("EXW" => "Ex Works", "FCA" => "Free Carrier", "DDP" => "Delivered Duty Paid", "DDU" => "Delivered Duty Unpaid", "DAT" => "Delivered At Terminal", "DAP" => "Delivered At Place", "DAF" => "Delivered At Frontier", "CPT" => "Carriage Paid To", "CIP" => "Carriage Insurance Paid", "DPU" => "Deliverd At Place Unloaded");
	$ship_dates_after = array("0" => "day on label generation", "1" => "1 day after creating label", "2" => "2 day after creating label", "3" => "3 day after creating label", "4" => "4 day after creating label", "5" => "5 day after creating label", "6" => "6 day after creating label", "7" => "7 day after creating label", "8" => "8 day after creating label", "9" => "9 day after creating label", "10" => "10 day after creating label", );
	$service_type_air = array("D2D" => "Door to Door", "D2A" => "Door to Airport", "A2D" => "Airport to Door", "A2A" => "Airport to Airport");
	$service_type_ocean = array("D2D" => "Door to Door", "D2P" => "Door to Port", "P2D" => "Port to Door", "P2P" => "Port to Port");
	$boxes = array();
	$package_type = array('BOX' => 'DBS Box','YP' => 'Your Pack');
	$weight_dim_unit = array("KG_CM" => "KG_CM", "LB_IN" => "LB_IN");
	$ship_pack_types = array("BX" => "Box(es)",
							"CI" => "Canister(s)",
							"CT" => "Carton(s)",
							"CS" => "Case(s)",
							"CO" => "Colli(s)",
							"CH" => "Crate(s)",
							"GP" => "Skeleton box pallet(s)",
							"NE" => "Unpacked Skid(s)",
							"BG" => "Bag(s)",
							"BL" => "Bale(s)",
							"DR" => "Barrel(s)",
							"BY" => "Bundle(s)",
							"TR" => "Drum(s)",
							"EP" => "Europallet(s)",
							"FR" => "Frame(s)",
							"HO" => "Hobbock(s)",
							"OP" => "One-way pallet(s)",
							"PK" => "Package(s)",
							"XP" => "Pallet(s)",
							"PZ" => "Pipe(s)",
							"RO" => "Roll(s)",
							"SK" => "Sack(s)",
							"ZZ" => "Other(s)",
						);
	$container_type = array("22BU20" => "BB (Bulk Container)",
							"22UP20" => "HT (Hard Top)",
							"20G220" => "OS (Open Sided Container)",
							"22UT20" => "OT (Open TopContainer)",
							"22PC20" => "PL (Platform Container)",
							"22RE20" => "RE (Reefer Container)",
							"22GP20" => "DC (Standard / Dry Container)",
							"22TN20" => "TC (Tank Container)",
							"42BU40" => "BB (Bulk Container)",
							"42P140" => "FR (Flat Rack Container)",
							"42UP40" => "HT (Hard Top)",
							"45GP40" => "HQ (High-Cube Standard / Dry Container)",
							"42PS40" => "OS (Open Sided Container)",
							"42UT40" => "OT (Open Top Container)",
							"42PL40" => "PL (Platform Container)",
							"42RE40" => "RE (Reefer Container)",
							"42GP40" => "DC (Standard / Dry Container)",
							"42TN40" => "TC (Tank Container)",
							"LEG045" => "HW (High Cube Palletwide)",
							"L5RE45" => "HR (Reefer High Cube Container)",
							"P6GP45" => "DC (Standard / Dry Container, High Cube)",
							"22DC20" => "GH (Garments on Hanger)",
							"25GP20" => "HQ (High Cube Container)",
							"22VH20" => "VC (Ventilated Container)",
							"40DC40" => "GH (Garments on Hanger)",
							"45BK40" => "HB (Bulk High Cube Container)",
							"45PC40" => "HF (Flat Rack High Cube Container)",
							"45UT40" => "HO (Open Top High Cube Container)",
							"45RE40" => "HR (Reefer High Cube Container)",
							"4EG040" => "HW (High Cube Palletwide)",
							"49PL40" => "MA (Mafi Trailer)",
							"42VH40" => "VC (Ventilated Container)",
							"L5VH45" => "HV (Ventilated High Cube Container)",
							"P6GP53" => "DC (Standard / Dry Container)",
							"22P120" => "FR (Flat Rack Container)"
						);
	$general_settings = get_option('hits_dbs_main_settings');
	$general_settings = empty($general_settings) ? array() : $general_settings;
	apply_filters("hits_dbs_del_cus_csv_rate", "");
	if(isset($_POST['save']))
	{	
		apply_filters("hits_dbs_save_cus_csv_rate", "");
		$boxes_id = isset($_POST['boxes_id']) ? sanitize_post($_POST['boxes_id']) : array();
		$boxes_name = isset($_POST['boxes_name']) ? sanitize_post($_POST['boxes_name']) : array();
		$boxes_length = isset($_POST['boxes_length']) ? sanitize_post($_POST['boxes_length']) : array();
		$boxes_width = isset($_POST['boxes_width']) ? sanitize_post($_POST['boxes_width']) : array();
		$boxes_height = isset($_POST['boxes_height']) ? sanitize_post($_POST['boxes_height']) : array();
		$boxes_box_weight = isset($_POST['boxes_box_weight']) ? sanitize_post($_POST['boxes_box_weight']) : array();
		$boxes_max_weight = isset($_POST['boxes_max_weight']) ? sanitize_post($_POST['boxes_max_weight']) : array();
		$boxes_enabled = isset($_POST['boxes_enabled']) ? sanitize_post($_POST['boxes_enabled']) : array();
		$boxes_pack_type = isset($_POST['boxes_pack_type']) ? sanitize_post($_POST['boxes_pack_type']) : array();

		$all_boxes = array();
		if (!empty($boxes_name)) {
			if (isset($boxes_name['filter'])) { //Using sanatize_post() it's adding filter type. Have to unset otherwise it will display as box
				unset($boxes_name['filter']);
			}
			if (isset($boxes_name['ID'])) {
				unset($boxes_name['ID']);
			}
			foreach ($boxes_name as $key => $value) {
				if (empty($value)) {
					continue;
				}
				$ind_box_id = $boxes_id[$key];
				$ind_box_name = empty($boxes_name[$key]) ? "New Box" : $boxes_name[$key];
				$ind_box_length = empty($boxes_length[$key]) ? 0 : $boxes_length[$key];
				$ind_boxes_width = empty($boxes_width[$key]) ? 0 : $boxes_width[$key];
				$ind_boxes_height = empty($boxes_height[$key]) ? 0 : $boxes_height[$key];
				$ind_boxes_box_weight = empty($boxes_box_weight[$key]) ? 0 : $boxes_box_weight[$key];
				$ind_boxes_max_weight = empty($boxes_max_weight[$key]) ? 0 : $boxes_max_weight[$key];
				$ind_box_enabled = isset($boxes_enabled[$key]) ? true : false;

				$all_boxes[$key] = array(
					'id' => $ind_box_id,
					'name' => $ind_box_name,
					'length' => $ind_box_length,
					'width' => $ind_boxes_width,
					'height' => $ind_boxes_height,
					'box_weight' => $ind_boxes_box_weight,
					'max_weight' => $ind_boxes_max_weight,
					'enabled' => $ind_box_enabled,
					'pack_type' => $boxes_pack_type[$key]
				);
			}
		}

		// echo '<pre>';print_r($all_boxes); die();

		$general_settings['hits_dbs_site_id'] = sanitize_text_field(isset($_POST['hits_dbs_site_id']) ? $_POST['hits_dbs_site_id'] : '');
		$general_settings['hits_dbs_site_pwd'] = sanitize_text_field(isset($_POST['hits_dbs_site_pwd']) ? $_POST['hits_dbs_site_pwd'] : '');
		$general_settings['hits_dbs_acc_no'] = sanitize_text_field(isset($_POST['hits_dbs_acc_no']) ? $_POST['hits_dbs_acc_no'] : '');
		$general_settings['hits_dbs_import_no'] = sanitize_text_field(isset($_POST['hits_dbs_import_no']) ? $_POST['hits_dbs_import_no'] : '');

		$general_settings['hits_dbs_test'] = sanitize_text_field(isset($_POST['hits_dbs_test']) ? 'yes' : 'no');
		$general_settings['hits_dbs_rates'] = sanitize_text_field(isset($_POST['hits_dbs_rates']) ? 'yes' : 'no');
		$general_settings['hits_dbs_etd_date'] = sanitize_text_field(isset($_POST['hits_dbs_etd_date']) ? 'yes' : 'no');
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
		$general_settings['hits_dbs_loc_type_sender'] = sanitize_text_field(isset($_POST['hits_dbs_loc_type_sender']) ? $_POST['hits_dbs_loc_type_sender'] : '');
		$general_settings['hits_dbs_loc_type_receiver'] = sanitize_text_field(isset($_POST['hits_dbs_loc_type_receiver']) ? $_POST['hits_dbs_loc_type_receiver'] : '');
		$general_settings['hits_dbs_con_per_type_sender'] = sanitize_text_field(isset($_POST['hits_dbs_con_per_type_sender']) ? $_POST['hits_dbs_con_per_type_sender'] : '');
		$general_settings['hits_dbs_con_per_type_receiver'] = sanitize_text_field(isset($_POST['hits_dbs_con_per_type_receiver']) ? $_POST['hits_dbs_con_per_type_receiver'] : '');
		$general_settings['hits_dbs_gstin'] = sanitize_text_field(isset($_POST['hits_dbs_gstin']) ? $_POST['hits_dbs_gstin'] : '');
		$general_settings['hits_dbs_carrier'] = !empty($_POST['hits_dbs_carrier']) ? sanitize_post($_POST['hits_dbs_carrier']) : array();
		$general_settings['hits_dbs_carrier_name'] = !empty($_POST['hits_dbs_carrier_name']) ? sanitize_post($_POST['hits_dbs_carrier_name']) : array();
		$general_settings['hits_dbs_carrier_adj'] = !empty($_POST['hits_dbs_carrier_adj']) ? sanitize_post($_POST['hits_dbs_carrier_adj']) : array();
		$general_settings['hits_dbs_carrier_adj_percentage'] = !empty($_POST['hits_dbs_carrier_adj_percentage']) ? sanitize_post($_POST['hits_dbs_carrier_adj_percentage']) : array();
		$general_settings['hits_dbs_account_rates'] = sanitize_text_field(isset($_POST['hits_dbs_account_rates']) ? 'yes' : 'no');
		$general_settings['hits_dbs_developer_rate'] = sanitize_text_field(isset($_POST['hits_dbs_developer_rate']) ? 'yes' :'no');
		$general_settings['hits_dbs_insure'] = sanitize_text_field(isset($_POST['hits_dbs_insure']) ? 'yes' :'no');
		$general_settings['hits_dbs_pay_con'] = sanitize_text_field(isset($_POST['hits_dbs_pay_con']) ? $_POST['hits_dbs_pay_con'] : '');
		$general_settings['hits_dbs_cus_pay_con'] = sanitize_text_field(isset($_POST['hits_dbs_cus_pay_con']) ? $_POST['hits_dbs_cus_pay_con'] : '');
		$general_settings['hits_dbs_exclude_countries'] = !empty($_POST['hits_dbs_exclude_countries']) ? sanitize_text_field($_POST['hits_dbs_exclude_countries']) : array();
		
		$general_settings['hits_dbs_translation'] = sanitize_text_field(isset($_POST['hits_dbs_translation']) ? 'yes' :'no');
		$general_settings['hits_dbs_translation_key'] = sanitize_text_field(isset($_POST['hits_dbs_translation_key']) ? $_POST['hits_dbs_translation_key'] : '');

		$general_settings['hits_dbs_trk_status_cus'] = sanitize_text_field(isset($_POST['hits_dbs_trk_status_cus']) ? 'yes' :'no');
		$general_settings['hits_dbs_email_alert'] = sanitize_text_field(isset($_POST['hits_dbs_email_alert']) ? 'yes' :'no');
		$general_settings['hits_dbs_cod'] = sanitize_text_field(isset($_POST['hits_dbs_cod']) ? 'yes' :'no');
		$general_settings['hits_dbs_label_automation'] = sanitize_text_field(isset($_POST['hits_dbs_label_automation']) ? 'yes' :'no');

		$general_settings['hits_dbs_packing_type'] = sanitize_text_field(isset($_POST['hits_dbs_packing_type']) ? $_POST['hits_dbs_packing_type'] : 'per_item');
		$general_settings['hits_dbs_max_weight'] = sanitize_text_field(isset($_POST['hits_dbs_max_weight']) ? $_POST['hits_dbs_max_weight'] : '100');
		$general_settings['hits_dbs_integration_key'] = sanitize_text_field(isset($_POST['hits_dbs_integration_key']) ? $_POST['hits_dbs_integration_key'] : '');
		$general_settings['hits_dbs_label_email'] = sanitize_text_field(isset($_POST['hits_dbs_label_email']) ? $_POST['hits_dbs_label_email'] : '');
		$general_settings['hits_dbs_ship_content'] = sanitize_text_field(isset($_POST['hits_dbs_ship_content']) ? $_POST['hits_dbs_ship_content'] : 'No shipment content');
		$general_settings['hits_dbs_print_size'] = sanitize_text_field(isset($_POST['hits_dbs_print_size']) ? $_POST['hits_dbs_print_size'] : 'A4');
		$general_settings['hits_dbs_incoterm_air'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_air']) ? $_POST['hits_dbs_incoterm_air'] : 'DAP');
		$general_settings['hits_dbs_incoterm_ocean'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_ocean']) ? $_POST['hits_dbs_incoterm_ocean'] : 'DAP');
		$general_settings['hits_dbs_incoterm_land'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_land']) ? $_POST['hits_dbs_incoterm_land'] : 'DAP');
		$general_settings['hits_dbs_incoterm_loc_air'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_loc_air']) ? $_POST['hits_dbs_incoterm_loc_air'] : '');
		$general_settings['hits_dbs_incoterm_loc_ocean'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_loc_ocean']) ? $_POST['hits_dbs_incoterm_loc_ocean'] : '');
		$general_settings['hits_dbs_incoterm_loc_land'] = sanitize_text_field(isset($_POST['hits_dbs_incoterm_loc_land']) ? $_POST['hits_dbs_incoterm_loc_land'] : '');
		$general_settings['hits_dbs_ser_type_air'] = sanitize_text_field(isset($_POST['hits_dbs_ser_type_air']) ? $_POST['hits_dbs_ser_type_air'] : 'D2D');
		$general_settings['hits_dbs_ser_type_ocean'] = sanitize_text_field(isset($_POST['hits_dbs_ser_type_ocean']) ? $_POST['hits_dbs_ser_type_ocean'] : 'D2D');
		$general_settings['hits_dbs_ship_pack_type'] = sanitize_text_field(isset($_POST['hits_dbs_ship_pack_type']) ? $_POST['hits_dbs_ship_pack_type'] : 'BX');
		$general_settings['hits_dbs_container_type'] = sanitize_text_field(isset($_POST['hits_dbs_container_type']) ? $_POST['hits_dbs_container_type'] : '');
		$general_settings['hits_dbs_food'] = sanitize_text_field(isset($_POST['hits_dbs_food']) ? 'yes' :'no');
		$general_settings['hits_dbs_heat'] = sanitize_text_field(isset($_POST['hits_dbs_heat']) ? 'yes' :'no');
		$general_settings['hits_dbs_weight_unit'] = sanitize_text_field(isset($_POST['hits_dbs_weight_unit']) ? $_POST['hits_dbs_weight_unit'] : 'KG_CM');
		$general_settings['hits_dbs_con_rate'] = sanitize_text_field(isset($_POST['hits_dbs_con_rate']) ? $_POST['hits_dbs_con_rate'] : '');
		$general_settings['hits_dbs_auto_con_rate'] = sanitize_text_field(isset($_POST['hits_dbs_auto_con_rate']) ? 'yes' : 'no');
		$general_settings['hits_dbs_bulk_service_dom'] = sanitize_text_field(isset($_POST['hits_dbs_bulk_service_dom']) ? $_POST['hits_dbs_bulk_service_dom'] : 'f');
		$general_settings['hits_dbs_bulk_service_intl'] = sanitize_text_field(isset($_POST['hits_dbs_bulk_service_intl']) ? $_POST['hits_dbs_bulk_service_intl'] : 'f');

		//Pickup
		$general_settings['hits_dbs_pic_ready_from'] = sanitize_text_field(isset($_POST['hits_dbs_pic_ready_from']) ? $_POST['hits_dbs_pic_ready_from'] : '');
		$general_settings['hits_dbs_pic_ready_to'] = sanitize_text_field(isset($_POST['hits_dbs_pic_ready_to']) ? $_POST['hits_dbs_pic_ready_to'] : '');
		$general_settings['hits_dbs_ves_arr'] = sanitize_text_field(isset($_POST['hits_dbs_ves_arr']) ? $_POST['hits_dbs_ves_arr'] : '');
		$general_settings['hits_dbs_ves_dep'] = sanitize_text_field(isset($_POST['hits_dbs_ves_dep']) ? $_POST['hits_dbs_ves_dep'] : '');

		// Multi Vendor Settings

		$general_settings['hits_dbs_v_enable'] = sanitize_text_field(isset($_POST['hits_dbs_v_enable']) ? 'yes' : 'no');
		$general_settings['hits_dbs_v_rates'] = sanitize_text_field(isset($_POST['hits_dbs_v_rates']) ? 'yes' : 'no');
		$general_settings['hits_dbs_v_labels'] = sanitize_text_field(isset($_POST['hits_dbs_v_labels']) ? 'yes' : 'no');
		$general_settings['hits_dbs_v_roles'] = !empty($_POST['hits_dbs_v_roles']) ? sanitize_post($_POST['hits_dbs_v_roles']) : array();
		$general_settings['hits_dbs_v_email'] = sanitize_text_field(isset($_POST['hits_dbs_v_email']) ? 'yes' : 'no');

		if (isset($general_settings['hits_dbs_v_roles']['ID'])) {
			unset($general_settings['hits_dbs_v_roles']['ID']);
		}
		if (isset($general_settings['hits_dbs_v_roles']['filter'])) {
			unset($general_settings['hits_dbs_v_roles']['filter']);
		}
		if (isset($general_settings['hits_dbs_carrier']['ID'])) {
			unset($general_settings['hits_dbs_carrier']['ID']);
		}
		if (isset($general_settings['hits_dbs_carrier']['filter'])) {
			unset($general_settings['hits_dbs_carrier']['filter']);
		}
		if (isset($general_settings['hits_dbs_carrier_name']['ID'])) {
			unset($general_settings['hits_dbs_carrier_name']['ID']);
		}
		if (isset($general_settings['hits_dbs_carrier_name']['filter'])) {
			unset($general_settings['hits_dbs_carrier_name']['filter']);
		}
		if (isset($general_settings['hits_dbs_carrier_adj']['ID'])) {
			unset($general_settings['hits_dbs_carrier_adj']['ID']);
		}
		if (isset($general_settings['hits_dbs_carrier_adj']['filter'])) {
			unset($general_settings['hits_dbs_carrier_adj']['filter']);
		}
		if (isset($general_settings['hits_dbs_carrier_adj_percentage']['ID'])) {
			unset($general_settings['hits_dbs_carrier_adj_percentage']['ID']);
		}
		if (isset($general_settings['hits_dbs_carrier_adj_percentage']['filter'])) {
			unset($general_settings['hits_dbs_carrier_adj_percentage']['filter']);
		}

		// boxes
		$general_settings['hits_dbs_boxes'] = !empty($all_boxes) ? $all_boxes : array();
		update_option('hits_dbs_main_settings', $general_settings);
		
	}
		$general_settings['hits_dbs_currency'] = isset($value[(isset($general_settings['hits_dbs_country']) ? $general_settings['hits_dbs_country'] : 'A2Z')]) ? $value[$general_settings['hits_dbs_country']]['currency'] : '';
		$general_settings['hits_dbs_woo_currency'] = get_option('woocommerce_currency');
		
?>
<style type="text/css">
	/*hit_tabs*/
.hit_tabs {
  max-width: 100%;
  min-width: 100%;
  margin-top: 20px;
  padding: 0 20px;
}
.hit_tabs input[type=radio] {
  display: none;
}
.hit_tabs label {
  display: inline-block;
  padding: 6px 0 6px 0;
  margin: 0 -2px;
  width: 11%; /* =100/hit_tabs number */
  border-bottom: 1px solid #dadada;
  text-align: center;
  font-weight:600;
}
.hit_tabs label:hover {
  cursor: pointer;
}
.hit_tabs input:checked + label {
  border: 1px solid #dadada;
  border-width: 1px 1px 0 1px;
}
.hit_tabs #tab1:checked ~ .content #content1,
.hit_tabs #tab2:checked ~ .content #content2,
.hit_tabs #tab3:checked ~ .content #content3,
.hit_tabs #tab4:checked ~ .content #content4,
.hit_tabs #tab5:checked ~ .content #content5,
.hit_tabs #tab6:checked ~ .content #content6,
.hit_tabs #tab7:checked ~ .content #content7,
.hit_tabs #tab8:checked ~ .content #content8,
.hit_tabs #tab8:checked ~ .content #content9{
  display: block;
}
.hit_tabs .content > div {
  display: none;
  padding-top: 20px;
  text-align: left;
  min-height: 240px;
  overflow: auto;
}
.woocommerce-save-button{margin-left:27px !important;}
</style>
<?php
if(!isset($general_settings['hits_dbs_site_id']) || $general_settings['hits_dbs_site_id'] == ''){
	?>
	<p style="    /* display: inline-block; */
    line-height: 1.4;
    padding: 11px 15px;
    font-size: 14px;
    text-align: left;
    margin: 25px 20px 0 2px;
    background-color: #fff;
    border-left: 4px solid #ffba00;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}"><?php _e('Required: Save DB Schenker Account Settings.','hitshipo_dbs') ?></p>

	<?php
}else if(!isset($general_settings['hits_dbs_shipper_name']) || $general_settings['hits_dbs_shipper_name'] == ''){
	?>
	<p style="    /* display: inline-block; */
    line-height: 1.4;
    padding: 11px 15px;
    font-size: 14px;
    text-align: left;
    margin: 25px 20px 0 2px;
    background-color: #fff;
    border-left: 4px solid #ffba00;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}"><?php _e('Required: Save Shipper Address.','hitshipo_dbs') ?></p>

	<?php
}else if(!isset($general_settings['hits_dbs_carrier']) || empty($general_settings['hits_dbs_carrier'])){
	?>
	<p style="    /* display: inline-block; */
    line-height: 1.4;
    padding: 11px 15px;
    font-size: 14px;
    text-align: left;
    margin: 25px 20px 0 2px;
    background-color: #fff;
    border-left: 4px solid #ffba00;
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}"><?php _e('Required: Choose serivices to continue. All domestic & international services are available','hitshipo_dbs') ?></p>

	<?php
}

$logo_url = str_replace("controllors/", "dbs.jpg", plugin_dir_url( __DIR__ ));
echo '<img src="'.$logo_url.'" alt="DBS" style="width:100px;float:right;margin-top: -100px;">';
?>
 
 <div class="hit_tabs">
   <input id="tab1" type="radio" name="hit_tabs" checked>
   <label for="tab1" >DB Schenker Account</label>
   <input id="tab2" type="radio" name="hit_tabs">
   <label for="tab2">Address</label>
   <input id="tab3" type="radio" name="hit_tabs">
   <label for="tab3">Shipping Rates</label>
   <input id="tab4" type="radio" name="hit_tabs">
   <label for="tab4">Services</label>
   <input id="tab5" type="radio" name="hit_tabs">
   <label for="tab5">Packing</label>
   <input id="tab6" type="radio" name="hit_tabs">
   <label for="tab6">Shipping Label</label>
   <input id="tab7" type="radio" name="hit_tabs">
   <label for="tab7">Pickup</label>
   <input id="tab8" type="radio" name="hit_tabs">
   <label for="tab8">Multi Vendor</label>
   <input id="tab9" type="radio" name="hit_tabs">
   <label for="tab9">Hooks</label>
   <div class="content">
      <div id="content1">
      	<h3><?php _e('DB Schenker Account Informations','hitshipo_dbs') ?></h3>
      		<div>
				<table style="width:100%;">

					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('DB Schenker Integration Team will give this to you.','hitshipo_dbs') ?>"></span>	<?php _e('DBS API Access Key','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" class="input-text regular-input" id="hits_dbs_site_id" name="hits_dbs_site_id" value="<?php echo (isset($general_settings['hits_dbs_site_id'])) ? $general_settings['hits_dbs_site_id'] : ''; ?>">
						</td>

					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this to Run the plugin in Test Mode','hitshipo_dbs') ?>"></span>	<?php _e('Is this Test Credentilas?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox"  name="hits_dbs_test" <?php echo (isset($general_settings['hits_dbs_test']) && $general_settings['hits_dbs_test'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('This will Update automatically.','hitshipo_dbs') ?>"></span>	<?php _e('Woocommerce Currency','hitshipo_dbs') ?><font style="color:red;">*</font></h4><p>You can change your Woocommerce currency <a href="admin.php?page=wc-settings">here</a>.</p>
						</td>
						<td>
							<h4><?php echo $general_settings['hits_dbs_woo_currency'];?></h4>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('This will automatically Update after Saving Settings.','hitshipo_dbs') ?>"></span>	<?php _e('DB Schenker Currency','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<h4><?php echo (isset($general_settings['hits_dbs_currency'])) ? $general_settings['hits_dbs_currency'] : '(Update After Save Action)'; ?></h4>
						</td>
					</tr>
					<tr class="auto_con">
						<td style=" width: 50%; ">
							<h4  style="display: inline;"> <span class="woocommerce-help-tip" data-tip="<?php _e('Convert currency from woocommerce currency to DB Schenker currency in front office.','hitshipo_dbs') ?>"></span>	<?php _e('Auto Currency Conversion ','hitshipo_dbs') ?></h4><font style="color:red;"><?php _e('( Only for Subscribed users )','hitshipo_dbs') ?></font>
						</td>
						<td>
							<input type="checkbox" id="auto_con" name="hits_dbs_auto_con_rate" <?php echo (isset($general_settings['hits_dbs_auto_con_rate']) && $general_settings['hits_dbs_auto_con_rate'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr class="con_rate">
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter conversion rate.','hitshipo_dbs') ?>"></span>	<?php _e('Exchange Rate','hitshipo_dbs') ?><font style="color:red;">*</font> <?php echo "( ".$general_settings['hits_dbs_woo_currency']."->".$general_settings['hits_dbs_currency']." )"; ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_con_rate" value="<?php echo (isset($general_settings['hits_dbs_con_rate'])) ? $general_settings['hits_dbs_con_rate'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('This will automatically Update after Saving Settings.','hitshipo_dbs') ?>"></span>	<?php _e('DB Schenker Weight Unit','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_weight_unit" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($weight_dim_unit as $key => $value)
								{
									if(isset($general_settings['hits_dbs_weight_unit']) && ($general_settings['hits_dbs_weight_unit'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				</table>

			</div>
      </div>

      <div id="content2">
      	<h3><?php _e('Shipper Address','hitshipo_dbs') ?></h3>
			<div>
				
				<table style="width:100%;">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipping Person Name','hitshipo_dbs') ?>"></span>	<?php _e('Shipper Name','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_shipper_name" id ="hits_dbs_shipper_name" value="<?php echo (isset($general_settings['hits_dbs_shipper_name'])) ? $general_settings['hits_dbs_shipper_name'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Company Name.','hitshipo_dbs') ?>"></span>	<?php _e('Company Name','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_company" id ="hits_dbs_company" value="<?php echo (isset($general_settings['hits_dbs_company'])) ? $general_settings['hits_dbs_company'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipper Mobile / Contact Number.','hitshipo_dbs') ?>"></span>	<?php _e('Contact Number','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_mob_num" id = "hits_dbs_mob_num" value="<?php echo (isset($general_settings['hits_dbs_mob_num'])) ? $general_settings['hits_dbs_mob_num'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Email Address of the Shipper.','hitshipo_dbs') ?>"></span>	<?php _e('Email Address','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_email" id ="hits_dbs_email" value="<?php echo (isset($general_settings['hits_dbs_email'])) ? $general_settings['hits_dbs_email'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 1 of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Address Line 1','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_address1" id="hits_dbs_address1" value="<?php echo (isset($general_settings['hits_dbs_address1'])) ? $general_settings['hits_dbs_address1'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Address Line 2 of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Address Line 2','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_address2" value="<?php echo (isset($general_settings['hits_dbs_address2'])) ? $general_settings['hits_dbs_address2'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('City of the Shipper from address.','hitshipo_dbs') ?>"></span>	<?php _e('City','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_city" id="hits_dbs_city" value="<?php echo (isset($general_settings['hits_dbs_city'])) ? $general_settings['hits_dbs_city'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('State of the Shipper from address.','hitshipo_dbs') ?>"></span>	<?php _e('State (Two Letter String)','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_state" value="<?php echo (isset($general_settings['hits_dbs_state'])) ? $general_settings['hits_dbs_state'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Postal/Zip Code.','hitshipo_dbs') ?>"></span>	<?php _e('Postal/Zip Code','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_zip" id="hits_dbs_zip" value="<?php echo (isset($general_settings['hits_dbs_zip'])) ? $general_settings['hits_dbs_zip'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Country of the Shipper from Address.','hitshipo_dbs') ?>"></span>	<?php _e('Country','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_country" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($countires as $key => $value)
								{
									if(isset($general_settings['hits_dbs_country']) && ($general_settings['hits_dbs_country'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Location type of the Shipper Address.','hitshipo_dbs') ?>"></span>	<?php _e('Location type (Shipper)','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_loc_type_sender" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($location_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_loc_type_sender']) && ($general_settings['hits_dbs_loc_type_sender'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Location type of yours customers (receivers).','hitshipo_dbs') ?>"></span>	<?php _e('Location type (Receivers)','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_loc_type_receiver" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($location_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_loc_type_receiver']) && ($general_settings['hits_dbs_loc_type_receiver'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Contact type of the Shipper Address.','hitshipo_dbs') ?>"></span>	<?php _e('Contact type (Shipper)','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_con_per_type_sender" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($person_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_con_per_type_sender']) && ($general_settings['hits_dbs_con_per_type_sender'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Contact type of yours customers (receivers).','hitshipo_dbs') ?>"></span>	<?php _e('Contact type (Receivers)','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_con_per_type_receiver" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($person_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_con_per_type_receiver']) && ($general_settings['hits_dbs_con_per_type_receiver'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('GSTIN/VAT No.','hitshipo_dbs') ?>"></span>	<?php _e('GSTIN/VAT No','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_gstin" value="<?php echo (isset($general_settings['hits_dbs_gstin'])) ? $general_settings['hits_dbs_gstin'] : ''; ?>">
						</td>
					</tr>
				</table>
			</div>
      </div>

      <div id="content3">
      	<h3><?php _e('DB Schenker Rate Section','hitshipo_dbs') ?></h3>
			<div>
				
				<table style="width:100%">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable Real time Rates to Show Rates in Checkout Page','hitshipo_dbs') ?>"></span>	<?php _e('Can I Show Rates?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_rates" <?php echo (isset($general_settings['hits_dbs_rates']) && $general_settings['hits_dbs_rates'] == 'yes') ? 'checked="true"' : (!isset($general_settings['hits_dbs_rates']) ? 'checked="true"' : '') ; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Rates Will not be shown to Selected Countries','hitshipo_dbs') ?>"></span>	<?php _e('Exclude Countries','hitshipo_dbs') ?></h4>
						</td>
						<td>

							<select name="hits_dbs_exclude_countries[]" multiple="true" class="wc-enhanced-select">

								<?php
								$general_settings['hits_dbs_exclude_countries'] = empty($general_settings['hits_dbs_exclude_countries'])? array() : $general_settings['hits_dbs_exclude_countries'];
								 foreach ($countires as $key => $county){
									if(in_array($key,$general_settings['hits_dbs_exclude_countries'])){
										echo "<option value=".$key." selected='true'>".$county."</option>";
									}else{
										echo "<option value=".$key.">".$county."</option>";	
									}
									
								}
							?>

							</select>
						</td>
					</tr>
					<!-- <tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable this option to Check the Request and Response','hitshipo_dbs') ?>"></span>	<?php _e('Plugin is not Working? (This option show the request and Response in cart / Checkout Page)','hitshipo_dbs') ?></h4>
						</td>
						<td >
							<input type="checkbox" name="hits_dbs_developer_rate" <?php echo (isset($general_settings['hits_dbs_developer_rate']) && $general_settings['hits_dbs_developer_rate'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr> -->
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Mail to the following Email address for Quick Support.','hitshipo_dbs') ?>"></span>	<?php _e('HITSshipo Support Email','hitshipo_dbs') ?><font style="color:red;"> *</font></h4>
						</td>
						<td>
							<a href="#" target="_blank">contact@hitstacks.com</a>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<?php echo apply_filters("hits_dbs_cus_csv_ui", "", $countires, $_carriers); ?>
						</td>
					</tr>
				</table>
			</div>
      </div>
     
      <div id="content4">
      	<h3><?php _e('DB Schenker Services (Change Name of the Services As you want)','hitshipo_dbs') ?></h3>
			<div>
				
				<table style="width:100%;">
				<tr>
					<td colspan="2" style=" width: 50%; ">
						<h4><?php _e('Why this?','hitshipo_dbs') ?><br/><?php _e('1) Enable Checkbox to Get the Service in Checkout Page','hitshipo_dbs') ?><br/><?php _e('2) Add New Name in the Textbox to Chnage the Core Service Name.','hitshipo_dbs') ?></h4>
					</td>
				</tr>
				<tr">
					<td>
						<h3 style="font-size: 1.10em;"><?php _e('Carries','hitshipo_dbs') ?></h3>
					</td>
					<td>
						<h3 style="font-size: 1.10em;"><?php _e('Alternate Name for Carrier','hitshipo_dbs') ?></h3>
					</td>
					<td>
						<h3 style="font-size: 1.10em;"><?php _e('Price adjustment','hitshipo_dbs') ?></h3>
					</td>
					<td>
						<h3 style="font-size: 1.10em;"><?php _e('Price adjustment (%)','hitshipo_dbs') ?></h3>
					</td>
				</tr>
						<?php foreach($_carriers as $key => $value)
						{
							echo '	<tr>
									<td>
									<input type="checkbox" value="yes" class="dbs_service" name="hits_dbs_carrier['.$key.']" '. ((isset($general_settings['hits_dbs_carrier'][$key]) && $general_settings['hits_dbs_carrier'][$key] == 'yes') ? 'checked="true"' : '') .' > <small>'.__($value,"hitshipo_dbs").' - [ '.$key.' ]</small>
									</td>
									<td>
										<input type="text" name="hits_dbs_carrier_name['.$key.']" value="'.((isset($general_settings['hits_dbs_carrier_name'][$key])) ? __($general_settings['hits_dbs_carrier_name'][$key],"hitshipo_dbs") : '').'">
									</td>
									<td>
										<input type="text" name="hits_dbs_carrier_adj['.$key.']" value="'.((isset($general_settings['hits_dbs_carrier_adj'][$key])) ? $general_settings['hits_dbs_carrier_adj'][$key] : '').'">
									</td>
									<td>
										<input type="text" name="hits_dbs_carrier_adj_percentage['.$key.']" value="'.((isset($general_settings['hits_dbs_carrier_adj_percentage'][$key])) ? $general_settings['hits_dbs_carrier_adj_percentage'][$key] : '').'">
									</td>
									</tr>';
						} ?>

					<tr>
						<td colspan="2" style="text-align: left;">
							<button type="button" id="checkAll" class="button">Select All</button>
							<button style="margin-left: 15px" type="button" id="uncheckAll" class="button">Unselect All</button>
						</td>
					</tr>
				</table>
			</div>
      </div>
      <div id="content5">
      	<h3><?php _e('Packing Section','hitshipo_dbs') ?></h3>
			<div>
				<table style="width:100%">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Integration key Created from HIT Shipo','hitshipo_dbs') ?>"></span>	<?php _e('Select Package Type','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_packing_type" id = "hits_dbs_packing_type" class="wc-enhanced-select" style="width:153px;" onchange="changepacktype(this)">
								<?php foreach($packing_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_packing_type']) && ($general_settings['hits_dbs_packing_type'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('To email address, the shipping label, Commercial invoice will sent.') ?>"></span>	<?php _e('What is the Maximum weight to one package?','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="number" name="hits_dbs_max_weight" placeholder="" value="<?php echo (isset($general_settings['hits_dbs_max_weight'])) ? $general_settings['hits_dbs_max_weight'] : ''; ?>">
						</td>
					</tr>
				</table>						
			</div>
			<div id="box_pack" style="width: 100%;">
				<h4 style="font-size: 16px;">Box packing configuration</h4><p>( Saved boxes are used when package type is "BOX". )</p>
				<table id="box_pack_t">
					<tr>
						<th style="padding:3px;"></th>
						<th style="padding:3px;"><?php _e('Name','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Length','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Width','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Height','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Box Weight','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Max Weight','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Enabled','hitshipo_dbs') ?><font style="color:red;">*</font></th>
						<th style="padding:3px;"><?php _e('Package Type','hitshipo_dbs') ?><font style="color:red;">*</font></th>
					</tr>
					<tbody id="box_pack_tbody">
						<?php

						$boxes = ( isset($general_settings['hits_dbs_boxes']) ) ? $general_settings['hits_dbs_boxes'] : $boxes;
							if (!empty($boxes)) {//echo '<pre>';print_r($general_settings['hits_dbs_boxes']);die();
								foreach ($boxes as $key => $box) {
									echo '<tr>
											<td class="check-column" style="padding:3px;"><input type="checkbox" /></td>
											<input type="hidden" size="1" name="boxes_id['.$key.']" value="'.$box["id"].'"/>
											<td style="padding:3px;"><input type="text" size="25" name="boxes_name['.$key.']" value="'.$box["name"].'" /></td>
											<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length['.$key.']" value="'.$box["length"].'" /></td>
											<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width['.$key.']" value="'.$box["width"].'" /></td>
											<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height['.$key.']" value="'.$box["height"].'" /></td>
											<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight['.$key.']" value="'.$box["box_weight"].'" /></td>
											<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight['.$key.']" value="'.$box["max_weight"].'" /></td>';
											if ($box['enabled'] == true) {
												echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.$key.']" checked/></center></td>';
											}else {
												echo '<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled['.$key.']" /></center></td>';
											}
											
									echo '<td style="padding:3px;"><select name="boxes_pack_type['.$key.']">';
										foreach ($package_type as $k => $v) {
											$selected = ($k==$box['pack_type']) ? "selected='true'" : '';
											echo '<option value="'.$k.'" ' .$selected. '>'.$v.'</option>';
										}
									echo '</select></td>
										</tr>';
								}
							}
						?>
						<tfoot>
						<tr>
							<th colspan="6">
								<a href="#" class="button button-secondary" id="add_box"><?php _e('Add Box','hitshipo_dbs') ?></a>
								<a href="#" class="button button-secondary" id="remove_box"><?php _e('Remove selected box(es)','hitshipo_dbs') ?></a>
							</th>
						</tr>
					</tfoot>
					</tbody>
				</table>
			</div>
      </div>
      <div id="content6">
      	<h3><?php _e('DB Schenker Shipping Label Section','hitshipo_dbs') ?></h3>
      	<p><?php _e('This is a premium service. <br/> DB Schenker Shipping label will created by our hitshipo only. You can create shipping labels automatically. There is no manual work need. <br/>Please register in hitshipo to create a shipping labels. <small style="color:green;">Trail available.</small><br> Checkout HIShipo Product Page:','hitshipo_dbs') ?> <a href="https://hitshipo.com/">https://hitshipo.com/</a></p>
			<div>
				
				<table style="width:100%">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Integration key Created from HIT Shipo','hitshipo_dbs') ?>"></span>	<?php _e('HIT-Shipo Integration Key','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_integration_key" id="hits_dbs_integration_key" placeholder="" value="<?php echo (isset($general_settings['hits_dbs_integration_key'])) ? $general_settings['hits_dbs_integration_key'] : ''; ?>"><br/>
							<a href="https://hitshipo.com/">Don't have a key? Signup for free</a>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('This will create a shipping label automatically, once the order is placed by DB Schenker services.','hitshipo_dbs') ?>"></span>	<?php _e('Create shipping label without any delay (Automated)','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_label_automation" <?php echo (isset($general_settings['hits_dbs_label_automation']) && $general_settings['hits_dbs_label_automation'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('To email address, the shipping label, Commercial invoice will sent.') ?>"></span>	<?php _e('To whom i want to sent the shipping label once created (email address).','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_label_email" placeholder="" value="<?php echo (isset($general_settings['hits_dbs_label_email'])) ? $general_settings['hits_dbs_label_email'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('It enables COD for orders','hitshipo_dbs') ?>"></span>	<?php _e('Cash on Delivery','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_cod" <?php echo (isset($general_settings['hits_dbs_cod']) && $general_settings['hits_dbs_cod'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('It shows DB Schenker tracking details in your customer\'s order page after creating the shipment (Front office)','hitshipo_dbs') ?>"></span>	<?php _e('Enable DB Schenker tracking informations to Customers','hitshipo_dbs') ?><span style="background-color: #fb0000; padding: 2px; color: #fff; margin-left: 5px;border-radius: 10px;">Comming Soon</span></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_trk_status_cus" <?php echo (isset($general_settings['hits_dbs_trk_status_cus']) && $general_settings['hits_dbs_trk_status_cus'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('It provides E-mail notification from DB Schenker to your customer\'s','hitshipo_dbs') ?>"></span>	<?php _e('Enable DB Schenker E-mail notification','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_email_alert" <?php echo (isset($general_settings['hits_dbs_email_alert']) && $general_settings['hits_dbs_email_alert'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enter some content for your shipments','hitshipo_dbs') ?>"></span>	<?php _e('Shipment Content','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<input type="text" name="hits_dbs_ship_content" placeholder="" value="<?php echo (isset($general_settings['hits_dbs_ship_content'])) ? $general_settings['hits_dbs_ship_content'] : ''; ?>">
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Shipping label format.','hitshipo_dbs') ?>"></span>	<?php _e('Shipping Label Format','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<b>PDF</b>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose the shipping label size.','hitshipo_dbs') ?>"></span>	<?php _e('Shipping Label Size','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_print_size" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($print_size as $key => $value)
								{
									if(isset($general_settings['hits_dbs_print_size']) && ($general_settings['hits_dbs_print_size'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm for Air Shipments.','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) - Air','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_incoterm_air" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($incoterm_air as $key => $value)
								{
									if(isset($general_settings['hits_dbs_incoterm_air']) && ($general_settings['hits_dbs_incoterm_air'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm for Ocean Shipments.','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) - Ocean','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_incoterm_ocean" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($incoterm_ocean as $key => $value)
								{
									if(isset($general_settings['hits_dbs_incoterm_ocean']) && ($general_settings['hits_dbs_incoterm_ocean'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm for Land Shipments.','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) - Land','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_incoterm_land" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($incoterm_land as $key => $value)
								{
									if(isset($general_settings['hits_dbs_incoterm_land']) && ($general_settings['hits_dbs_incoterm_land'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm Location for Air Shipments','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) Location - Air','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<textarea maxlength="250" name="hits_dbs_incoterm_loc_air" style="width: 300px;" placeholder=""><?php echo (isset($general_settings['hits_dbs_incoterm_loc_air'])) ? $general_settings['hits_dbs_incoterm_loc_air'] : ''; ?></textarea>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm Location for Ocean Shipments','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) Location - Ocean','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<textarea maxlength="250" name="hits_dbs_incoterm_loc_ocean" style="width: 300px;" placeholder=""><?php echo (isset($general_settings['hits_dbs_incoterm_loc_ocean'])) ? $general_settings['hits_dbs_incoterm_loc_ocean'] : ''; ?></textarea>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Incoterm Location for Land Shipments','hitshipo_dbs') ?>"></span>	<?php _e('International Commercial Terms (incoterm) Location - Land','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<textarea maxlength="250" name="hits_dbs_incoterm_loc_land" style="width: 300px;" placeholder=""><?php echo (isset($general_settings['hits_dbs_incoterm_loc_land'])) ? $general_settings['hits_dbs_incoterm_loc_land'] : ''; ?></textarea>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose service type for Air shipments.','hitshipo_dbs') ?>"></span>	<?php _e('Service type - Air','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_ser_type_air" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($service_type_air as $key => $value)
								{
									if(isset($general_settings['hits_dbs_ser_type_air']) && ($general_settings['hits_dbs_ser_type_air'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose service type for Air shipments.','hitshipo_dbs') ?>"></span>	<?php _e('Service type - Ocean','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_ser_type_ocean" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($service_type_ocean as $key => $value)
								{
									if(isset($general_settings['hits_dbs_ser_type_ocean']) && ($general_settings['hits_dbs_ser_type_ocean'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose pack type shipments.','hitshipo_dbs') ?>"></span>	<?php _e('Shipment pack type','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_ship_pack_type" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($ship_pack_types as $key => $value)
								{
									if(isset($general_settings['hits_dbs_ship_pack_type']) && ($general_settings['hits_dbs_ship_pack_type'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose container type (only mandatory for ocen shipments).','hitshipo_dbs') ?>"></span>	<?php _e('Shipment Container type - Ocean','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_container_type" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($container_type as $key => $value)
								{
									if(isset($general_settings['hits_dbs_container_type']) && ($general_settings['hits_dbs_container_type'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Are you shipping food releated products, then enable this.','hitshipo_dbs') ?>"></span>	<?php _e('Food Related','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_food" <?php echo (isset($general_settings['hits_dbs_food']) && $general_settings['hits_dbs_food'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Heated transport','hitshipo_dbs') ?>"></span>	<?php _e('Heated transport','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_heat" <?php echo (isset($general_settings['hits_dbs_heat']) && $general_settings['hits_dbs_heat'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose a service for domestic bulk labels.','hitshipo_dbs') ?>"></span>	<?php _e(' shipment carrier - Domestic','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_bulk_service_dom" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($_carriers as $key => $value)
								{
									if(isset($general_settings['hits_dbs_bulk_service_dom']) && ($general_settings['hits_dbs_bulk_service_dom'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Choose a service for international bulk labels.','hitshipo_dbs') ?>"></span>	<?php _e(' shipment carrier - International','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_bulk_service_intl" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($_carriers as $key => $value)
								{
									if(isset($general_settings['hits_dbs_bulk_service_intl']) && ($general_settings['hits_dbs_bulk_service_intl'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				</table>
			</div>
      </div>
      <div id="content7">
      	<h3><?php _e('DBS Pickup Section','hitshipo_dbs') ?></h3>
      	<p><?php _e('Pickup releated settings on shipping labels are seperated here.<br/>If you didn\'t registered in HITShipo, please register to create a shipping labels. <small style="color:green;">Trail available.</small><br> Checkout HIShipo Product Page:','hitshipo_dbs') ?> <a href="https://hitshipo.com/">https://hitshipo.com/</a></p>
			<div>
				<table style="width:100%">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('When the pickup ready.','hitshipo_dbs') ?>"></span>	<?php _e('Pickup available from','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_pic_ready_from" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($ship_dates_after as $key => $value)
								{
									if(isset($general_settings['hits_dbs_pic_ready_from']) && ($general_settings['hits_dbs_pic_ready_from'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Pickup available upto.','hitshipo_dbs') ?>"></span>	<?php _e('Pickup available upto','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_pic_ready_to" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($ship_dates_after as $key => $value)
								{
									if(isset($general_settings['hits_dbs_pic_ready_to']) && ($general_settings['hits_dbs_pic_ready_to'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Latest vessel arrival date / Posisioning date.','hitshipo_dbs') ?>"></span>	<?php _e('Latest vessel arrival date / Posisioning date','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_ves_arr" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($ship_dates_after as $key => $value)
								{
									if(isset($general_settings['hits_dbs_ves_arr']) && ($general_settings['hits_dbs_ves_arr'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Latest vessel departure date.','hitshipo_dbs') ?>"></span>	<?php _e('Latest vessel departure date','hitshipo_dbs') ?><font style="color:red;">*</font></h4>
						</td>
						<td>
							<select name="hits_dbs_ves_dep" class="wc-enhanced-select" style="width:153px;">
								<?php foreach($ship_dates_after as $key => $value)
								{
									if(isset($general_settings['hits_dbs_ves_dep']) && ($general_settings['hits_dbs_ves_dep'] == $key))
									{
										echo "<option value=".$key." selected='true'>".$value."</option>";
									}
									else
									{
										echo "<option value=".$key.">".$value."</option>";
									}
								} ?>
							</select>
						</td>
					</tr>
				</table>
			</div>
      </div>
      <div id="content8">
      	<h3><?php _e('Multi Vendor Support','hitshipo_dbs') ?></h3>
      	<p></p>
			<div>
				<table style="width:100%">
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Enable multi vendor to create shipping label from diffrent address.','hitshipo_dbs') ?>"></span>	<?php _e('Are you using Multi vendor?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_v_enable" <?php echo (isset($general_settings['hits_dbs_v_enable']) && $general_settings['hits_dbs_v_enable'] == 'yes') ? 'checked="true"' : ''; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('The shipping rates calculates from this address only. Suppose 2 vendors products in same cart. Then We will calculate the each vendor shipping cost then update to customers.','hitshipo_dbs') ?>"></span>	<?php _e('Do I wants to calculate the shipping rates based on vendor address?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_v_rates" <?php echo (isset($general_settings['hits_dbs_v_rates']) && $general_settings['hits_dbs_v_rates'] == 'yes') ? 'checked="true"' : '' ; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('The shipping Label created from vendor address to customer address.','hitshipo_dbs') ?>"></span>	<?php _e('Do I wants to create shipping labels based on vendor address?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_v_labels" <?php echo (isset($general_settings['hits_dbs_v_labels']) && $general_settings['hits_dbs_v_labels'] == 'yes') ? 'checked="true"' : '' ; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('The shipping Label created from vendor address to customer address.','hitshipo_dbs') ?>"></span>	<?php _e('What all are the user roles used for multi vendor?','hitshipo_dbs') ?></h4>
						</td>
						<td>

							<select name="hits_dbs_v_roles[]" multiple="true" class="wc-enhanced-select">

								<?php foreach (get_editable_roles() as $role_name => $role_info){
									if(isset($general_settings['hits_dbs_v_roles']) && in_array($role_name, $general_settings['hits_dbs_v_roles'])){
										echo "<option value=".$role_name." selected='true'>".$role_info['name']."</option>";
									}else{
										echo "<option value=".$role_name.">".$role_info['name']."</option>";	
									}
									
								}
							?>

							</select>
						</td>
					</tr>
					<tr>
						<td style=" width: 50%; ">
							<h4> <span class="woocommerce-help-tip" data-tip="<?php _e('Once shipping label is generated, Shipping Label will email to the vendor emails.','hitshipo_dbs') ?>"></span>	<?php _e('Do i wants to sent created shipping label to the vendor email?','hitshipo_dbs') ?></h4>
						</td>
						<td>
							<input type="checkbox" name="hits_dbs_v_email" <?php echo (isset($general_settings['hits_dbs_v_email']) && $general_settings['hits_dbs_v_email'] == 'yes') ? 'checked="true"' : '' ; ?> value="yes" > <?php _e('Yes','hitshipo_dbs') ?>
						</td>
					</tr>
				</table>
				
			</div>
      </div>
   </div>

</div>

<?php
// add_action('wp_head', 'hits_scripts' );
// function hits_scripts() {//echo 'string';die();
// // wp_deregister_script( 'jquery' );
//   wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js');
// }
// do_action('hits_js');
wp_enqueue_script('jquery');
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	var dbs_curr = '<?php echo $general_settings['hits_dbs_currency']; ?>';
	var woo_curr = '<?php echo $general_settings['hits_dbs_woo_currency']; ?>';
	// console.log(dbs_curr);
	// console.log(woo_curr);

	if (dbs_curr != null && dbs_curr == woo_curr) {
		jQuery('.con_rate').each(function(){
		jQuery('.con_rate').hide();
	    });
	}else{
		if(jQuery("#auto_con").prop('checked') == true){
			jQuery('.con_rate').hide();
		}else{
			jQuery('.con_rate').each(function(){
			jQuery('.con_rate').show();
		    });
		}
	}

	jQuery('#add_box').click( function() {
		var pack_type_options = '<option value="BOX">DB Schenker Box</option><option value="FLY">Flyer</option><option value="YP" selected="selected" >Your Pack</option>';
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');
		var size = tbody.find('tr').size();
		var code = '<tr class="new">\
			<td  style="padding:3px;" class="check-column"><input type="checkbox" /></td>\
			<input type="hidden" size="1" name="boxes_id[' + size + ']" value="box_id_' + size + '"/>\
			<td style="padding:3px;"><input type="text" size="25" name="boxes_name[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_length[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_width[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_height[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_box_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><input type="text" style="width:100%;" name="boxes_max_weight[' + size + ']" /></td>\
			<td style="padding:3px;"><center><input type="checkbox" name="boxes_enabled[' + size + ']" /></center></td>\
			<td style="padding:3px;"><select name="boxes_pack_type[' + size + ']" >' + pack_type_options + '</select></td>\
	        </tr>';
		tbody.append( code );
		return false;
	});

	jQuery('#remove_box').click(function() {
		var tbody = jQuery('#box_pack_t').find('#box_pack_tbody');console.log(tbody);
		tbody.find('.check-column input:checked').each(function() {
			jQuery(this).closest('tr').remove().find('input').val('');
		});
		return false;
	});

	var payment_cun = "<?php echo $general_settings['hits_dbs_pay_con']; ?>";
	if (payment_cun != null && payment_cun == 'C') {
		jQuery('#cus_pay_con').show();
	}else{
		jQuery('#cus_pay_con').hide();
	}

	var translation = "<?php echo ( isset($general_settings['hits_dbs_translation']) && !empty($general_settings['hits_dbs_translation']) ) ? $general_settings['hits_dbs_translation'] : ''; ?>";
	if (translation != null && translation == "yes") {
		jQuery('#translation_key').show();
	}else{
		jQuery('#translation_key').hide();
	}

	jQuery('#hits_dbs_translation').click(function() {
		if (jQuery(this).is(":checked")) {
			jQuery('#translation_key').show();
		}else{
			jQuery('#translation_key').hide();
		}
	});

	if('#checkAll'){
    	jQuery('#checkAll').on('click',function(){
            jQuery('.dbs_service').each(function(){
                this.checked = true;
            });
    	});
    }
    if('#uncheckAll'){
    jQuery('#uncheckAll').on('click',function(){
            jQuery('.dbs_service').each(function(){
                this.checked = false;
            });
    	});
	}
	jQuery('.woocommerce-save-button').click(function() {
		var side_id = jQuery('#hits_dbs_site_id').val();
		var shipper_name = jQuery('#hits_dbs_shipper_name').val();
		var shipper_company = jQuery('#hits_dbs_company').val();
		var shipper_number = jQuery('#hits_dbs_mob_num').val();
		var shipper_address1 = jQuery('#hits_dbs_address1').val();
		var shipper_city = jQuery('#hits_dbs_city').val();
		var shipper_zip = jQuery('#hits_dbs_zip').val();
		var shipper_integration_key = jQuery('#hits_dbs_integration_key').val();	
		var shipper_email = jQuery('#hits_dbs_email').val();		
			if(side_id == ''){
				alert('API Access Key is empty');
				return false;
			}	
			if(shipper_name == ''){
				alert('Shipper Name is empty');
				return false;
			}	
			if(shipper_company == ''){
				alert('Company Name is empty');
				return false;
			}	
			if(shipper_number == ''){
				alert(' Contact Number is empty');
				return false;
			}	
			if(shipper_email == ''){
				alert(' Email Address is empty');
				return false;
			}	
			if(shipper_address1 == ''){
				alert('Address Line 1 is empty');
				return false;
			}	
			if(shipper_city == ''){
				alert('City is empty');
				return false;
			}
			if(shipper_zip == ''){
				alert('Postal/Zip Code is empty');
				return false;
			}
			if(shipper_integration_key == ''){
				alert('HIT-Shipo Integration Key Code is empty');
				return false;
			}
	});
});
function changepacktype(selectbox){
	var box = document.getElementById("box_pack");
	var box_type = selectbox.value;
	if (box_type == "box") {
	    box.style.display = "block";
	  } else {
	    box.style.display = "none";
	  }
		// alert(box_type);
}
var box_type = document.getElementById("hits_dbs_packing_type").value;
var box = document.getElementById("box_pack");
if (box_type != "box") {
	box.style.display = "none";
}

jQuery("#auto_con").change(function() {
    if(this.checked) {
        jQuery('.con_rate').hide();
    }else{
    	jQuery('.con_rate').show();
    }
});

function changepaycon(selectbox){
	// var payment_cun = document.getElementById("hits_dbs_pay_con");
	var sel_pay_cun = selectbox.value;
	var cus_pay = document.getElementById("cus_pay_con");
	if (sel_pay_cun == "C") {
	    cus_pay.style.display = "table-cell";
	  } else {
	    cus_pay.style.display = "none";
	  }
		// alert(box_type);
}

</script>