<?php

/**
 * Description of BraintreeActionController
 *
 * @author aswinvk28
 */

namespace Drupal\braintree\Controller;

class BraintreeActionController 
{
    public function getAllBraintreePlans()
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreePlan = $factory->get('Plan');
        $plans = $braintreePlan::all();
        if(empty($plans)) return NULL;
        return $plans;
    }
}

?>
