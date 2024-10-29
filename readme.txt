=== Automated DB Schenker Shipping - HPOS supported ===
Contributors: HITShipo,a2zplugins, hitstacks
Tags: DB Schenker, DBS, automated, DBS shipping, DBS Woocommerce, DB Schenker Woocommerce, dbs label, dbs manual rates, hitshipo plugin, hitstacks, dbs shipping rates, dbs manual label, dbs automated, dbs price shipping, db schenker,dbs for woocommerce,dbs for ecommerce. 
Requires at least: 4.0.1
Tested up to: 8.2
Requires PHP: 5.6
Stable tag: 1.3.2
License: GPLv3 or later License
URI: http://www.gnu.org/licenses/gpl-3.0.html

(Fully automated) Manual shipping rates, shipping label, return label, pickup, invoice, multi vendor,etc. supports all countries. 

== Description ==

[DB Schenker shipping](https://wordpress.org/plugins/automated-db-schenker-shipping/) plugin, integrate the [DB Schenker](https://eschenker-fat.dbschenker.com) web service for delivery in Domestic and Internationally. According to the destination, We are providing all kind of DBS Services. Includes three DB Schenker's web service product on booking - Land, Air, and Ocean. 

Annoyed of clicking button to create shipping label and generating it here is a hassle free solution, [HITShipo](https://hitshipo.com/) is the tool with fully automated will reduce your cost and will save your time. 

= FRONT OFFICE (CHECKOUT PAGE): =

To fetch manual rates on the checkout page, we will send product information and location to DB Schenker.

We are providing the following domestic & international shipping carriers of DB Schenker:
 * Jetcargo economy (Air)
 * Complete -FCL (Ocean)
 * Combine -LCL (Ocean)
 * Logistics Parcel (Land) 

and more 32 Services via Air, Ocean and Land.

By using hooks and filters you can make currency conversions.

= BACK OFFICE (SHIPPING ): =

[DB Schenker shipping](https://wordpress.org/plugins/) plugin is deeply integrated with [HITShipo](https://hitshipo.com). So the shipping labels will be generated automatically. You can get the shipping label through email or from the order page.

 This plugin also supported the manual shipments option. By using this you can create the shipments directly from the order page. [HITShipo](https://hitshipo.com) will keep track of the orders and update the order state to complete.

= Our Guarantees =

* Support warranty on plugin's bugs.
* We can customize the plugin or we can make necessary modifications. For customisation please contact our support or mail to contact@hitstacks.com.

= Useful filters =

1) Flat Rate based on order total for services

> function hitstacks_dbs_rate_cost_fnc($rate_cost, $rate_code, $order_total, $order_country){
>	if($order_total > 250){
>		return 0;
>	}
>	return 20; // Return currency is must to be a DBS configured currency.
> }
> add_filter("hitstacks_shipping_cost_conversion", "hitstacks_dbs_rate_cost_fnc", 10,4);

2) Hide any service

> function hitstacks_dbs_hide($rate_cost, $rate_code, $order_total, $order_country){
>	if($order_country == "US" && $rate_code == "auco"){
>		return "hide";
>	}
> }
> add_filter("hits_dbs_hide_service", "hitstacks_dbs_hide", 10,4);

3) Show flat rates

> add_filter('hits_dbs_flat_rates', 'hits_dbs_flat_rates_fun', 10, 2);
> function hits_dbs_flat_rates_fun($rates=[], $order_info=[]){
> 	$rates[] = ["code"=>"f", "cost"=>100];
> 	return $rates;
> }

(Note: While copy paste the code from worpress plugin page may throw error “Undefined constant”. It can be fixed by replacing backtick (`) to apostrophe (‘) inside add_filter()))

= Your customer will appreciate : =

* The Product is delivered very quickly. The reason is, there this no delay between the order and shipping label action.
* Access to the many services of DBS for domestic & international shipping.
* Good impression of the shop.


= Informations for Configure plugin =

> If you have already a DBS Account, please contact your DBS account manager to get your credentials.
> If you are not registered yet, please contact DBS customer service.
> Functions of the module are available only after receiving your API’s credentials.
> Create account in HITShipo.
> Get the integration key.
> Configure the plugin.

Plugin Tags: <blockquote>DB Schenker, DBS, DB Schenker shipping, DBS shipping, DB Schenker Woocommerce, DBS Woocommerce,  DB Schenker for woocommerce, DBS for woocommerce, official DB Schenker, official dbs, dbs plugin, DB Schenker plugin, create shipment</blockquote>


= About DB Schenker =
DB Schenker is a division of German rail operator Deutsche Bahn AG that focuses on logistics. The company was acquired by Deutsche Bahn as Schenker-Stinnes in 2002. It comprises divisions for air, land, sea freight, and Contract Logistics.


= About HITShipo =

We are Web Development Company. We are planning for make everything automated. 

= What HITShipo Tell to Customers? =

> "Configure & take rest"

== Screenshots ==
1. Configuration - DBS Details.
2. Configuration - DBS Shipper Address.
3. Configuration - DBS Rate Section.
4. Configuration - DBS Services Available.
5. Configuration - Packing algorithm Settings.
6. Configuration - Shipping label Settings.
7. Configuration - Pickup Settings.
8. Configuration - Multi-vendor Settings.
9. Output - DBS Shipping Rates in Shop.
10. Output - Edit Order Page (label) Shipping Section.
11. Integration - HITShipo integration plans.
12. Why HIT Shipo?.


== Changelog ==

= 1.3.2 =
*Release Date - 16 April 2024*
	> Added HPOS support.

= 1.3.1 =
*Release Date - 10 November 2023*
	> Minor fixes and handlings.

= 1.3.0 =
*Release Date - 12 October 2023*
	> Added flat rates with CSV through filter

= 1.2.10 =
*Release Date - 19 April 2023*
	>  Fix fatal error when passing empty value for weight and dimension conversion on PHP 8

= 1.2.9 =
*Release Date - 21 March 2022*
	> minor bug 

= 1.2.8 =
*Release Date - 21 March 2022*
	> minor bug fix

= 1.2.7 =
*Release Date - 17 November 2022*
	> minor bug fix

= 1.2.6 =
*Release Date - 17 November 2022*
	> update tested version

= 1.2.5 =
*Release Date - 31 October 2022*
	> minor improvement

= 1.2.4 =
*Release Date - 31 October 2022*
	> minor bug fix

= 1.2.3 =
*Release Date - 29 september 2022*
	> minor improvement

= 1.2.2 =
*Release Date - 21 september 2022*
	> minor improvement

= 1.2.1 =
*Release Date - 21 July 2022*
	> minor bug fix

= 1.2.0 =
*Release Date - 20 July2022*
	> shipping label automated

= 1.1.0 =
*Release Date - 06 June 2022*
	> Wordpress minor update


= 1.0.3 =
*Release Date - 18 July 2021*
	> Wordpress Version updated

= 1.0.2 =
*Release Date - 05 January 2020*
	> Minor bug fixes

= 1.0.1 =
*Release Date - 19 December 2020*
	> Added Banner and Icon
	
= 1.0.0 =
*Release Date - 11 December 2020*
	> Initial Version
