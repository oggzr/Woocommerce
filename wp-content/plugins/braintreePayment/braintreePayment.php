<?php
/*
Plugin Name: Braintree Payment
*/




add_action( 'plugins_loaded', 'init_klarna_gateway' );

  function init_klarna_gateway() {
      class WC_Gateway_Your_Klarna extends WC_Payment_Gateway {

        protected $brainTree;

        function __construct() {
            $this->id = 'klarna1337';
            $this->title = 'Braintree';
            $this->has_fields = true;
            $this->method_title = 'Braintree';
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
            )
        );
        }

        private function getApi () {
          require_once('braintree-php-3.26.1/lib/Braintree.php');
          try {
            Braintree_Configuration::environment('sandbox');
            Braintree_Configuration::merchantId('pwtj44444328w96g');
            Braintree_Configuration::publicKey('cw8tc3576pvpfxq8');
            Braintree_Configuration::privateKey('a4581ac805e1891803b9041d87e80e63');
            return true;
          }catch (Exception $e) {
            return false;
          }
        }

        function payment_fields() {
          $this->getApi();
          $clientToken = Braintree_ClientToken::generate();
          echo '<div id="dropin-container"></div>
                <button id="submit-button">Request payment method</button>
                <input type="hidden" name="my-nonce" id="my-nonce"/>
                <script>
                  var button = document.querySelector("#submit-button");

                  braintree.dropin.create({
                    authorization: "'.$clientToken.'",
                    container: "#dropin-container"
                  }, function (createErr, instance) {
                    button.addEventListener("click", function (e) {
                      e.preventDefault();
                      instance.requestPaymentMethod(function (err, payload) {
                        // Submit payload.nonce to your server
                        document.querySelector("#my-nonce").value = payload.nonce;
                      });
                    });
                  });
                </script>';

        }





        function process_payment( $order_id ) {



          global $woocommerce;
          $order = new WC_Order( $order_id );

          $braintreeErrArr=array(
    				'number' => __('Please check the credit card number.', 'woocommerce'),
    				'cvv' => __('Please check CVV number.', 'woocommerce'),
    				'expirationMonth' => __('Please check credit card expiration month.', 'woocommerce'),
    				'expirationYear' => __('Please check credit card expiration year.', 'woocommerce'),
    				'empty' => __('Please fill in the credit card details.', 'woocommerce'),
    				'check' => __('Please check your credit card details.', 'woocommerce'),
    			);

          if(empty($_POST['my-nonce'])) {
            return wc_add_notice('Failed Payment', 'error');
          }



          $total = $order->get_total();

          $this->getApi();

          $result = Braintree_Transaction::sale([
              'amount' => $total,
              'paymentMethodNonce' => $_POST['my-nonce'],
              'options' => [ 'submitForSettlement' => true ]
          ]);


          if ($result->success) {

    			    echo("Success! Transaction ID: " . $result->transaction->id);
    				// Payment complete
    				$order->payment_complete( $result->transaction->id );
    				// Add order note
    				$order->add_order_note( sprintf( __( '%s payment approved! Transaction ID: %s', 'woocommerce' ), $this->title, $result->transaction->id ) );
    				// Remove cart
    				WC()->cart->empty_cart();
    				// Return thank you page redirect
    				return array(
    					'result'   => 'success',
    					'redirect' => $this->get_return_url( $order )
    				);

        }
      }
    }

  function add_klarna( $methods ) {
        $methods[] = 'WC_Gateway_Your_Klarna';
        return $methods;
            }

  add_filter( 'woocommerce_payment_gateways', 'add_klarna' );


}
