<?php

/**
 * Description of PaymentDeleteFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Tests\Form;

use Drupal\simpletest\WebTestBase;
use SmartPan\Tests\TestBundle\Fixture\Environment;
use Smartpan\Tests\TestBundle\Fixture\Payment as PaymentFixture;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;

class PaymentDeleteFormTest extends WebTestBase
{
    public static $modules = array('smartpan_account', 'product_subscription', 'user_subscription', 'braintree', 'smartpan_payment');
    
    private $admin_user;
    private $smartpan_user;
    
    private $environment;
    private $paymentFixture;
    
    private $paymentReceipt;
    
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
            'name' => 'Payment Delete Form Test',
            'description' => 'Test for payment deletion',
            'group' => 'Payment'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        \Drupal::config('braintree.account')
        ->set('name', 'smartpan_sandbox')
        ->save();
        
        $this->smartpan_user = $this->userFixture->createAccount('unamious@ymail.com', 'First Name', 'Last Name');
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $plan = $this->environment->createPlan('basic');
        $subscription = $this->smartpan_user->createSubscription($this->smartpan_user, $plan);
        $order = $this->paymentFixture->createOrder($this->smartpan_user, $subscription, $plan);
        
        $account = \Drupal::config('braintree.account')->get('name');
        
        $this->paymentReceipt = $this->paymentFixture->createPayment($this->smartpan_user, $order, $account);
        
        $this->admin_user = $this->drupalCreateUser(array(
            'administer orders'
        ));
        
        $this->drupalLogin($this->admin_user);
    }
    
    public function providerTestDelete()
    {
        return $this->paymentReceipt->id();
    }
    
    public function testDelete($payment_id)
    {
        $this->drupalPostForm('admin/payment/'.$payment_id.'/delete', array(), 'Confirm');
        
        $payment = entity_load('smartpan_payment', $payment_id);
        
        $this->assertNull($payment, 'Payment has been deleted');
    }
}
