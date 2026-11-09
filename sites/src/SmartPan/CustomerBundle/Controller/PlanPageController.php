<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SmartPan\Bundles\CustomerBundle\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\product_subscription\Controller\PlanController;

/**
 * Description of PlanPageController
 *
 * @author aswinvk28
 */
class PlanPageController extends ControllerBase
{
    public function pricePage()
    {
        $planController = new PlanController();
        $plans = $planController->setTrialPlansOnPaid();
        $keys = array_keys($plans);
        $values = array_values($plans);
        $keys = array_map(function($value) {
            return 'plan_'.$value;
        }, $keys);
        $plans = array_combine($keys, $values);
        $render = array(
            '#theme' => 'plans_and_pricing',
            '#plans' => $plans,
            '#plan_controller' => $planController
        );
        return drupal_render($render);
    }
}

?>
