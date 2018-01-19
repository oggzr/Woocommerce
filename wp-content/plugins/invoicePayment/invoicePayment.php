<?php
/*
Plugin Name: Invoice Payment
*/



add_action( 'plugins_loaded', 'init_your_gateway_class' );

  function init_your_gateway_class() {
      class WC_Gateway_Your_Gateway extends WC_Payment_Gateway {

        function __construct() {
          $this->id = 'invoice123';
          $this->title = 'Invoice';
          $this->has_fields = true;
          $this->method_title = 'Invoice';
          $this->init_form_fields();
          $this->init_settings();
          add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        function init_form_fields() {
          $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'woocommerce' ),
                'type' => 'checkbox',
                'label' => __( 'Enable', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title' => __( 'Title', 'woocommerce' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default' => __( 'Invoice', 'woocommerce' ),
                'desc_tip'      => true,
            ),
            'description' => array(
                'title' => __( 'Customer Message', 'woocommerce' ),
                'type' => 'textarea',
                'default' => 'Invoice'
            )

        );
        }

        function payment_fields() {
           echo '
                  <input type="text" name="ssn" placeholder="SSN">
                  <p>Write your ssn with 10 digits, </p>
                  <P>yymmddxxxx</p>
                ';
        }

        function is_valid_luhn($number) {
          settype($number, 'string');
          $sumTable = array(
            array(0,1,2,3,4,5,6,7,8,9),
            array(0,2,4,6,8,1,3,5,7,9));
          $sum = 0;
          $flip = 0;
          for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $sum += $sumTable[$flip++ & 0x1][$number[$i]];
          }
          return $sum % 10 === 0;
        }


        function process_payment( $order_id ) {

          global $woocommerce;
          $order = new WC_Order( $order_id );

          if (!empty($_POST['ssn']) && $this->is_valid_luhn($_POST['ssn'])) {

              // Mark as on-hold (we're awaiting the cheque)
              $order->update_status('on-hold', __( 'Awaiting cheque payment', 'woocommerce' ));

              // Reduce stock levels
              $order->reduce_order_stock();

              // Remove cart
              $woocommerce->cart->empty_cart();

              // Return thankyou redirect

              $order->payment_complete();
              return array(
                  'result' => 'success',
                  'redirect' => $this->get_return_url( $order )
              );
              } else {
                $order->update_status('failed', __( 'wrong SSN', 'woocommerce' ));
                wc_add_notice('Payment error: check your SSN','error');return;
              }




        }

      }

  function add_your_gateway_class( $methods ) {
        $methods[] = 'WC_Gateway_Your_Gateway';
        return $methods;
            }

  add_filter( 'woocommerce_payment_gateways', 'add_your_gateway_class' );


}
