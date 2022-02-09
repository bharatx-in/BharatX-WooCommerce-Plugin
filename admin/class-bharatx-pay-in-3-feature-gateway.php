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

		// URL of the icon that will be displayed on checkout page near your gateway name.
		
		if ( $this->get_option('checkout_page_payment_method_logo_image') ) {
			$this->icon               = $this->get_option('checkout_page_payment_method_logo_image');
		} else {
			$this->icon               = plugin_dir_url( BHARATX_PAY_IN_3_FEATURE_PLUGIN_FILE ) . 'public/images/logo.png';
		}


		$this->has_fields         = true; // in case you need a custom credit card form.
		$this->method_title       = esc_html__( 'Bharatx', 'bharatx-pay-in-3-feature-plugin' );
		$this->method_description = esc_html__( 'Pay in 3 easy installments', 'bharatx-pay-in-3-feature-plugin' );
		$this->log                = new WC_Logger();

		$this->supports = array(
			'products',
	//	  	'refunds',
		);

		$this->init_form_fields();

		$this->init_settings();
		$this->title                  = 'Bharatx';
		$this->description            = 'Pay In 3';
		$this->enabled                = $this->get_option( 'enabled' );
		$this->merchant_partner_id    = $this->get_option( 'merchant_partner_id' );
		$this->merchant_private_key   = $this->get_option( 'merchant_private_key' );
		$this->color				  = $this->get_option( 'color' );
		

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		add_action( 'woocommerce_api_' . strtolower( 'BharatX_Pay_In_3_Feature_Gateway' ), array( $this, 'payment_callback' ) );
		
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
			),
			'checkout_page_payment_method_logo_image' => array(
				'title' => esc_html__( 'Brand Logo Url ', 'bharatx-pay-in-3-feature-plugin' ),
				'placeholder' => __( 'Optional', 'bharatx-pay-in-3-feature-plugin' ),
				'description' => esc_html__( 'Paste your brand logo url here to feature it on the payment gateway ', 'bharatx-pay-in-3-feature-plugin' ),
				'desc_tip'    => true,
				'type'  => 'text',
			),
			'color' => array(
				'title' => esc_html__( 'Primary Color', 'bharatx-pay-in-3-feature-plugin' ),
				'placeholder' => __( 'Optional', 'bharatx-pay-in-3-feature-plugin' ),
				'description' => esc_html__( 'Enter the hex color you want to use in the format FFFFFF ', 'bharatx-pay-in-3-feature-plugin' ),
				'desc_tip'    => true,
				'type'  => 'text',
			),
			'logging'                => array(
				'title'   => __( 'Enable Logging', 'bharatx-pay-in-3-feature-plugin' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Logging', 'bharatx-pay-in-3-feature-plugin' ),
				'default' => 'yes',
			),
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
	 * Returns refund URL.
	 *
	 * @since    1.2.0
	 * @return  string $url refund URL.
	 */

	/*
	 public function get_refund_url() {
		if ( $this->testmode ) {
			return '';
		} else {
			return '';
		}
	}
	*/

	/**
	 * Returns Redirect URL.
	 *
	 * @since    1.2.0
	 * @param Object $order Order.
	 * @return  array $url redirect URL.
	 */
	public function get_redirect_url( $order ) {
		$uniq_order_id = $this->get_unique_order_id($order->get_id());
		$transaction_id = wp_generate_uuid4();

				$body = array(
			'merchant_partner_id'                 => $this->merchant_partner_id,
			'transaction_status_redirection_url'  => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway&key=' . $order->get_order_key(),
		    'transaction_status_webhook_url'      => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway_Webhook&key=' . $order->get_order_key(),
			'order_id'                            => (string) $uniq_order_id,
			'amount_in_paise'                     => (int) ( $order->calculate_totals() * 100 ),
			'journey_id'                          => WC()->session->get( 'bharatx_journey_id' ),
		);

		$body['user'] = array(
			'first_name'   => $order->get_billing_first_name(),
			'last_name'    => $order->get_billing_last_name(),
			'phone_number' => $order->get_billing_phone(),
			'email'        => $order->get_billing_email(),
		);

		$body['user_details'] = array(
			'id'              => $transaction_id,
			'amount' 	      => $body['amount_in_paise'],
			'user'			  => array(
				'name'     		 => $order->get_billing_first_name() . $order->get_billing_last_name(),
				'phoneNumber'    => $phone,
				'email'           => $order->get_billing_email(),
			),
			'redirect'		  =>array(
				'url'			 => $body['transaction_status_redirection_url'],
				'logoOverride'	 => $this->icon,
				'colorOverride'  => '#' . $this->color,
			)
		);

		$body['billing_address'] = array(
			'line1'   => $order->get_billing_address_1(),
			'line2'   => $order->get_billing_address_2(),
			'city'    => $order->get_billing_city(),
			'pincode' => $order->get_billing_postcode(),
		);

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
				);
				array_push( $body['items'], $item_data );
			}
		}


		$this->log( 'BharatX redirecting' );

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
			),
			'body' => $php_string
		);
		$initiate_url         = $this->get_initiate_url();
		$response             = wp_remote_post( $initiate_url, $args);
		$encode_response_body = wp_remote_retrieve_body( $response );
		$response_code        = wp_remote_retrieve_response_code( $response );
		$this->dump_api_actions( $initiate_url, $args, $encode_response_body, $response_code );
		if ( 200 == $response_code ) {
			echo($response_code . $encode_response_body);
			$response_body = json_decode($encode_response_body);
			update_post_meta( $order->get_id(), '_bharatx_redirect_url', $response_body->redirectUrl );
			return array(
				'result'   => 'success',
				'redirect' =>$order->get_checkout_payment_url( true ),
			);
		} else{
			echo($response_code . $encode_response_body);
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
		
		//$_GET = stripslashes_deep( wc_clean( $_GET ) );
		
		// $this->dump_api_actions( 'paymenturl', '', $_GET );

		$order_key = ( isset( $_GET['key'] ) ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';

		$order_id = wc_get_order_id_by_order_key( $order_key );

		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}


			if ( isset( $_GET['txnStatus'] ) && 'SUCCESS' === sanitize_text_field( wp_unslash( $_GET['txnStatus'] ) ) ) {

				$status              = ( isset( $_GET['txnStatus'] ) ) ? sanitize_text_field( wp_unslash( $_GET['txnStatus'] ) ) : '';
				$nonce               = ( isset( $_GET['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
				$transaction_id      = ( isset( $_GET['txnId'] ) ) ? sanitize_text_field( wp_unslash( $_GET['txnId'] ) ) : '';

				$data = array();
				if ( ! empty( $nonce ) ) {
					$data['nonce'] = $nonce;
				}
				if ( ! empty( $_order_id ) ) {
					$data['order_id'] = $_order_id;
				}
				if ( ! empty( $status ) ) {
					$data['status'] = $status;
				}
				if ( ! empty( $transaction_id ) ) {
					$data['transaction_id'] = $transaction_id;
					$order->set_transaction_id( $transaction_id );
					$order->save();
				}

				$data       = build_query( $data );
				$_signature = hash_hmac( 'sha256', $data, $this->merchant_private_key );

				if ( true ) {
					$url = $this->get_transaction_status_url( $transaction_id );
					$this->log( 'BharatX Transaction Status Check' );
					$args                 = array(
						'headers'     => array(
							'Content-Type'  => 'application/json',
							'X-Partner-Id' => $this->merchant_partner_id,
						),
					);
					$response             = wp_remote_get( $url, $args);
					$encode_response_body = wp_remote_retrieve_body( $response );
					$response_code        = wp_remote_retrieve_response_code( $response );
					$this->dump_api_actions( $url, $args, $encode_response_body, $response_code );
					if ( 200 === $response_code ) {
						$response_body  = json_decode( $encode_response_body );
						$status         = $response_body->status;
						$transaction_id = $order->get_transaction_id();
						if ( 'SUCCESS' === $response_body->status) {
							$signature_fields = $transaction_id . $response_body->status . $this->merchant_private_key ;
							$hash_sign = hash('sha256', $signature_fields, true);
							$new_signature = base64_encode($hash_sign);
							if($response_body->signature === $new_signature) {
								$order->add_order_note( esc_html__( 'Payment approved by BharatX was successful.', 'Bharatx_Pay_In_3_Feature_Plugin' ) );
								$order->payment_complete( $transaction_id);
								WC()->cart->empty_cart();
								$redirect_url = $this->get_return_url( $order );
							} else {
								$message = esc_html__( 'Your payment via BharatX was unsuccessful due to authorization error. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
								$order->update_status( 'failed', $message );
								$this->add_order_notice( $message );
								$redirect_url = wc_get_checkout_url();
							}
						} else {
							$data          = $response_body->error;
							$error_code    = $data->code;
							$error_message = $data->message;
							$message       = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'pcss-woo-order-notifications' );
							$order->update_status( 'failed', $message );
							$this->add_order_notice( $message );
							$redirect_url = wc_get_checkout_url();
						}
					} else {
						$response_body  =  $encode_response_body ;
						$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.' . $response_body . $response_code , 'Bharatx_Pay_In_3_Feature_Plugin' );
						$order->update_status( 'failed', $message );
						$this->add_order_notice( $message );
						$redirect_url = wc_get_checkout_url();
					}
				} else {
					$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
					$order->update_status( 'failed', $message );
					$this->add_order_notice( $message );
					$redirect_url = wc_get_checkout_url();
				}
			} else {
				$error   = isset( $_GET['error_code'] ) ? sanitize_text_field( wp_unslash( $_GET['error_code'] ) ) : '';
				$message = esc_html__( 'Your payment via BharatX was Cancelled. Please try again.' , 'Bharatx_Pay_In_3_Feature_Plugin' );
				$order->update_status( 'failed', $message );
				$this->add_order_notice( $message );
				$redirect_url = wc_get_checkout_url();
			}
			wp_redirect( $redirect_url );
			exit();
		} catch(Exception $e) {
			print "something went wrong, caught yah! ";
		}
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

}
