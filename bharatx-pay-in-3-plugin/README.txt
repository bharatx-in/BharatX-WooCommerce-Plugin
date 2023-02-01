=== Plugin Name ===
Contributors: bharatxdev
Tags: bharatx, payments, paylater, installments
Requires at least: 5.3.2
Version: 1.6.1
Tested up to: 5.9
Stable tag: 1.6.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BharatX Pay In 3 plugin is a payment gateway plugin which allows you to accept payments in 3 easy instalments.

== Description ==  

BharatX Pay in 3 is a white-labeled Pay in 3 parts plugin 
which allows your customers to split their payments into
3 parts, one tap, with any documentation. 
The user gets access to “Buy now pay later”, and has to
pay 1/3rd of the amount at the time of placing the order 
and the rest after 30 days and 60 days, with no extra charge.
The payment gateway would be a standalone feature at the checkout
page and can be customised as per your needs. 
For example, it can also be called “YourBrandName Pay in 3", 
helping you to build/strengthen your brand name further, 
and building customers trust and engagement with your website. 
After the customer chooses Pay in 3 on the checkout page, 
the customer goes through a one minute sign-in process via 
a popup and after the payment is completed, is redirected 
to the payment confirmation page, making it a seamless 
process with low drop-off due to the ease of the flow.


### Key Features

* Integrate a white-labelled Pay in 3 feature at the checkout page 
* Allow customers to pay in 3 easy instalments
* Includes a widget at the product page displaying the Pay in 3- features.
* 0 Documentation, no hidden fees and charges
* 1 minute hassle-free seamless setup
* Works like a payment gateway. Integrate it like a payment gateway. Receive funds like a payment gateway on T+1, with no business risk.

== Installation ==

= Automatic installation =

1. Log into your WordPress admin
2. Click __Plugins__
3. Click __Add New__
4. Search for __BharatX Pay In 3 Feature__
5. Click __Install Now__ under "BharatX Pay In 3 Feature"
6. Activate the plugin

= Manual installation =

1. Download the plugin
2. Extract the contents of the zip file
3. Upload the contents of the zip file to the `wp-content/plugins/` folder of your WordPress installation
4. Activate the Code Snippets plugin from 'Plugins' page

https://youtu.be/MvErwlo-LQA


== Frequently Asked Questions ==

= Do you have any credentials using which I can test your product out? =

For testing purposes you can enter the following testing credentials made up of your API key and secret key.

Merchant Partner Id: testPartnerId
Merchant Private Key: testPrivateKey

= What happens if I transact with test credentials? =

All successfull transactions with the testing credentials will be refunded within 24hrs.
You can get support for this by mailing us at contact@bharatx.tech.

= I have checked the product out. I want to go live with it. How do I do that? =

That's amazing! Welcome to the BharatX family. We just need a few details from you to go live.
You can email jainam@bharatx.tech to get you sorted within 2 working hours.



== Changelog ==
= 1.2.1 =

* Security and UI enhancements

= 1.2.0 =

* Initial Release

= 1.3.0 =

* Fixes Price Text on product pages and updated credential setting UI

= 1.3.1 =

* Fixed multiple price text on pages

= 1.4.1 = 

* Added Feature : Exclude Payment Gateway For Specific Product Ids 

= 1.4.2 =

* Added Webhook Feature for Payment Confirmation

= 1.5.0 =

* Added Refund Payment Feature

= 1.5.1 =

* Fix: Occassionaly the payment method would result in 'Sorry, there was a problem with your payment' error

= 1.5.3 =

* Feat: auto choose pay in 3 option at checkout
* Fix: logo size standardization
* Feat: more descriptive payment gateway description
* Fix: phone number validation happens for all payment gateways

= 1.5.4 =
* Fix: Invalid argument supplied for foreach()

= 1.5.5 =
* Fix: autoselect.js missing in public directory

= 1.5.6 =
* Fix: action in checkout page was causing infinite loading in checkout page at some instances. Fixed by switching to a filter.

= 1.5.7 =
* Fix: Messaging strings and gateway enablement based on partner limit configurations.

= 1.5.8 =
* Fix: Plugin breaks admin panel

= 1.5.9 =
* Feat: allow PDP logo override 

= 1.6.0 =
* Fix: security bugs
* Feat: skipped receipt page before redirecting to BharatX flow
* Better logs for debugging purposes

= 1.6.1 =
* Feat: support for V2 BharatX APIs 
* Better logs for refund requests
