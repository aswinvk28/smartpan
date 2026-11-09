<?php

namespace Drupal\braintree\Factory;

use Drupal\braintree\BraintreeAccount;
use CometCult\BraintreeBundle\Factory\BraintreeFactory;

class PaymentGatewayFactory 
{
    private static $instance;
    
    public function __construct(BraintreeAccount $account)
    {
        $this->account = $account->getAccount();
    }
    
    /**
     * 
     * @return \CometCult\BraintreeBundle\Factory\BraintreeFactory
     */
    public function getPaymentGateway()
    {
        if(is_null(self::$instance)) {
            self::$instance = new BraintreeFactory(
                $this->account['environment'], $this->account['merchant_id'], 
                $this->account['public_key'], $this->account['private_key']
            );
        }
        return self::$instance;
    }
}

?>
