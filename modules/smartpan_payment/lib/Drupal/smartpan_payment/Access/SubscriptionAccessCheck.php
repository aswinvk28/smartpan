<?php

/**
 * Description of SubscriptionAccessCheck
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Access;

use Drupal\Core\Controller\ControllerBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\Access\AccessInterface;

class SubscriptionAccessCheck extends ControllerBase
{
    public function upgradeAccess(Request $request, $plan_machine_name = '')
    {
        $account = UserController::getCurrentActiveSmartpanUser();
        if(!empty($account) && smartpan_account_is_member($account->getDrupalUser())) {
            $subscription = $account->getActiveSubscription();
            $plan = $subscription->getPlan();
            $planId = $plan->id();
            if(!empty($planId)) {
                if(!empty($plan_machine_name)) {
                    $futurePlan = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans($plan_machine_name);
                    if(empty($futurePlan)) {
                        drupal_set_message('Non existing Plan', 'error');
                        return AccessInterface::KILL;
                    }
                    $futurePlanId = current($futurePlan);
                    if($futurePlanId == $planId) {
                        drupal_set_message('Cannot Upgrade to the Current Plan', 'error');
                        return AccessInterface::KILL;
                    }
                }
                $planTerm = entity_load('taxonomy_term', $plan->get('type')->value);
                $subscriptionTerm = entity_load('taxonomy_term', $subscription->get('type')->value);
                if(!empty($planTerm) && !empty($subscriptionTerm)) {
                    $subscriptionValidity = $subscription->getValidity();
                    $validityCheckDateTime = new \DateTime();
                    $validityCheckDateTime->setTimestamp(REQUEST_TIME);
                    $validityCheckDateTime->setTimezone(new \DateTimeZone($account->getDrupalUser()->getTimeZone()));
                    if(!empty($subscriptionValidity) && $validityCheckDateTime < $subscriptionValidity) {
                        if($planTerm->get('name')->value === 'free-trial' && $subscription->bundle() === 'free-trial'
                                && smartpan_account_is_free_member($account->getDrupalUser())) {
                            $order = $subscription->getLastCreatedOrder();
                            if(empty($order)) {
                                return AccessInterface::ALLOW;
                            }
                        } elseif(!is_null($subscriptionTerm->get('name')->value) && $subscriptionTerm->get('name')->value != 'free-trial'
                            && $subscription->bundle() !== 'free-trial'
                            && smartpan_account_is_paid_member($account->getDrupalUser())) {
                            $order = $subscription->getLastSuccessfulOrder();
                            $payment = $order->getRecentPayment();
                            $duration = entity_load('taxonomy_term', $plan->get('duration')->value);
                            if(!empty($duration) && !empty($order) && !empty($payment)) {
                                $dateIntervalString = '1' . \product_subscription_duration_interval_mapper($duration->get('name')->value);
                                $paymentCreated = $payment->getCreated();
                                if(!empty($paymentCreated)) {
                                    $paymentValidity = $paymentCreated->add(new \DateInterval('P'.$dateIntervalString));
                                    if($validityCheckDateTime < $paymentValidity && $payment->get('status')->value == 1) {
                                        return AccessInterface::ALLOW;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return AccessInterface::DENY;
    }
    
    public function createPayment(Request $request, $form_token)
    {
        $access = \Drupal::service('smartpan_account.form_user_token')->accessToken($form_token, 'plan_upgrade_form');
        $data = \Drupal::service('smartpan_account.form_user_token')->getData();
        
        if($this->upgradeAccess($request, $data['plan']) == AccessInterface::ALLOW && $access) {
            return AccessInterface::ALLOW;
        }
        return AccessInterface::KILL;
    }
    
    public function confirmPayment(Request $request, $form_token, $confirm_form_token)
    {
        $confirm_form_access = \Drupal::service('smartpan_account.form_user_token')->accessToken($confirm_form_token, 'order_confirm_form');
        
        if(($this->createPayment($request, $form_token) == AccessInterface::ALLOW) && $confirm_form_access) {
            return AccessInterface::ALLOW;
        }
        return AccessInterface::KILL;
    }
}

?>
