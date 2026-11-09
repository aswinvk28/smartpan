<?php

/**
 * Description of SubscriptionDeleteFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Tests\Form;

use Drupal\simpletest\WebTestBase;
use SmartPan\Tests\TestBundle\Controller\Environment;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use Smartpan\Tests\TestBundle\Fixture\Payment as PaymentFixture;

class SubscriptionDeleteFormTest extends WebTestBase
{
    public static $modules = array('smartpan_account', 'product_subscription', 'user_subscription');
    
    private $subscription;
    
    private $admin_user;
    private $member_user;
    
    private $environment;
    private $userFixture;
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->environment = Environment::create(\Drupal::getContainer());
        $this->userFixture = new UserFixture();
        $this->paymentFixture = PaymentFixture::create(\Drupal::getContainer());
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Subscription Delete Form Test',
            'description' => 'test for subscription deletion',
            'group' => 'Smartpan Account'
        );
    }
    
    public function setUp()
    {
        $this->smartpan_user = $this->userFixture->createAccount('unamious@ymail.com', 'First Name', 'Last Name');
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $plan = $this->environment->createPlan();
        
        $this->subscription = $this->environment->createSubscription($this->smartpan_user, $plan);
        
        $order = $this->paymentFixture->createOrder($this->smartpan_user, $subscription, $plan);
        
        $this->admin_user = $this->drupalCreateUser(array(
            'administer plans'
        ));
        
        $this->drupalLogin($this->admin_user);
    }
    
    public function providerTestDelete()
    {
        return $this->subscription->id();
    }
    
    public function testDelete($subscription_id)
    {
        $this->drupalPostForm('admin/subscription/'.$subscription_id.'/delete', array(), 'Confirm');
        
        $subscription = entity_load('subscription', $subscription_id);
        
        $this->assertNull($subscription, 'Subscription has been deleted');
        
        $order = entity_load_multiple_by_properties('order', array(
            'subscription_id' => $subscription_id
        ));
        $this->assertEqual($order, array(), 'Orders under the subscription have been deleted');
        
        $payment = entity_load_multiple_by_properties('order', array(
            'user_id' => $this->smartpan_user->id()
        ));
        $this->assertEqual($payment, array(), 'Payments under the user have been deleted');
    }
}
