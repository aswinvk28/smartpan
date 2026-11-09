<?php

/**
 * Description of StockProfileAccess
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Access;

use Drupal\Core\Entity\EntityAccessController;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Entity\EntityInterface;

class StockProfileAccess extends EntityAccessController
{
    /**
     * {@inheritdoc}
     */
    protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account)
    {
        switch ($operation) {
          case 'view':
            return $this->viewAccess($entity, $langcode, $account);
            break;
          default:
            break;
        }
    }

    /**
     * 
     * @param Drupal\smartpan_stock\Access\EntityInterface $entity
     * @param type $langcode
     * @param Drupal\smartpan_stock\Access\AccountInterface $account
     * @return boolean
     */
    protected function viewAccess(EntityInterface $entity, $langcode, AccountInterface $drupalUser)
    {
        $account = UserController::getCurrentActiveSmartpanUser($drupalUser);
        $token = $account->getToken();
        if(!empty($token)) {
            $tokenIsActive = $token->isActive();
            if($tokenIsActive === TRUE) {
                return TRUE;
            }
        } elseif ($entity->id()) {
            $subscription = $account->getActiveSubscription();
            $plan = $subscription->getPlan();
            $planId = $plan->id();
            if(!empty($planId)) {
                $planTerm = entity_load('taxonomy_term', $plan->get('type')->value);
                $subscriptionTerm = entity_load('taxonomy_term', $subscription->get('type')->value);
                if(!empty($planTerm) && !empty($subscriptionTerm)) {
                    $subscriptionValidity = $subscription->getValidity();
                    $validityCheckDateTime = new \DateTime();
                    $validityCheckDateTime->setTimestamp(REQUEST_TIME);
                    $validityCheckDateTime->setTimezone(new \DateTimeZone('UTC'));
                    if(!empty($subscriptionValidity) && $subscriptionValidity < $validityCheckDateTime) {
                        if($planTerm->get('name')->value === 'free-trial' && $subscription->bundle() === 'free-trial') {
                            $order = $subscription->getLastCreatedOrder();
                            if(empty($order)) {
                                return TRUE;
                            }
                        } elseif(!is_null($subscriptionTerm->get('name')->value) && $subscriptionTerm->get('name')->value != 'free-trial'
                            && $subscription->bundle() !== 'free-trial') {
                            $order = $subscription->getLastSuccessfulOrder();
                            $payment = $order->getRecentPayment();
                            $duration = entity_load('taxonomy_term', $plan->get('duration')->value);
                            if(!empty($duration) && !empty($order) && !empty($payment)) {
                                $dateIntervalString = '1' . \product_subscription_duration_interval_mapper($duration->get('name')->value);
                                $paymentCreated = $payment->getCreated();
                                if(!empty($paymentCreated)) {
                                    $paymentValidity = $paymentCreated->add(new \DateInterval('P'.$dateIntervalString));
                                    if($paymentValidity < $validityCheckDateTime && $payment->get('status')->value === 1) {
                                        $token = entity_create('account_token', array(
                                            'subscription_id' => $subscription->id(),
                                            'order_id' => $order->id(),
                                            'type' => $planTerm->get('name')->value,
                                            'expiry' => ($subscriptionValidity->getTimestamp() - time()) > 7200 ? 7200 : $subscriptionValidity->getTimestamp() - time()
                                        ));
                                        if($token->save()) {
                                            return TRUE;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return FALSE;
    }
}

?>
