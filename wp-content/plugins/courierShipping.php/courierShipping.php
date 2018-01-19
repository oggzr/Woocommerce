<?php
/*
Plugin Name: Courier Shipping
*/


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	function courier_shipping_init() {
		if ( ! class_exists( 'WC_My_Courier_Method' ) ) {
			class WC_My_Courier_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'cs1337'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Courier Shipping' );  // Title shown in admin
					$this->method_description = __( 'Shipping with Courier' ); // Description shown in admin

					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->title              = "Courier Shipping"; // This can be added as an setting but for this example its forced.

					$this->init();
				}

        function init_form_fields() {
          $this->form_fields = array (
            'cost' => array(
              'title' => 'Standard cost',
              'type' => 'number',
              'label' => '',
            ),
            'bronze' => array(
              'title' => 'Small',
              'type' => 'number',
              'label' => 'Small',
            ),
            'silver' => array(
              'title' => 'Medium',
              'type' => 'number',
              'label' => 'Medium',
            ),
            'gold' => array(
              'title' => 'Large',
              'type' => 'number',
              'label' => 'Large',
            ),

          );
        }

				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.

					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = [] ) {

          $cost = '0';
          $weight = -1;

          global $woocommerce;
            $items = $woocommerce->cart->get_cart();
            foreach ($items as $item) {

              if ( $item['data']->get_weight() > $weight){
                $weight = $item['data']->get_weight();
                $product = $item['data'];
              }elseif($item['data']->get_weight() === $weight){
                if(!is_null($item['data']->get_shipping_class())) {
                  if($this->settings[$product->get_shipping_class()] < $this->settings[$item['data']->get_shipping_class()]){
                    $weight = $item['data']->get_weight();
                    $product = $item['data'];
                  }
                }
              }
            }

            if (isset($product) && !is_null($product->get_shipping_class())) {

              $shippingCost = $this->settings[$product->get_shipping_class()];

              $cost = $shippingCost;

            }

            if($cost < $this->settings['cost']) {
              $cost = $this->settings['cost'];
            }



					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $cost,
						'calc_tax' => 'per_order'
					);

					// Register the rate
					$this->add_rate( $rate );
				}
			}
		}
	}

	add_action( 'woocommerce_shipping_init', 'courier_shipping_init' );

	function add_my_courier_method( $methods ) {
		$methods['your_courier_method'] = 'WC_My_Courier_Method';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'add_my_courier_method' );
}
