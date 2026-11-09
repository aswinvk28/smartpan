<?php

/**
 * Description of PlanUpgradeFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Tests\Form;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use SmartPan\Tests\TestBundle\Fixture\Environment;

class PlanUpgradeFormTest extends WebTest
{
    private $userFixture;
    private $environment;
    
    private $smartpan_user;
    
    
    public static $modules = array('smartpan_account', 'product_subscription', 'braintree', 'user_subscription', 'smartpan_payment');
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        
        $this->userFixture = new UserFixture();
        $this->environment = Environment::create(\Drupal::getContainer());
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Upgrade/Downgrade Plan',
            'description' => 'Unit test of plan upgradation or downgradation.',
            'group' => 'Payment'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->environment->createPlan('basic', 'basic_plan');
        $this->environment->createPlan('normal', 'normal_plan');
        
        $this->smartpan_user = $this->userFixture->createAccount();
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $this->smartpanLogin($this->smartpan_user->getDrupalUser());
    }
    
    public function providerTestUpgrade()
    {
        return 'normal_plan';
    }
    
    public function testUpgrade($plan_machine_name)
    {
        $form_1 = $this->userFixture->upgradePlan($plan_machine_name);
        $form_2 = $this->userFixture->confirmOrder($form_1);
        $this->userFixture->confirmPayment($form_2);
    }
}
