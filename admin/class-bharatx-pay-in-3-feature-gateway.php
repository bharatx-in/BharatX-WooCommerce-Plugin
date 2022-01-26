<?php
/**
 * The paymentgateway-specific functionality of the plugin.
 *
 * @since      1.0.0
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
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->id                 = BHARATX_PAY_IN_3_FEATURE_SLUG; // payment gateway plugin ID.

		// URL of the icon that will be displayed on checkout page near your gateway name.
		if ( $this->get_option('checkout_page_payment_method_logo_image') ) {
			$this->icon               = $this->get_option('checkout_page_payment_method_logo_image');
		} else {
			$this->icon               = plugin_dir_url( BHARATX_PAY_IN_3_FEATURE_FILE ) . 'public/images/brand.svg';
		}


		$this->has_fields         = true; // in case you need a custom credit card form.
		$this->method_title       = esc_html__( 'Bharatx', 'bharatx-pay-in-3-for-woocommerce' );
		$this->method_description = esc_html__( 'Enables the user to pay in 3 interest free payments.', 'Bharatx_Pay_In_3_Feature_Plugin' );
		$this->log                = new WC_Logger();

		$this->supports = array(
			'products',
		  //'refunds',
		);

		$this->init_form_fields();

		$this->init_settings();
		$this->title                  = 'Bharatx';
		$this->description            = 'Pay In 3';
		$this->enabled                = $this->get_option( 'enabled' );
		$this->testmode               = 'yes' === $this->get_option( 'testmode' );
		$this->merchant_partner_id     = $this->get_option( 'merchant_partner_id' );
		$this->merchant_client_secret = $this->get_option( 'merchant_client_secret' );
		$this->name     = $this->get_option( 'name' );
		$this->phone_number     = $this->get_option( 'phone_number' );
		$this->email     = $this->get_option( 'email' );

		
		

		add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

		add_action( 'woocommerce_api_' . strtolower( 'BharatX_Pay_In_3_Feature_Gateway' ), array( $this, 'payment_callback' ) );
		
		add_action( 'woocommerce_api_' . strtolower( 'BharatX_Pay_In_3_Feature_Gateway_Webhook' ), array( $this, 'webhook_callback' ) );
	}

	/**
	 * Plugin options, we deal with it in Step 3 too
	 *
	 * @since    1.0.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'                => array(
				'title'       => esc_html__( 'Enable/Disable', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'label'       => esc_html__( 'Enable BharatX', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'testmode'               => array(
				'title'       => esc_html__( 'Test mode', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'label'       => esc_html__( 'Enable Test Mode', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'        => 'checkbox',
				'description' => esc_html__( 'Place the payment gateway in test mode using test API keys.', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'merchant_partner_id'     => array(
				'title' => esc_html__( 'Merchant Partner ID', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),
			'merchant_client_secret' => array(
				'title' => esc_html__( 'Merchant Private Key', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'password',
			),

			'name'     => array(
				'title' => esc_html__( 'Name', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'phone_number'     => array(
				'title' => esc_html__( 'Phone Number', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'email'     => array(
				'title' => esc_html__( 'Email', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'email',
			),

			'merchant_popup_image'   => array(
				'title' => esc_html__( 'Price Breakdown Popup Image Path', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'placeholder' => __( 'Optional', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'pay_in_3_note_bharatx_logo_image' => array(
				'title' => esc_html__( 'Price Breakdown Description BharatX Logo Image Path', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'placeholder' => __( 'Optional', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'checkout_page_payment_method_title' => array(
				'title' => esc_html__( 'Payment Method Title', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'placeholder' => __( 'Optional', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'checkout_page_payment_method_description' => array(
				'title' => esc_html__( 'Payment Method Description', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'placeholder' => __( 'Optional', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'checkout_page_payment_method_logo_image' => array(
				'title' => esc_html__( 'Payment Method Logo Image Path', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'placeholder' => __( 'Optional', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'  => 'text',
			),

			'logging'                => array(
				'title'   => __( 'Enable Logging', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Logging', 'Bharatx_Pay_In_3_Feature_Plugin' ),
				'default' => 'yes',
			),
		);
	}

	/**
	 * Process Payment.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * @return  string $url Initiate URL.
	 */
	public function get_initiate_url() {
			return 'https://web.bharatx.tech/api/transaction';
	}


	/**
	 * Returns Transaction status URL.
	 *
	 * @since    1.0.0
	 * @param int $order_id The Order Id.
	 * @return  string $url Transaction status URL.
	 */
	public function get_transaction_status_url( $order_id ) {
		$url = 'https://web.bharatx.tech/api/transaction?id={order_id}';
		$url = str_replace( '{order_id}', $order_id, $url );
		return $url;
	}

	/**
	 * Returns refund URL.
	 *
	 * @since    1.0.0
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

	// Returns Unique Transaction Id

	public function guidv4($data = null) {
		// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);
	
		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);
	
		// Output the 36 character UUID.
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
	 

	/**
	 * Returns Redirect URL.
	 *
	 * @since    1.0.0
	 * @param Object $order Order.
	 * @return  array $url redirect URL.
	 */
	public function get_redirect_url( $order ) {
		$uniq_order_id = $this->get_unique_order_id($order->get_id());
		$transaction_id = $this->guidv4();

		update_post_meta( $order->get_id(), '_bharatx_order_id', $uniq_order_id );

		$body = array(
			'merchant_partner_id'                 => $this->merchant_partner_id,
			// 'transaction_status_redirection_url' => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway&key=' . $order->get_order_key(),
			// 'transaction_status_webhook_url' => get_site_url() . '/?wc-api=Bharatx_Pay_In_3_Feature_Gateway_Webhook&key=' . $order->get_order_key(),
			'order_id'                           => (string) $uniq_order_id,
			'amount_in_paise'                    => (int) ( $order->calculate_totals() * 100 ),
			'journey_id'                         => WC()->session->get( 'bharatx_journey_id' ),
		);

		$body['user'] = array(
			'first_name'   => $order->get_billing_first_name(),
			'last_name'    => $order->get_billing_last_name(),
			'phone_number' => $order->get_billing_phone(),
			'email'        => $order->get_billing_email(),
		);

		$body['user_details'] = array(
			'id'              => $transaction_id,
			'amount' 	      => (int) ( $order->calculate_totals() * 100 ),
			'name'     		  => $order->get_billing_first_name() . $order->get_billing_last_name(),
			'phone_number'    => $order->get_billing_phone(),
			'email'           => $order->get_billing_email(),
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

		$php_string = json_encode($body['user_details']);
		$signature = hash_hmac('sha256',$php_string,$this->merchant_client_secret,true);

		$args                 = array(
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'X-Partner-Id'  => $this->merchant_partner_id,
				'X-Signature'	=> $signature,
			)
		);
		$initiate_url         = $this->get_initiate_url();
		$response             = wp_remote_post( $initiate_url, $args['headers'] );
		$encode_response_body = wp_remote_retrieve_body( $response );
		$response_code        = wp_remote_retrieve_response_code( $response );
		$this->dump_api_actions( $initiate_url, $args['headers'], $encode_response_body, $response_code );
		if ( 200 === $response_code ) {
			$response_body = json_decode( $encode_response_body );
			update_post_meta( $order->get_id(), '_bharatx_redirect_url', $response_body->data->redirection_url );
			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		} else {
			$order->add_order_note( esc_html__( 'Unable to generate the transaction ID. Payment couldn\'t proceed.', 'Bharatx_Pay_In_3_Feature_Plugin' ) );
			wc_add_notice( esc_html__( 'Sorry, there was a problem with your payment.', 'Bharatx_Pay_In_3_Feature_Plugin' ), 'error' );
			return array(
				'result'   => 'failure',
				'redirect' => $order->get_checkout_payment_url( true ),
			);
		}

	}

	/**
	 * Generates unique BharatX order id.
	 *
	 * @since    1.0.0
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
	 * @since    1.0.0
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
	 * @since    1.0.0
	 * @param string $url URL.
	 * @param Array  $request Request.
	 * @param Array  $response Response.
	 * @param Int    $status_code Status Code.
	 */
	public function dump_api_actions( $url, $request = null, $response = null, $status_code = null ) {
		if ( $this->get_option( 'testmode' ) === 'no' ) {
			return;
		}
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
	 * @since    1.0.0
	 * @param  int $order_id Order Id.
	 */
	public function receipt_page( $order_id ) {
		echo '<p>' . esc_html__( 'Thank you for your order, please wait as you will be automatically redirected to BharatX.', 'Bharatx_Pay_In_3_Feature_Plugin' ) . '</p>';

		$redirect_url = get_post_meta( $order_id, '_bharatx_redirect_url', true );
		?>
		<script>
			var redirect_url = <?php echo json_encode( $redirect_url ); ?>;
			window.location.replace(redirect_url);
		</script>
		<?php
	}

	/**
	 * Payment Callback check.
	 *
	 * @since    1.0.0
	 */
	public function payment_callback() {
		$_GET = stripslashes_deep( wc_clean( $_GET ) );
		$this->dump_api_actions( 'paymenturl', '', $_GET );

		$order_key = ( isset( $_GET['key'] ) ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';

		$order_id = wc_get_order_id_by_order_key( $order_key );

		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}

			if ( isset( $_GET['status'] ) && 'SUCCESS' === sanitize_text_field( wp_unslash( $_GET['status'] ) ) ) {

				$_order_id           = ( isset( $_GET['order_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '';
				$status              = ( isset( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
				$signature           = ( isset( $_GET['signature'] ) ) ? sanitize_text_field( wp_unslash( $_GET['signature'] ) ) : '';
				$signature_algorithm = ( isset( $_GET['signature_algorithm'] ) ) ? sanitize_text_field( wp_unslash( $_GET['signature_algorithm'] ) ) : '';
				$nonce               = ( isset( $_GET['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
				$transaction_id      = ( isset( $_GET['transaction_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['transaction_id'] ) ) : '';

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
				$_signature = hash_hmac( 'sha256', $data, $this->merchant_client_secret );

				if ( $signature === $_signature ) {
					$url = $this->get_transaction_status_url( $_order_id );
					$this->log( 'BharatX Transaction Status Check' );
					$args                 = array(
						'headers'     => array(
							'Content-Type'  => 'application/json',
							'X-Partner-Id' => $this->merchant_partner_id,
						),
					);
					$response             = wp_remote_get( $url, $args['headers'] );
					$encode_response_body = wp_remote_retrieve_body( $response );
					$response_code        = wp_remote_retrieve_response_code( $response );
					$this->dump_api_actions( $$url, $args['headers'], $encode_response_body, $response_code );
					if ( 200 === $response_code ) {
						$response_body  = json_decode( $encode_response_body );
						$data           = $response_body->data;
						$transaction_id = $order->get_transaction_id();
						if ( empty( $transaction_id ) && ! empty( $data->id ) ) {
							$order->set_transaction_id( $data->id );
						}
						if ( true === $response_body->success ) {
							if ( 'SUCCESS' === $data->status ) {
								$order->add_order_note( esc_html__( 'Payment approved by BharatX successfully.', 'Bharatx_Pay_In_3_Feature_Plugin' ) );
								$order->payment_complete( $data->id );
								WC()->cart->empty_cart();
								$redirect_url = $this->get_return_url( $order );
							} else {
								$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
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
						$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
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
				$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
				$order->update_status( 'failed', $message );
				$this->add_order_notice( $message );
				$redirect_url = wc_get_checkout_url();
			}
			wp_redirect( $redirect_url );
			die();
		} catch(Exception $e) {
			print "something went wrong, caught yah! n";
		}
	}
	
	/**
	 * Webhook Callback check.
	 *
	 * @since    1.0.0
	 */
	public function webhook_callback() {
		$_GET = stripslashes_deep( wc_clean( $_GET ) );
		$this->dump_api_actions( 'webhook', '', $_GET );
		
		$order_key = ( isset( $_GET['key'] ) ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';

		$order_id = wc_get_order_id_by_order_key( $order_key );
		
		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}
			
			if( 'pending' != $order->get_status() ) return;
			
			if ( isset( $_GET['status'] ) && 'SUCCESS' === sanitize_text_field( wp_unslash( $_GET['status'] ) ) ) {
				$_order_id           = ( isset( $_GET['order_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order_id'] ) ) : '';
				$status              = ( isset( $_GET['status'] ) ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '';
				$signature           = ( isset( $_GET['signature'] ) ) ? sanitize_text_field( wp_unslash( $_GET['signature'] ) ) : '';
				$signature_algorithm = ( isset( $_GET['signature_algorithm'] ) ) ? sanitize_text_field( wp_unslash( $_GET['signature_algorithm'] ) ) : '';
				$nonce               = ( isset( $_GET['nonce'] ) ) ? sanitize_text_field( wp_unslash( $_GET['nonce'] ) ) : '';
				$transaction_id      = ( isset( $_GET['transaction_id'] ) ) ? sanitize_text_field( wp_unslash( $_GET['transaction_id'] ) ) : '';
				
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
				$_signature = hash_hmac( 'sha256', $data, $this->merchant_client_secret );
				
				if ( $signature === $_signature ) {
					$url = $this->get_transaction_status_url( $_order_id );
					$this->log( 'BharatX Transaction Status Check' );
					$args                 = array(
						'headers'     => array(
							'Content-Type'  => 'application/json',
							'X-Partner-Id' => $this->merchant_client_secret,
						),
						'timeout'     => 80,
						'redirection' => 35,
					);
					$response             = wp_remote_get( $url, $args );
					$encode_response_body = wp_remote_retrieve_body( $response );
					$response_code        = wp_remote_retrieve_response_code( $response );
					$this->dump_api_actions( $$url, $args, $encode_response_body, $response_code );
					if ( 200 === $response_code ) {
						$response_body  = json_decode( $encode_response_body );
						$data           = $response_body->data;
						$transaction_id = $order->get_transaction_id();
						if ( empty( $transaction_id ) && ! empty( $data->id ) ) {
							$order->set_transaction_id( $data->id );
						}
						if ( true === $response_body->success ) {
							if ( 'SUCCESS' === $data->status ) {
								$order->add_order_note( esc_html__( 'Payment approved by BharatX successfully.', 'Bharatx_Pay_In_3_Feature_Plugin' ) );
								$order->payment_complete( $data->id );
							} else {
								$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
								$order->update_status( 'failed', $message );
								$this->add_order_notice( $message );
							}
						} else {
							$data          = $response_body->error;
							$error_code    = $data->code;
							$error_message = $data->message;
							$message       = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'pcss-woo-order-notifications' );
							$order->update_status( 'failed', $message );
							$this->add_order_notice( $message );
						}
					} else {
						$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
						$order->update_status( 'failed', $message );
						$this->add_order_notice( $message );
					}
				} else {
					$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
					$order->update_status( 'failed', $message );
					$this->add_order_notice( $message );
				}	
			}else {
				$error   = isset( $_GET['error_code'] ) ? sanitize_text_field( wp_unslash( $_GET['error_code'] ) ) : '';
				$message = esc_html__( 'Your payment via BharatX was unsuccessful. Please try again.', 'Bharatx_Pay_In_3_Feature_Plugin' );
				$order->update_status( 'failed', $message );
				$this->add_order_notice( $message );
			}
			
		}catch(Exception $e) {
			print "something went wrong, caught yah! n";
		}
	}



	/**
	 * Add notice to order.
	 *
	 * @since    1.0.0
	 * @param  string $message Message.
	 */
	public function add_order_notice( $message ) {
		wc_add_notice( $message, 'error' );
	}

	/**
	 * Process Refund.
	 *
	 * @since    1.0.0
	 * @param Int    $order_id Order Id.
	 * @param float  $amount Amount.
	 * @param String $reason Refund Reason.
	 * @return  bool true|false Return Refund Status.
	*/
	/*
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		try {
			if ( function_exists( 'wc_get_order' ) ) {
				$order = wc_get_order( $order_id );
			} else {
				$order = new WC_Order( $order_id );
			}
			$transaction_id = $order->get_transaction_id();
			$url            = $this->get_refund_url();

			$uniq_order_id = $this->get_unique_order_id($order->get_id());
			update_post_meta( $order->get_id(), '_bharatx_order_refund_id', $uniq_order_id );

			$body                 = array(
				'merchant_partner_id' => $this->merchant_partner_id,
				'amount_in_paise'    => (int) ( round( $amount, 2 ) * 100 ),
				'transaction_id'     => $transaction_id,
				'reason'             => $reason,
				'order_id'           => (string) $uniq_order_id,
			);
			$args                 = array(
				'headers'     => array(
					'Content-Type'  => 'application/json',
					'X-Partner-Id' => $this->merchant_client_secret,
					'X-Signature' => $this->encodedSignature,
				),
				'body'        => json_encode( $body ),
				'timeout'     => 80,
				'redirection' => 35,
			);
			$response             = wp_remote_post( $url, $args );
			$encode_response_body = wp_remote_retrieve_body( $response );
			$response_code        = wp_remote_retrieve_response_code( $response );
			$this->dump_api_actions( $url, $args, $encode_response_body, $response_code );
			$status  = '';
			$code    = '';
			$message = '';
			if ( 200 === $response_code ) {
				$response_body = json_decode( $encode_response_body );
				if ( true === $response_body->success ) {
					$data   = $response_body->data;
					$status = true;
					*/
					/* translators: %1$s Amount, %2$s Refund ID */
					/*
					$message = sprintf( __( 'Refund of %1$s successfully sent to BharatX. Refund Transaction Id : %2$s', 'Bharatx_Pay_In_3_Feature_Plugin' ), $amount, $data->refunded_transaction_id );
				} else {
					$status = false;
					*/
					/* translators: %1$s Error Code, %2$s Error Message */
					/*
					$message = sprintf( __( 'There was an error submitting the refund to BharatX. Error Code %1$s, Error Message : %2$s', 'Bharatx_Pay_In_3_Feature_Plugin' ), $response_body->error->code, $response_body->error->message );
				}
			} else {
				$status  = false;
				$message = sprintf( __( 'There was an error submitting the refund to BharatX.', 'Bharatx_Pay_In_3_Feature_Plugin' ) );
			}

			if ( true === $status ) {
				$order->add_order_note( $message );
				return true;
			} else {
				$order->add_order_note( $message );
				return false;
			}
		} catch(Exception $e) {
			$this->notify_airbrake( $e, $order_id );
			return false;
		}
	}

	*/
}
