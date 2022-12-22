<?php
/**
 * The paymentgateway-specific functionality of the plugin.
 *
 * @since      1.2.0
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/admin
 * @author     BharatX <Karan@bharatx.tech>
 */
class Bharatx_Pay_In_3_Feature_Gateway extends WC_Payment_Gateway {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 */
	
	public function __construct() {

		$this->id                 = BHARATX_PAY_IN_3_FEATURE_PLUGIN_SLUG; // payment gateway plugin ID.
		$this->has_fields         = true; // in case you need a custom credit card form.
		$this->method_title       = esc_html__( 'Bharatx', 'bharatx-pay-in-3-feature-plugin' );
		$this->method_description = esc_html__( 'Pay in 3 easy installments' );
		$this->log                = new WC_Logger();
		$this->icon				  = 'https://d30flbpbaljuso.cloudfront.net/img/partner/logo/light/' . $this->get_option('merchant_partner_id');
		$this->supports = array(
			'products',
		  	'refunds',
		);

		$this->init_form_fields();

		$this->init_settings();
		$this->title                  = 'Bharatx';
		$this->description            = 'Pay In 3';
		$this->enabled                = $this->get_option( 'enabled' );
		$this->merchant_partner_id    = $this->get_option( 'merchant_partner_id' );
		$this->merchant_private_key   = $this->get_option( 'merchant_private_key' );
		$this->color				  = $this->get_option( 'color' );
	//	$this->category_ids			  = $this->get_option( 'category_ids' );
		$this->checkout_page_payment_method_title = $this-> get_option('checkout_page_payment_method_title');

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		add_action( 'woocommerce_api_' . strtolower( 'BharatX_Pay_In_3_Feature_Gateway' ), array( $this, 'payment_callback' ) );

		add_action( 'woocommerce_api_' . strtolower( 'BharatX_Pay_In_3_Feature_Gateway_Webhook' ), array( $this, 'webhook_callback' ) );
	}

	/**
	 * Plugin options
	 *
	 * @since    1.2.0
	 */


	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'                => array(
				'title'       => esc_html__( 'Enable/Disable', 'bharatx-pay-in-3-feature-plugin' ),
				'label'       => esc_html__( 'Enable BharatX', 'bharatx-pay-in-3-feature-plugin' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'yes',
			),
			'merchant_partner_id'     => array(
				'title' => esc_html__( 'Merchant Partner ID', 'bharatx-pay-in-3-feature-plugin' ),
				'type'  => 'text',
			),
			'merchant_private_key' => array(
				'title' => esc_html__( 'Merchant Private Key', 'bharatx-pay-in-3-feature-plugin' ),
				'type'  => 'password',
			),
			'checkout_page_payment_method_title' => array(
				'title' => esc_html__( 'Payment Method Title', 'bharatx-pay-in-3-feature-plugin' ),
				'placeholder' => __( 'Optional', 'bharatx-pay-in-3-feature-plugin' ),
				'type'  => 'text',
				'default' => 'Pay in 3 by '
			),
			'color' => array(
				'title' => esc_html__( 'Primary Color', 'bharatx-pay-in-3-feature-plugin' ),
				'placeholder' => __( '#FFFFFF', 'bharatx-pay-in-3-feature-plugin' ),
				'description' => esc_html__( 'Enter the hex color you want to use in the format #FFFFFF ', 'bharatx-pay-in-3-feature-plugin' ),
				'desc_tip'    => true,
				'type'  => 'text',
			),
			'category_ids' => array(
				'title' => esc_html__( 'Exclude Category Ids', 'bharatx-pay-in-3-feature-plugin' ),
				'placeholder' => __( 'xxx,xxx,xxx,xxx', 'bharatx-pay-in-3-feature-plugin' ),
				'description' => esc_html__( 'Enter the category ids with "," as a seperator' , 'bharatx-pay-in-3-feature-plugin' ),
				'desc_tip'    => true,
				'type'  => 'text',
			),
			'logging'                => array(
				'title'   => __( 'Enable Logging', 'bharatx-pay-in-3-feature-plugin' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Logging', 'bharatx-pay-in-3-feature-plugin' ),
				'default' => 'yes',
			),
			'pdp_popup_logo' => array(
				'title' => esc_html__('PDP Popup Logo', 'bharatx-pay-in-3-feature-plugin'),
				'placeholder' => __('https://mywebsite.com/assets/pdp.png', 'bharatx-pay-in-3-feature-plugin'),
				'label' => __('PDP Popup Logo', 'bharatx-pay-in-3-feature-plugin'),
				'description' => esc_html__('URL to override the preconfigured PDP logo'),
				'default' => ''
			)
		);
	}

	/**	
	 * Process Payment.
	 *
	 * @since    1.2.0
	 * @param      int $order_id       The Order ID.
	 * @return  string $url Redirect URL.
	 */
	public function process_payment( $order_id ) {
		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}

			return $this->get_redirect_url( $order );
		} catch(Exception $e) {
			print "something went wrong, caught yah! n";
		}
	}

	/**
	 * Returns Initiate URL.
	 *
	 * @since    1.2.0
	 * @return  string $url Initiate URL.
	 */
	public function get_initiate_url() {
			return 'https://web.bharatx.tech/api/transaction';
	}


	/**
	 * Returns Transaction status URL.
	 *
	 * @since    1.2.0
	 * @param int $order_id The Order Id.
	 * @return  string $url Transaction status URL.
	 */
	public function get_transaction_status_url( $transaction_id ) {
		$url = 'https://web.bharatx.tech/api/transaction/status?id={transaction_id}';
		$url = str_replace( '{transaction_id}', $transaction_id, $url );
		return $url;
	}

	/**
	 * Validates User's Phone Number.
 	* @since    1.2.0
 		*/

	function validatePhone() {
    	$billing_phone = filter_input(INPUT_POST, 'billing_phone');
		$billing_phone= preg_replace('/\s+/', '', $billing_phone);
    	$billing_phone=preg_replace("/[^0-9]/", '', $billing_phone);
		$brand_name= $this->checkout_page_payment_method_title;

    	if (strlen($billing_phone) < 10) {
       	 	wc_add_notice(__('Invalid <strong>Phone Number</strong>, Pay In 3 by' . $brand_name . ' supports only Indian Phone Numbers as of now.'), 'error');
    	}	
	}

	/**
	 * Returns refund URL.
	 *
	 * @since    1.0.0
	 * @return  string $url refund URL.
	 */
	public function get_refund_url() {
		return 'https://web.bharatx.tech/api/refund';
	}

	/**
	 * Returns Redirect URL.
	 *
	 * @since    1.2.0
	 * @param Object $order Order.
	 * @return  array $url redirect URL.
	 */
	public function get_redirect_url( $order ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-bharatx-pay-in-3-feature-plugin.php';

		$uniq_order_id = $this->get_unique_order_id($order->get_id());
		$order_id = $order->get_id();
		$transaction_id = wp_generate_uuid4();

		$phone = $order->get_billing_phone();
		$phone = preg_replace('/\s+/', '', $phone);

        switch(strlen($phone)){
        	case 10:
            	$phone = '+91' . $phone;
                break;
            
            
            case 11:
            	$phone = '+91' . substr($phone,1);
                break;
            
            case 12:
            	$phone = '+' . $phone;
                break;
        }

		$order_data = $order->get_data();
		$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
		$address1 = $order->get_shipping_address_1() .' ' . $order->get_shipping_address_2() . ' ' . $order->get_shipping_city() . $order->get_shipping_postcode();
		$billing_add = $order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
	

		$body = array(
			'merchant_partner_id'                 => $this->merchant_partner_id,
			'transaction_status_redirection_url'  => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway&key=' . $order->get_order_key(),
		    'transaction_status_webhook_url'      => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway_Webhook&key=' . $order->get_order_key(),
			'order_id'                            => (string) $uniq_order_id,
			'amount_in_paise'                     => (int) ( $order->calculate_totals() * 100 ),
			'journey_id'                          => WC()->session->get( 'bharatx_journey_id' ),
		);


		$body['items'] = array();
		if ( count( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item['variation_id'] ) {
					if ( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( $item['variation_id'] );
					} else {
						$product = new WC_Product( $item['variation_id'] );
					}
				} else {
					if ( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( $item['product_id'] );
					} else {
						$product = new WC_Product( $item['product_id'] );
					}
				}

				$item_data = array(
					'sku'           => $item['name'],
					'quantity'      => $item['qty'],
					'rate_per_item' => (int) ( round( ( $item['line_subtotal'] / $item['qty'] ), 2 ) * 100 ),
					'name' => $product->get_name(),
					'image' => $product->get_image(),	
					'url' => $product->get_permalink(),
					'dimensions' => array(
						'length' => $product->get_length(),
						'width'  => $product->get_width(),
						'height' => $product->get_height(),
					),
				);
				array_push( $body['items'],$item_data);
			}
		}

		$body['user'] = array(
			'first_name'   => $order->get_billing_first_name(),
			'last_name'    => $order->get_billing_last_name(),
			'phone_number' => $order->get_billing_phone(),
			'email'        => $order->get_billing_email(),
		);

		$body['billing_address'] = array(
			'line1'   => $order->get_billing_address_1(),
			'line2'   => $order->get_billing_address_2(),
			'city'    => $order->get_billing_city(),
			'pincode' => $order->get_billing_postcode(),
		);

		$body['user_details'] = array(
			'id'              => $transaction_id,
			'amount' 	      => $body['amount_in_paise'],
			'user'			  => array(
				'name'     		  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'phoneNumber'     => $phone,
				'email'           => $order->get_billing_email(),
			),
			'notes'			  => array(
				'woocommerceOrderId' => $uniq_order_id,
				'shippingAddress'	 => $address1,
				'billingAddress'	 => $billing_add,
				'items'				 => $body['items'],
				'orderKey'		     => $order->get_order_key(),
				'webhook_url'	     => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway_Webhook&key=' . $order->get_order_key(),
				'orderId' 			 => $order_id,
				),
			'redirect'		  => array(
				'url'			 => $body['transaction_status_redirection_url'],
				'logoOverride'	 => $this->icon,
				'colorOverride'  => $this->color,
			)
		);

		$order_key = $order->get_order_key();

		if ( ! empty( $order->get_billing_address_2() ) ) {
			$body['billing_address']['line1'] = $order->get_billing_address_2();
			$body['billing_address']['line2'] = $order->get_billing_address_1();
		}

		$body['shipping_address'] = array(
			'line1'   => $order->get_shipping_address_1(),
			'line2'   => $order->get_shipping_address_2(),
			'city'    => $order->get_shipping_city(),
			'pincode' => $order->get_shipping_postcode(),
		);

		$this->log( "INFO: creating bharatx transaction for order/$order_id/$order_key" );

		$php_string = json_encode($body['user_details'] );
		$msg_hash = $php_string . '/api/transaction' . $this->merchant_private_key;
		$shasignature = hash('sha256',$msg_hash,true);
		$signature = base64_encode($shasignature);

		$args                 = array(
			'method'      => "POST",
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'X-Partner-Id'  => $this->merchant_partner_id,
				'X-Signature'	=> $signature,
				'Expect' => '',
			),
			'body' => $php_string,
			'blocking' => true,
			'timeout' => 60
		);

		$initiate_url         = $this->get_initiate_url();
		$response             = wp_remote_post( $initiate_url, $args);
		$encode_response_body = wp_remote_retrieve_body( $response );
		$response_code        = wp_remote_retrieve_response_code( $response );

		$this->log("INFO: received statusCode/$response_code for creating transaction/$transaction_id for order/$order_id/$order_key");

		if ( 200 == $response_code ) {
			$response_body = json_decode($encode_response_body);
			$order->add_order_note( esc_html__( 'Transaction Id: ' . $transaction_id , 'Bharatx_Pay_In_3_Feature_Plugin' ) );

			Bharatx_Pay_In_3_Feature_Plugin::set_bharatx_transaction_id_for_order($order_key, $transaction_id);
			$this -> log("INFO: associated transaction/$transaction_id with order/$order_id/$order_key");

			update_post_meta( $order->get_id(), '_bharatx_redirect_url', $response_body->redirectUrl );

			return array(
				'result'   => 'success',
				"redirect" => $response_body->redirectUrl ,
			);
		} else{
			$this -> log("ERROR: error occured while creating BharatX transaction for order/$order_id/$order_key: $encode_response_body");

			$order->add_order_note( esc_html__( 'Unable to generate the transaction ID. Payment couldn\'t proceed.', 'bharatx-pay-in-3-feature-plugin' ) );
			wc_add_notice( esc_html__( 'Sorry, there was a problem with your payment.', 'bharatx-pay-in-3-feature-plugin' ), 'error' );

			return array(
				'result'   => 'failure',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}
	}

	/**
	 * Generates unique BharatX order id.
	 *
	 * @since    1.2.0
	 * @param string $order_id Order ID.
	 * @return  string $uniq_order_id Unique Order ID.
	 */
	public function get_unique_order_id( $order_id ) {
		$random_bytes = random_bytes(13);
		return $order_id . '-' . bin2hex($random_bytes);
	}

	/**
	 * Log Messages.
	 *
	 * @since    1.2.0
	 * @param string $message Log Message.
	 */
	public function log( $message ) {
		if ( $this->get_option( 'logging' ) === 'no' ) {
			return;
		}
		if ( empty( $this->log ) ) {
			$this->log = new WC_Logger();
		}
		$this->log->add( 'BharatX', $message );
	}

	/**
	 * Dump API Actions.
	 *
	 * @since    1.2.0
	 * @param string $url URL.
	 * @param Array  $request Request.
	 * @param Array  $response Response.
	 * @param Int    $status_code Status Code.
	 */
	public function dump_api_actions( $url, $request = null, $response = null, $status_code = null ) {
		ob_start();
		echo esc_url( $url );
		echo '<br>';
		echo 'Request Body : ';
		echo '<br>';
		print_r( $request );
		echo '<br>';
		echo 'Response Body : ';
		echo '<br>';
		print_r( $response );
		echo '<br>';
		echo 'Status Code : ';
		echo esc_html( $status_code );
		$data = ob_get_clean();
		$this->log( $data );
	}

	/**
	 * Receipt Page.
	 *
	 * @since    1.2.0
	 * @param  int $order_id Order Id.
	 */
	public function receipt_page( $order_id ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please wait as your transaction is initiated on the BharatX', 'bharatx-pay-in-3-feature-plugin' ) . '</p>';
		$redirect_url = get_post_meta( $order_id, '_bharatx_redirect_url', true );
		?>
		<script>
			var redirect_url = <?php echo json_encode( $redirect_url ); ?>;
			window.location.replace(redirect_url);
		</script>
		<?php
	}

	public function payment_callback() {
		$order_key = ( isset( $_GET['key'] ) ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		$order_id = wc_get_order_id_by_order_key( $order_key );
		$this -> log("INFO: payment callback for order/$order_id/$order_key");

		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}

			$current_order_status = $order->get_status();
			$this->log("INFO: payment callback for order/$order_id/$order_key with orderStatus/$current_order_status");

			if ($current_order_status != "pending" && $current_order_status != "failed") {
				$this->log("WARN: invalid orderStatus/$current_order_status for order/$order_id/$order_key");

				wp_redirect($this->get_return_url($order));
				exit();
			}

			$transaction_id = Bharatx_Pay_In_3_Feature_Plugin::get_bharatx_transaction_id_for_order($order_key);
			$this -> log("INFO: fetched previously mapped transactionId/$transaction_id for order/$order_id/$order_key");

			if (is_null($transaction_id)) {
				$this -> log("ERROR: no mapped transactionId could be fetched for order/$order_key");
				$this -> log("INFO: redirecting back to cart page");

				wp_redirect(wc_get_checkout_url());
				exit();
			}

			$this -> log("INFO: fetching latest status of transactionId/$transaction_id");

			$url = $this->get_transaction_status_url( $transaction_id );
			$args = array(
				'headers'     => array(
					'Content-Type'  => 'application/json',
					'X-Partner-Id' => $this->merchant_partner_id,
				),
				"timeout" => 60
			);
			$response = wp_remote_get( $url, $args);
			$encode_response_body = wp_remote_retrieve_body( $response );
			$response_code = wp_remote_retrieve_response_code( $response );

			$this->log("INFO: got statusCode/$response_code for get transaction: transactionId/$transaction_id for order/$order_id/$order_key");
			if (200 == $response_code) {
				$response_body  = json_decode( $encode_response_body );
				$status         = $response_body->status;

				if ("SUCCESS" != $status) {
					$this->log("ERROR: invalid txnStatus/$status for transaction/$transaction_id for order/$order_id/$order_key");
					$order->update_status( 'failed', "error updating status from BharatX");
					$this->add_order_notice("Some error occured while updating status");
					wp_redirect(wc_get_checkout_url());
					exit();
				}

				$this->log("INFO: successful transaction/$transaction_id for order/$order_id/$order_key");

				$order->add_order_note( esc_html__( 'Payment successfully processed via BharatX Pay in 3' , 'Bharatx_Pay_In_3_Feature_Plugin' ) );
				$order->payment_complete( $transaction_id);
				WC()->cart->empty_cart();
				
				wp_redirect($this->get_return_url( $order ));
				exit();
			} else {
				$this->log("ERROR: invalid response for get transaction/$transaction_id for order/$order_id/$order_key: $encode_response_body");
				$order->update_status( 'failed', "error updating status from BharatX");
				$this->add_order_notice("Some error occured while updating status");
				wp_redirect(wc_get_checkout_url());
				exit();
			}
		} catch(Exception $e) {
			$errorMessage = $e->getMessage();
			$this->log("ERROR: error occured while confirming status for order/$order_id: $errorMessage");
			wp_redirect(wc_get_checkout_url());
			exit();
		}
	}


	public function webhook_callback() {
		return $this->payment_callback();
	}


	/**
	 * Add notice to order.
	 *
	 * @since    1.2.0
	 * @param  string $message Message.
	 */
	public function add_order_notice( $message ) {
		wc_add_notice( $message, 'error' );
	}

	/** 
	 * @since   1.5.0
     * @param Int    $order_id Order Id.
	 * @param float  $amount Amount.
	 * @param String $reason Refund Reason.
	 * @return  bool true|false Return Refund Status.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}
			$transaction_id = $order->get_transaction_id();

			$uniq_order_id = $this->get_unique_order_id($order->get_id());
			update_post_meta( $order->get_id(), '_bharatx_order_refund_id', $uniq_order_id );

			$body                    = array(
				'transactionId'     => $transaction_id,
			);

			$php_string = json_encode($body);
			$msg_hash = $php_string . '/api/refund' . $this->merchant_private_key;
			$shasignature = hash('sha256',$msg_hash,true);
			$signature = base64_encode($shasignature);

			$args                 = array(
					'headers'     => array(
						'Content-Type'  => 'application/json',
						'X-Partner-Id' => $this->merchant_partner_id,
						'X-Signature' => $signature,
					),
					'body'		  => $php_string
			);
			$url                  = $this->get_refund_url();
			$response             = wp_remote_post( $url, $args );
			$encode_response_body = wp_remote_retrieve_body( $response );
			$response_code        = wp_remote_retrieve_response_code( $response );
			$this->dump_api_actions( $url, $args, $encode_response_body, $response_code );
			$status  = '';
			$message = '';
			$response_body = json_decode( $encode_response_body );
			if ( 200 == $response_code ) {
					$status = true;
					$message = sprintf( __( 'Refund of %1$s successfully sent to BharatX', 'Bharatx_Pay_In_3_Feature_Plugin' ), $amount );
			} else {
				$status  = false;
				$message = sprintf( __( 'There was an error submitting the refund to BharatX.' . $response_body->message, 'Bharatx_Pay_In_3_Feature_Plugin' ) );
			}

			if ( true === $status ) {
				$order->add_order_note( $message );
				return true;
			} else {
				$order->add_order_note( $message );
				return false;
			}
		} catch(Exception $e) {
			print "something went wrong, caught yah! ";
		}
	}

}
