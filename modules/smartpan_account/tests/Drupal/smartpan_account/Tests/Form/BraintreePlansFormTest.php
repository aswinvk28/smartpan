<?php

/**
 * Description of BraintreePlansFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account\Tests\Form;

use Drupal\simpletest\WebTestBase;
use SmartPan\Tests\TestBundle\Fixture\Environment;

class BraintreePlansFormTest extends WebTestBase
{
    private $admin_user;
    private $planType;
    private $braintreePlan;
    
    private $environment;
    
    public static $modules = array('taxonomy', 'smartpan_account');
    
    public static function getInfo()
    {
        return array(
            'name' => 'Braintree Account Plans test',
            'description' => 'Unit test of braintree account plans form for setting role and plan_type.',
            'group' => 'Braintree'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->environment = Environment::create(\Drupal::getContainer());
        
        $this->admin_user = $this->drupalCreateUser(array(
            'administer braintree'
        ));
        
        $this->drupalLogin($this->admin_user);
        
        $this->environment->createVocab();
        
        $this->planType = entity_create('taxonomy_term', array(
            'vid' => 'plan_types',
            'name' => 'basic_test'
        ));
        $this->planType->save();
        
        $this->braintreePlan = entity_create('braintree_plan', array(
            'machineName' => 'basic_plan_test',
            'type' => NULL,
            'role' => NULL
        ));
        $this->braintreePlan->save();
        
        $this->environment->createRoles();
    }
    
    public function testBraintreeAccountNameSave()
    {
        list($account_name, $roleId) = array('basic_plan_test', SMARTPAN_BASIC_RID);
        $this->drupalPostForm('admin/braintree/plan/'.$account_name.'/edit', array(
            'type' => $this->planType->id(),
            'role' => $roleId
        ), t('Save'));
        
        $this->checkBraintreeAccountNameSave($account_name, $roleId);
    }
    
    public function checkBraintreeAccountNameSave($account_name, $roleId)
    {
        $braintreePlan = entity_load('braintree_plan', $account_name);
        $this->assertNotNull($braintreePlan, 'Braintree Plan Not Empty');
        $this->assertEqual($braintreePlan->role, $roleId, 'Braintree Plan Role matches');
        $this->assertEqual($braintreePlan->type, $this->planType->id(), 'Braintree Plan Plan type matches');
    }
}

?>
