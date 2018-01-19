<?php

require_once 'braintree-php-3.26.1/lib/Braintree.php';

class myBraintree {


  protected $clientToken;

  function __construct(){


    $this->clientToken = 

  }

  public function Transaction ($amount) {



  }

  public function clientToken() {
    return $this->clientToken;
  }

}
