<?php
/*
Plugin Name: Ship to store
*/





if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {



  add_action('woocommerce_after_shipping_rate', function($method){

    if($method->id == 'ss1337' ) {
      $args = array(
    		'post_type'	=>'store',
    	);

    	$stores = new WP_Query($args);

      echo '<select name="stores">';

      while($stores->have_posts()) : $stores->the_post();

        $name = get_the_title();

        echo '<option value="'.$name.'">'. $name . '</option>';

      endwhile;

      echo '</select>';

    }

  });

  add_action('woocommerce_checkout_update_order_meta', 'storeMyData');

  function storeMyData($order_id) {
    if($_POST['shipping_method'][0] === 'ss1337'){
          update_post_meta( $order_id, 'Deliver store', $_POST['stores'] );
      }
  }


    add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );



  function my_custom_checkout_field_display_admin_order_meta($order){
    if(get_post_meta( $order->id, 'Deliver store', true )){
      echo '<p>Customer wants to ship to this store: <strong>'.get_post_meta( $order->id, 'Deliver store', true ).'</strong></p>';
}
  }



  function your_shipping_method_init() {
    if ( ! class_exists( 'WC_Your_Shipping_Method' ) ) {
  	class WC_Your_Shipping_Method extends WC_Shipping_Method {
  		/**
  		 * Constructor for your shipping class
  		 *
  		 * @access public
  		 * @return void
  		 */
  		public function __construct() {
  			$this->id                 = 'ss1337';
        $this->method_title       = __( 'Store Shipping' );  // Title shown in admin
  			$this->title       = __( 'Store Shipping' );
  			$this->method_description = __( 'SS Settings' ); //
  			$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
  			$this->init();
  		}

      function init_form_fields() {
        $this->form_fields = array (
          'maxSum' => array(
            'title' => 'Free shipping sum',
            'type' => 'number',
            'label' => '',
          ),
          'cost' => array(
            'title' => 'Standard cost',
            'type' => 'number',
            'label' => '',
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
  		public function calculate_shipping( $package = array() ) {

        $cost = $this->settings['cost'];

        global $woocommerce;

        $cartTotal = $woocommerce->cart->subtotal;

        $free = $this->settings['maxSum'];

        if ($cartTotal > $free) {
          $cost = '0';
        }



        $rate = array(
      	'id'       => $this->id,
      	'label'    => "Store Pickup",
      	'cost'     => $cost,
      	'calc_tax' => 'per_order'
  );

        // Register the rate
        $this->add_rate( $rate );
  		}
  	}
  }
  }

  add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );

  function add_your_shipping_method( $methods ) {
  	$methods['your_shipping_method'] = 'WC_Your_Shipping_Method';
  	return $methods;
  }

  add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' ); }
