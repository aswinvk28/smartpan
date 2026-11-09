<?php

/**
 * Description of SmartPanUserProfileController
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Form;

use Drupal\user\ProfileFormController;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

class SmartPanUserProfileController extends ProfileFormController
{
    private $pass_reset = false;
    
    public function submit(array $form, array &$form_state)
    {
        $this->pass_reset = isset($_SESSION['pass_reset_' . $this->entity->id()]) && (\Drupal::request()->query->get('pass-reset-token') == $_SESSION['pass_reset_' . $this->entity->id()]);
        parent::submit($form, $form_state);
    }
    
    public function save(array $form, array &$form_state)
    {
        try {
            try {
                $hasRoles = \smartpan_account_is_member($this->entity);
                $smartpan_user = UserController::getCurrentActiveSmartpanUser($this->entity);
                $subscription = $smartpan_user->getInitialisedSubscription();
            } catch(\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            $transaction = db_transaction();
            try {
                if($this->pass_reset && !$hasRoles && !empty($smartpan_user) && !empty($subscription)) {
                    $subscriptionId = $subscription->id();

                    if(!empty($subscriptionId)) {
                        $subscription->set('status', 1);
                        $subscription->set('created', REQUEST_TIME);
                        $return = $subscription->setValidity();
                        if(empty($return)) {
                            throw new \Exception('Validity Extension not allowed');
                        }
                        $subscription->save();
                    }
                }
            } catch(\Exception $e) {
                $transaction->rollback();
                throw new \Exception($e->getMessage());
            }
            try {
                if(!empty($subscriptionId)) {
                    $plan = $subscription->getPlan();
                    $freeTrialTerm = entity_load('taxonomy_term', $plan->get('type')->value);
                    if($freeTrialTerm->get('name')->value == 'free-trial') {
                        $smartpan_user->updateRole($plan);
                    }
                }
            } catch(\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        } catch (\Exception $ex) {
            drupal_set_message($e->getMessage(), 'error');
        }
        
        parent::save($form, $form_state);
    }
}

?>
