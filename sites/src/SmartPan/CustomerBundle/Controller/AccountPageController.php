<?php

/**
 * Description of AccountPageController
 *
 * @author aswinvk28
 */

namespace SmartPan\Bundles\CustomerBundle\Controller;

use Drupal\Core\Controller\ControllerBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountPageController extends ControllerBase
{
    public function accountSettings(Request $request)
    {
        $smartpanUser = UserController::getCurrentActiveSmartpanUser();
        if(empty($smartpanUser)) {
            drupal_set_message('No User Registration', 'error');
            $render = array('#theme' => 'status_messages');
            return drupal_render($render);
        }
        $view = entity_view($smartpanUser, 'account');
        
        return drupal_render($view);
    }
    
    public function loginPage(Request $request)
    {
        $user = $this->currentUser();
        if ($user->id()) {
            $response = $this->redirect('user.view', array('user' => $user->id()));
        }
        else {
            $form_builder = $this->formBuilder();
            $form_builder->setRequest($request);
            $response = $form_builder->getForm('Drupal\user\Form\UserLoginForm');
        }
        return $response;
    }
    
    public function accountCreatePage(Request $request)
    {
        $user = UserController::getDrupalUser();
        if ($user->id()) {
            $response = $this->redirect('user.view', array('user' => $user->id()));
        } else {
            $user = entity_create('user');
            $response = $this->entityFormBuilder()->getForm($user, 'subscribe');
        }
        return $response;
    }
    
    public function getDashboard(Request $request)
    {
        $user = $this->currentUser();
        if ($user->id()) {
            $render = array(
                '#theme' => 'dashboard',
                '#title' => 'Dashboard'
            );
            return drupal_render($render);
        }
        drupal_set_message('Please log in to the dashboard', 'status');
        return $this->redirect('smartpan_account.login');
    }
    
    public function upgradePlan(Request $request)
    {
        $user = $this->currentUser();
        if ($user->id()) {
            $smartpanUser = UserController::getCurrentActiveSmartpanUser();
            $subscription = $smartpanUser->getActiveSubscription();
            if($subscription->id()) {
                $controller = $this->entityManager()->getFormController('subscription', 'upgrade');
                $form_builder = $this->formBuilder();
                $form_builder->setRequest($request);
                $response = $form_builder->getForm($controller);
                return $response;
            }
        }
    }
    
    public function confirmOrder(Request $request)
    {
        $user = $this->currentUser();
        if ($user->id()) {
            $smartpanUser = UserController::getCurrentActiveSmartpanUser();
            $subscription = $smartpanUser->getActiveSubscription();
            if($subscription->id()) {
                
                $controller = $this->entityManager()->getFormController('smartpan_order', 'order_confirm');
                
                $initResponse = $controller->init();
        
                if($initResponse !== TRUE && $initResponse instanceof Response) {
                    return $initResponse;
                }
                
                $form_builder = $this->formBuilder();
                $form_builder->setRequest($request);
                $response = $form_builder->getForm($controller);
                return $response;
            }
        }
    }
    
    public function confirmPayment(Request $request)
    {
        $user = $this->currentUser();
        if ($user->id()) {
            $smartpanUser = UserController::getCurrentActiveSmartpanUser();
            $subscription = $smartpanUser->getActiveSubscription();
            if($subscription->id()) {
                $controller = $this->entityManager()->getFormController('smartpan_payment', 'payment_confirm');
                
                $initResponse = $controller->init();
        
                if($initResponse !== TRUE && $initResponse instanceof Response) {
                    return $initResponse;
                }
                
                $form_builder = $this->formBuilder();
                $form_builder->setRequest($request);
                $response = $form_builder->getForm($controller);
                return $response;
            }
        }
    }
}

?>
