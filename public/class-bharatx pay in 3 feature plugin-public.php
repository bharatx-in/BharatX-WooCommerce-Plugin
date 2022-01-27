<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.bharatx.tech
 * @since      1.0.0
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Bharatx_Pay_In_3_Feature_Plugin
 * @subpackage Bharatx_Pay_In_3_Feature_Plugin/public
 * @author     BharatX <Karan@bharatx.tech>
 */
class Bharatx_Pay_In_3_Feature_Plugin_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	/**
	 * The settings of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $settings    Settings of this plugin.
	 */
	private $settings;

	/**
	 * The strings of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $strings    Strings of this plugin.
	 */

	private $strings;

	/**
	 * The supported countries of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $supported_countries  Supported countries of this plugin.
	 */

	private $supported_countries;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name         = $plugin_name;
		$this->version             = $version;
		$this->supported_countries = array( 'IN' );

		$this->strings = array(
			'price_string'               => '3 interest free payments of {{ amount }} with {{ logo }} {{ info_icon }}',
			'payment_method_title'       => 'BharatX',
			'payment_method_description' => 'Pay In 3',
			'varying_product_payment_description' => '3 interest free payments starting with {{ amount }} on {{ logo }} {{ info_icon }}'
		);

		add_action( 'init', array( $this, 'init' ) );

	}

	public function init() {
		
		$available_payment_methods = WC()->payment_gateways->get_available_payment_gateways();
		if ( isset( $available_payment_methods['bharatx-pay-in-3-feature'] ) ) {
			$this->settings = $available_payment_methods['bharatx-pay-in-3-feature']->settings;

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'remove_gateway_based_on_billing_country' ), 10, 2 );

			add_action( 'woocommerce_single_product_summary', array( $this, 'bharatx_price_text' ), 12 );
			add_action( 'woocommerce_before_add_to_cart_button', array($this, 'variation_price_text'), 10);

			add_filter( 'woocommerce_available_variation', array( $this, 'bharatx_price_text_variation' ), 10, 3 );

			add_action( 'woocommerce_cart_totals_after_order_total', array( $this, 'after_cart_totals' ), 99999 );

			add_action( 'woocommerce_review_order_after_order_total', array( $this, 'bharatx_price_text_checkout' ), 1 );

			add_filter( 'woocommerce_gateway_title', array( $this, 'checkout_gateway_title' ), 10, 2 );
			add_filter( 'woocommerce_gateway_description', array( $this, 'checkout_gateway_description' ), 10, 2 );
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bharatx_Pay_In_3_Feature_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bharatx_Pay_In_3_Feature_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bharatx pay in 3 feature plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'featherlight', plugin_dir_url( __FILE__ ) . 'js/featherlight.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Remove payment gateway based on country.
	 *
	 * @since    1.0.0
	 * @param      array $available_gateways       Available Payment Gateways.
	 * @return     array $available_gateways       Available Payment Gateways.
	 */
	public function remove_gateway_based_on_billing_country( $available_gateways ) {
		if ( is_admin() ) {
			return $available_gateways;
		}
		if ( ! WC()->customer ) {
			return $available_gateways;
		}
		$country_code = WC()->customer->get_billing_country();
		if ( $country_code ) {
			if ( ! in_array( $country_code, $this->supported_countries, true ) ) {
				if ( isset( $available_gateways['bharatx-pay-in-3-feature'] ) ) {
					unset( $available_gateways['bharatx-pay-in-3-feature'] );
				}
			}
		}
		return $available_gateways;
	}

	/**
	 * Display bharatx text on single product.
	 *
	 * @since    1.0.0
	 */
	public function bharatx_price_text() {
		global $product;
		if ( 'simple' === $product->get_type() ) {
			$price = $product->get_price();
			echo wp_kses_post( $this->get_bharatx_price_text( $price, 'product' ) );
		}
	}

	public function variation_price_text(){
		global $product;
		if ( 'variable' === $product->get_type() ) {
			$price = $product->get_price();
			$prices = $product->get_variation_prices();
			if(!empty($prices)){
				$min_price     = current( $prices['price'] );
				$max_price     = end( $prices['price'] );
				$min_reg_price = current( $prices['regular_price'] );
				$max_reg_price = end( $prices['regular_price'] );

				if($min_price != $max_price || $min_reg_price != $max_reg_price){
					echo '<div class="bharatx-price-variation-default-text">';
					echo wp_kses_post( $this->get_bharatx_price_text( $price, 'product', 'variation' ) );
					echo '</div>';
				}else{
					echo '<div class="bharatx-price-variation-default-text">';
					echo wp_kses_post( $this->get_bharatx_price_text( $price, 'product' ) );
					echo '</div>';
				}
				?>
                <script type="text/javascript">
				jQuery(document).ready(function($) {
					if($(".single_variation_wrap").length > 0){
						var $text = '';
						if($('.bharatx-price-variation-default-text').length > 0){
							var $text = $('.bharatx-price-variation-default-text').html();
						}
						$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
							if($('.bharatx-price-variation-default-text').length > 0){
								$('.bharatx-price-variation-default-text').html(variation.bharatx_price_text);
							}
						});
						$( ".single_variation_wrap" ).on( "hide_variation", function ( event, variation ) {
							if($('.bharatx-price-variation-default-text').length > 0){
								$('.bharatx-price-variation-default-text').html($text );
							}
						});
					}
                });
				</script>
                <?php
			}
		}
	}

	/**
	 *
	 * @since    1.0.0
	 * @param array  $value Value.
	 * @param Object $product Product.
	 * @param Object $variation Variation.
	 * @return array $value Value.
	 */
	public function bharatx_price_text_variation( $value, $product = null, $variation = null ) {
		if ( null != $variation ) {
			$price                = $variation->get_price();
			$value['bharatx_price_text'] = $this->get_bharatx_price_text( $price, 'product' );
		}
		return $value;
	}

	/**
	 * Display BharatX text on Checkout Page.
	 *
	 * @since    1.0.0
	 */
	public function bharatx_price_text_checkout() {
		$total = WC()->cart->get_total( 'edit' );
		echo '<tr class="order-bharatx">';
			echo '<td colspan="2">';
				echo wp_kses_post( $this->get_bharatx_price_text( $total, 'checkout' ) );
			echo '</td>';
		echo '</tr>';
	}

	/**
	 * Display Bharatx text on Cart Page.
	 *
	 * @since    1.0.0
	 */
	public function after_cart_totals(){
		$total = WC()->cart->get_total( 'edit' );
		?>
        <tr class="bharatx-cart-text">
        	<td colspan="2">
            	<?php
					echo wp_kses_post( $this->get_bharatx_price_text( $total, 'cart' ) );
				?>
            </td>
        </tr>
        <?php
	}

	/**
	 * Ger bharatx text.
	 *
	 * @since    1.0.0
	 * @param float  $price Price.
	 * @param string $page Page Name.
	 */
	public function get_bharatx_price_text( $price, $page, $type='simple' ) {
		$popup_image     = $this->settings['merchant_popup_image'];
		$featherlight    = '';
		$amount_in_paise = (int) ( round( $price, 2 ) * 100 );
		$div             = (int) floor( $amount_in_paise / 3 );
		$rem             = $amount_in_paise - ( $div * 3 );
		$part            = (int) $div + (int) $rem;
		$amount_in_rs    = (float) ( round( $part / 100, 2 ) );
		$part            = wc_price( $amount_in_rs );
		if ( $rem > 0 ) {
			$args            = array(
					'decimals'           => 2,
				);
			$part            = wc_price( $amount_in_rs, $args );
		}
		if( $this->settings['pay_in_3_note_bharatx_logo_image'] ) {
			$image           = '<img class="bharatx-brand-logo" src="' . esc_html( $this->settings['pay_in_3_note_bharatx_logo_image'] ) . '"/>';
		} else {
			$image           = '<img class="bharatx-brand-logo" src="' . esc_html( plugin_dir_url( __FILE__ ) . 'images/brand.svg' ) . '"/>';
		}

		$info_icon       = '<img src="' . esc_html( plugin_dir_url( __FILE__ ) . 'images/info.svg' ) . '"/>';
		if ( empty( $popup_image ) ) {
			$popup_image = 'https://bharatx.tech/wp-content/webp-express/webp-images/uploads/2021/10/p2.png.webp';
		}

		$featherlight = 'data-featherlight="' . $popup_image . '"';
		$interstitial_options = ' data-page="' . $page . '" ';
		if ( is_singular( 'product' ) ) {
			$object                = get_queried_object();
			$product_id            = $object->ID;
			$interstitial_options .= ' data-product-id="' . $product_id . '"';
		} else {
			$interstitial_options .= ' data-product-id=""';
		}

		ob_start();
		if($type == 'simple'){
			$string       = $this->strings['price_string'];
		}else{
			$string       = $this->strings['varying_product_payment_description'];
		}
		$placeholders = array(
			'{{ amount }}'   => $part,
			'{{ logo }}'     => sprintf( '<a class="bharatx-popup-link" href="#" %s %s><span class="product-bharatx-logo-text">%s</span></a>', $interstitial_options, $featherlight, $image ),
			'{{ info_icon }}' => sprintf( '<a class="bharatx-popup-link" href="#" %s %s>%s</a>', $interstitial_options, $featherlight, $info_icon ),
		);
		$string       = str_replace( array_keys( $placeholders ), $placeholders, $string );
		?>
		<p class="product-bharatx-text-note"><?php echo wp_kses_post( $string ); ?></p>
		<?php
		return ob_get_clean();
	}

	/**
	 * Return payment gateway title.
	 *
	 * @since    1.0.0
	 * @param string $title Title.
	 * @param string $id Gateway Id.
	 * @return string $title Title.
	 */
	public function checkout_gateway_title( $title, $id ) {
		if ( is_admin() ) {
			return $title;
		}
		if ( BHARATX_PAY_IN_3_FEATURE_SLUG === $id ) {

			if( $this->settings['checkout_page_payment_method_title'] ) {
				$title = $this->settings['checkout_page_payment_method_title'];
			} else {
				$title = $this->strings['payment_method_title'];
			}
		}
		return $title;
	}

	/**
	 * Return payment gateway description.
	 *
	 * @since    1.0.0
	 * @param string $description description.
	 * @param string $id Gateway Id.
	 * @return string $description description.
	 */
	public function checkout_gateway_description( $description, $id ) {
		if ( is_admin() ) {
			return $description;
		}
		if ( BHARATX_PAY_IN_3_FEATURE_SLUG === $id ) {
			if( $this->settings['checkout_page_payment_method_description'] ) {
				$description = $this->settings['checkout_page_payment_method_description'];
			} else {
				$description = $this->strings['payment_method_description'];
			}
		}
		return $description;
	}

}
