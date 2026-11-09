<?php

/**
 * Description of BraintreeAccountManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Manager;

use Symfony\Component\HttpFoundation\ParameterBag;

class BraintreeAccountManager
{
    private $subscriptionId;
    
    public function __construct($smartpanUser, $orderController)
    {
        $this->smartpanUser = $smartpanUser;
        $this->parameterBag = new ParameterBag(array(
            'order_controller' => $orderController
        ));
        $this->subscriptionId = $this->smartpanUser->get('subscription_id')->value;
    }
    
    public function getSubscriptionId()
    {
        return $this->subscriptionId;
    }
    
    public function createOrder($plan, $subscription, $smartpan_user, $currency, $paymentMethod, $braintreeSubscriptionId = '')
    {
        $creationAttributes = array(
            'subscription_id' => $subscription->id(),
            'user_id' => $smartpan_user->id(),
            'product' => $plan->id(),
            'currency' => $currency,
            'payment_method' => $paymentMethod,
            'order_total' => $plan->get('price')->value,
        );
        if(!empty($braintreeSubscriptionId)) {
            $creationAttributes['braintree_subscrition_id'] = $braintreeSubscriptionId;
        }
        $order = entity_create('smartpan_order', $creationAttributes);
        return $order;
    }
    
    public function saveCard($creditCard, &$billing)
    {
        $orderController = $this->parameterBag->get('order_controller');
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        if(!empty($hasCustomerId) && !empty($hasToken)) {
            $orderController->deleteBraintreeCard($hasCustomerId, $hasToken);
            $this->smartpanUser->set('payment_token', NULL);
            $this->smartpanUser->save();
            drupal_set_message('Existing Card Deleted', 'status');
        }
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        if(empty($hasCustomerId) || empty($hasToken)) {
            $resultCustomer = $orderController->createBraintreeCustomer($this->smartpanUser);
            if(!empty($resultCustomer) && !empty($resultCustomer->id)) {
                $hasCustomerId = $resultCustomer->id;
                $this->smartpanUser->set('customer_id', $resultCustomer->id);
                $this->smartpanUser->save();
            } else {
                throw new \Exception('Customer not saved');
            }
        }
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        if(empty($hasToken) && !empty($hasCustomerId)) {
            $resultCard = $orderController->createBraintreeCreditCard(
                $this->smartpanUser, $resultCustomer->id, $creditCard, $billing
            );
            if($billing) $billing = false;
            $hasToken = $resultCard->_attributes['token'];
            if(!empty($hasToken)) {
                $this->smartpanUser->set('payment_token', $hasToken);
                $this->smartpanUser->save();
            } else {
                throw new \Exception('Card not saved');
            }
        }
        return TRUE;
    }
    
    public function saveSubscription()
    {
        $orderController = $this->parameterBag->get('order_controller');
        $hasCustomerId = $this->smartpanUser->get('customer_id')->value;
        $hasToken = $this->smartpanUser->get('payment_token')->value;
        $plan = $this->smartpanUser->getActiveSubscription()->getPlan();
        $planId = $plan->id();
        if($plan->get('validity')->value == 0) return FALSE;
        if(empty($this->subscriptionId)) {
            if(!empty($planId)) {
                $resultSubscription = $orderController->createBraintreeSubscription(
                    $this->smartpanUser, $plan
                );
            }
        } elseif(!empty($hasCustomerId) && !empty($hasToken)) {
            $resultSubscription = $orderController->updateBraintreeSubscription($this->subscriptionId, array(
                'paymentMethodToken' => $hasToken,
                'planId' => $plan->get('plan_machine_name')
            ));
        }
        if(empty($resultSubscription) || empty($resultSubscription->id)) {
            throw new \Exception('Subscription not saved');
        }
        return $resultSubscription;
    }
    
    public function renewSubscription($creditCard = array())
    {
        $orderController = $this->parameterBag->get('order_controller');
        $billing_first_name = $this->smartpanUser->get('billing_first_name')->value;
        $billing_email = $this->smartpanUser->get('billing_email')->value;
        $billing = !empty($billing_first_name) && !empty($billing_email);
        $plan = $this->smartpanUser->getActiveSubscription()->getPlan();
        $planId = $plan->id();
        if(empty($planId)) throw new \Exception('No Plan Initialized');
        $result = $orderController->createBraintreeTransaction(
            $this->smartpanUser, $plan, $creditCard, $billing
        );
        if(empty($result)) throw new \Exception('Transaction Failed');
        return $result;
    }
    
    public function finalisePayment($subscription, $plan, $paymentReceipt, $subscriptionId = NULL)
    {
        $savedStatus = $this->smartpanUser->save();
        if(empty($savedStatus)) {
            throw new \Exception('User not saved');
        }
        $savedStatus = $subscription->save();
        if(empty($savedStatus)) {
            throw new \Exception('Subscription not saved');
        }
        $order = $this->createOrder(
            $plan, $subscription, $this->smartpanUser, 'USD', 'card', $subscriptionId
        );
        $order->set('status', 1);
        $savedStatus = $order->save();
        if(empty($savedStatus)) {
            throw new \Exception('Order not placed');
        }
        $account = \Drupal::service('braintree_account')->getAccount();
        $paymentReceipt->set('order_id', $order->id());
        $paymentReceipt->set('amount', $order->get('order_total')->value);
        $paymentReceipt->set('status', 1);
        $paymentReceipt->set('method', $order->get('payment_method')->value);
        $paymentReceipt->set('account_id', $account['account_name']);
        $savedStatus = $paymentReceipt->save();
        if(empty($savedStatus)) {
            throw new \Exception('Payment Receipt not created');
        }
        $paymentReceipt->setOrder($order);
        return $paymentReceipt;
    }
    
    public function findCard($customerId, $paymentToken) 
    {
        $orderController = $this->parameterBag->get('order_controller');
        return $orderController->findCreditCard($customerId, $paymentToken);
    }
}

?>
