<?php

/**
 * Description of BraintreeActionControllerTest
 *
 * @author aswinvk28
 */

namespace Drupal\braintree\Tests\Controller;

use Drupal\simpletest\DrupalUnitTestBase;
use Drupal\braintree\Controller\BraintreeActionController;

class BraintreeActionControllerTest extends DrupalUnitTestBase
{
    public function setUp()
    {
        self::$modules = array('braintree');
        parent::setUp();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Braintree Action Controller test',
            'description' => 'Unit test of Braintree Action Controller.',
            'group' => 'Braintree API'
        );
    }
    
    public function testGetAllBraintreePlans()
    {
        $testPlans = array('basic_plan', 'normal_plan');
        $controller = new BraintreeActionController();
        $plans = $controller->getAllBraintreePlans();
        
        $this->assertNotNull($plans, 'Braintree Plans are not empty');
        if(!empty($plans)) {
            foreach($plans as $plan) {
                $this->assertTrue(in_array($plan->id, $testPlans), "Braintree Plan $plan->id existing");
            }
        }
    }
}

?>
