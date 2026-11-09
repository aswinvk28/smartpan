<?php

/**
 * Description of OrderDeleteFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Tests\Form;

use Drupal\simpletest\WebTestBase;
use SmartPan\Tests\TestBundle\Fixture\Environment;
use Smartpan\Tests\TestBundle\Fixture\Payment as PaymentFixture;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;

class OrderDeleteFormTest extends WebTestBase
{
    public static $modules = array('smartpan_account', 'product_subscription', 'user_subscription', 'braintree', 'smartpan_payment');
    
    private $admin_user;
    private $smartpan_user;
    
    private $environment;
    private $paymentFixture;
    
    private $order;
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->environment = Environment::create(\Drupal::getContainer());
        $this->paymentFixture = PaymentFixture::create(\Drupal::getContainer());
        $this->userFixture = new UserFixture();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Order Delete Form Test',
            'description' => 'Test for order deletion',
            'group' => 'Payment'
        );
    }
    
    public function setUp()
    {
        $this->smartpan_user = $this->userFixture->createAccount('unamious@ymail.com', 'First Name', 'Last Name');
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $plan = $this->environment->createPlan('basic');
        $subscription = $this->smartpan_user->createSubscription($this->smartpan_user, $plan);
        
        $this->order = $this->paymentFixture->createOrder($this->smartpan_user, $subscription, $plan);
        
        $this->admin_user = $this->drupalCreateUser(array(
            'administer orders'
        ));
        
        $this->drupalLogin($this->admin_user);
    }
    
    public function providerTestDelete()
    {
        return $this->order->id();
    }
    
    public function testDelete($order_id)
    {
        $this->drupalPostForm('admin/order/'.$order_id.'/delete', array(), 'Confirm');
        
        $order = entity_load('smartpan_order', $order_id);
        
        $this->assertNull($order, 'Order has been deleted');
        
        $payments = entity_load_multiple_by_properties('subscription', array(
            'order_id' => $order_id
        ));
        
        $this->assertEqual($payments, array(), 'Payments Do not exist');
        
        foreach($payments as $payment) {
            $this->assertEqual($payment->get('status')->value, 0, 'Payment Receipt is inactive');
        }
    }
    
    
}
