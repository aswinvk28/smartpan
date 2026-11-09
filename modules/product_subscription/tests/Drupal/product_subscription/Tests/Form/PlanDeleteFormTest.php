<?php

/**
 * Description of PlanDeleteFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\product_subscription\Tests\Form;

use SmartPan\Tests\TestBundle\Fixture\User as UserFixture;

class PlanDeleteFormTest extends UserFixture
{
    public static $modules = array('taxonomy', 'smartpan_account', 'product_subscription', 'user_subscription');
    
    private $plan;
    private $subscription;
    
    private $admin_user;
    private $smartpan_user;
    
    public static function getInfo()
    {
        return array(
            'name' => 'Plan Delete Form Test',
            'description' => 'test for plan deletion',
            'group' => 'Smartpan Account'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        $this->smartpan_user = $this->createAccount('unamious@ymail.com', 'First Name', 'Last Name');
        $this->register($this->smartpan_user->getDrupalUser());
        
        $this->subscription = $this->smartpan_user->getActiveSubscription();
        $this->plan = $this->subscription->getPlan();
        
        $this->admin_user = $this->drupalCreateUser(array(
            'administer plans'
        ));
        
        $this->drupalLogin($this->admin_user);
    }
    
    public function testDelete()
    {
        $plan_id = $this->plan->id();
        $this->drupalPostForm('admin/plan/'.$plan_id.'/delete', array(), 'Confirm');
        
        $plan = entity_load('plan', $plan_id);
        
        $this->assertNull($plan, 'Plan has been deleted');
        
        $subscriptions = entity_load_multiple_by_properties('subscription', array(
            'plan_id' => $plan_id
        ));
        
        $this->assertEqual($subscriptions, array(), 'Subscription Does not exist');
        
        foreach($subscriptions as $subscription) {
            $this->assertEqual($subscription->get('status')->value, 0, 'Subscription is inactive');
        }
    }
}

?>