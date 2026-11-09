<?php

/**
 * Description of PaymentControllerTest
 *
 * @author aswinvk28
 */

namespace Drupal\product_subscription\Tests\Controller;

use Drupal\simpletest\DrupalUnitTestBase;
use Drupal\product_subscription\Controller\PlanController;

class PlanControllerTest extends DrupalUnitTestBase
{
    private $planController;
    private $braintreePlans;
    
    public function setUp()
    {
        self::$modules = array('braintree', 'smartpan_account', 'product_subscription');
        parent::setUp();
        $this->planController = new PlanController();
        $plan = \Drupal::service('payment.gateway')->get('Plan');
        $this->braintreePlans = $plan::all();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Plan Controller test',
            'description' => 'Unit test of Plan Controller involving Braintree.',
            'group' => 'Smartpan Environment'
        );
    }
    
    public function testSync()
    {
        $this->assertNotNull($this->braintreePlans, 'Braintree Plans Not Null');
        $this->assertEqual($this->braintreePlans, array(), 'Braintree Plans Empty');
        $this->assertFalse($this->braintreePlans, 'Braintree Plans False');
        
        $this->planController->initialiseFromBraintree($this->braintreePlans);
        
        $terms = taxonomy_term_load_multiple_by_name('free-trial');
        $this->assertEqual($terms, array(), 'Free Trial Term Name Does not exist');
        if(!empty($terms)) {
            $term = current($terms);
            $this->assertEqual('free-trial', $term->get('name')->value, 'Free Trial Term Exists');
        }
        $query = \Drupal::entityQuery('braintree_plan');
        $planSaved = $query->execute();
        $machineNames = array_values($planSaved);
        $braintreeMachineNames = array();
        if(!empty($this->braintreePlans)) {
            foreach($this->braintreePlans as $plan) {
                $braintreeMachineNames[] = $plan->id;
            }
            $this->assertEqual($braintreeMachineNames, $machineNames, 'Machine Names Match');
        }
        
    }
    
    public function provideTestCreationOfPlansFromBraintree()
    {
        return array(
            'basic_plan', 'normal_plan'
        );
    }
    
    public function testCreationOfPlansFromBraintree($plan1, $plan2)
    {
        $planId1 = $this->planController->searchTrialPlanDB(array(
            'plan_machine_name' => $plan1,
            'price' => 0
        ));
        $this->assertNotNull($planId1, 'Plan 1 exists');
        $planId2 = $this->planController->searchPaidPlanDB(array(
            'plan_machine_name' => $plan2
        ));
        $this->assertNotNull($planId2, 'Plan 2 exists');
    }
}

?>
