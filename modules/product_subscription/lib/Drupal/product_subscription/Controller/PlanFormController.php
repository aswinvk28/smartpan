<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\product_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\braintree\Controller\BraintreeActionController;

/**
 * Description of PlanFormController
 *
 * @author aswinvk28
 */
class PlanFormController extends ControllerBase
{
    public function sync()
    {
        $controller = new BraintreeActionController();
        $plans = $controller->getAllBraintreePlans();

        $planController = new PlanController();

        $response = $planController->initialiseFromBraintree($plans);

        if($response instanceof Response) {
            return $response;
        }
        
        $render = array(
            '#theme' => 'table',
            '#prefix' => '<h4>' . $this->t('Plans synced now is listed below. Go to <a href="@link">List Plans</a> to have a view of the list of plans setup in DB', array('@link' => url('admin/plan/list', array('base path' => TRUE)))) . '</h4>',
            '#rows' => $response,
            '#header' => array(t('Plan Machine Name'), t('Plan Title')),
            '#empty' => $this->t('Plan Sync did not work. <a href="@link">Try later after rectifying the problem</a>.', array('@link' => url('admin/plan/setup', array('base path' => TRUE)))),
        );
        return drupal_render($render);
    }
}

?>
