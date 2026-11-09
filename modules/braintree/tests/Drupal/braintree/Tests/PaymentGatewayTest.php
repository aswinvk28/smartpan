<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\braintree\Tests;

use Drupal\Tests\UnitTestCase;
use CometCult\BraintreeBundle\Factory\BraintreeFactory;

class PaymentGatewayTest extends UnitTestCase
{
    protected $account;
    
    public static function getInfo()
    {
        return array(
            'name' => 'Payment Gateway test',
            'description' => 'Unit test of Payment Gateway.',
            'group' => 'Braintree'
        );
    }
    
    public function providerTestGatewayInstance()
    {
        return array(
            array(
                'account_name' => 'smartpan_sandbox',
                'environment' => 'sandbox',
                'merchant_id' => 'y5tfyrfj8sszhtgf',
                'public_key' => 'q58q4hhpczygz8km',
                'private_key' => '3daa380ccdc759a062d99be986763fd6',
                'cse_key' => 'MIIBCgKCAQEAqq69a7dQ8zLodKjk5FrwL7dUr32ht+I3JmhoAZdqvWs0RsQnBFZr+cxub2adGpbqqlYVPeR+wB/gkp0JCXVQc3Eqfpy0fxGwqNyYkoZIhLzNbqDuSg+9JMu10sZ/ZJP4tWu9GL9kLNTtpGe2bDG9VEL9mx6YSI7MVyxy7Z9l3lD5n8pllsXHk4N4VT1UuugeKt8vSRfL2EvJRbi97wlT97qPw6a1FREhEZdq1s5Bivo2m6nbu3TG+Wz8HqH9OmataLj6PR8w0jzLHUx1/HhvOv7e0FrdGtvZuWrp9QZ6wD++ZY4/eR+hwHBTOxZbG2+tgmGJ5dWNtcFwO+QojmtwvwIDAQAB'
            )
        );
    }
    
    public function testGatewayInstance($account)
    {
        $this->account = $this->getMock('Drupal\braintree\BraintreeAccount');
        $this->account->expects($this->any())
            ->method('getAccount')
            ->will($this->returnValue($account));
        
        $braintreeFactoryMock = $this->getMock('CometCult\BraintreeBundle\Factory\BraintreeFactory')
            ->expects($this->once());
        
        $gatewayFactory = $this->getMock('Drupal\braintree\Factory\PaymentGatewayFactory');
        $gatewayFactory->expects($this->once())
            ->method('getPaymentGateway')
            ->will($this->returnValue($braintreeFactoryMock));
        
        $this->assertTrue($gatewayFactory->getPaymentGateway() instanceof BraintreeFactory, "Instance of CometCult\BraintreeBundle\Factory\BraintreeFactory");
    }
}

?>
