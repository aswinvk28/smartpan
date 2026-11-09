<?php

/**
 * Description of BraintreeActionController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

class BraintreeActionController{
    
    public function createBraintreeTransaction($smartpan_user, $plan, array $creditCard = array(), $billing = false)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeTransaction = $factory->get('Transaction');
        $creationAttributes = array(
            'amount' => $plan->get('price')->value,
        );
        $token = $smartpan_user->get('payment_token')->value;
        $customerId = $smartpan_user->get('customer_id')->value;
        if(!empty($token) && !empty($customerId)) {
            $creationAttributes['paymentMethodToken'] = $token;
            $creationAttributes['customerId'] = $customerId;
        } elseif(!empty($creditCard)) {
            $creationAttributes['creditCard'] = $creditCard;
            $customer = array(
                'firstName' => $smartpan_user->get('first_name')->value,
                'lastName' => $smartpan_user->get('last_name')->value,
                'email' => $smartpan_user->getDrupalUser()->get('mail')->value
            );
            if(!empty($customer)) {
                $creationAttributes['customer'] = $customer;
            }
            if(!empty($billing)) {
                $creationAttributes['billing'] = array(
                    'firstName' => $smartpan_user->get('billing_first_name')->value,
                    'lastName' => $smartpan_user->get('billing_last_name')->value,
                    'company' => $smartpan_user->get('billing_company')->value,
                    'streetAddress' => $smartpan_user->get('billing_street1')->value,
                    'extendedAddress' => $smartpan_user->get('billing_street2')->value,
                    'locality' => $smartpan_user->get('billing_city')->value,
                    'postalCode' => $smartpan_user->get('billing_city')->value,
                    'countryCodeAlpha2' => $smartpan_user->get('billing_country')->value
                );
            }
        } else {
            return FALSE;
        }
        $result = $braintreeTransaction::sale($creationAttributes);
        if($result->success) return $result->transaction;
        return FALSE;
    }
    
    public function createBraintreeSubscription($smartpan_user, $plan, array $values = array())
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeSubscription = $factory->get('Subscription');
        $token = $smartpan_user->get('payment_token')->value;
        if(empty($token)) return FALSE;
        $creationAttributes = array(
            'paymentMethodToken' => $smartpan_user->get('payment_token')->value,
            'planId' => $plan->get('plan_machine_name')->value,
            'options' => array('startImmediately' => true)
        ) + $values;
        $result = $braintreeSubscription::create($creationAttributes);
        if($result->success) return $result->subscription;
        return FALSE;
    }
    
    public function createBraintreeCreditCard($smartpan_user, $customerId, array $creditCard, $billing = false)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeCreditCard = $factory->get('CreditCard');
        $creationAttributes = array(
            'customerId' => $customerId,
            'options' => array(
                'verifyCard' => true
            )
        );
        $creationAttributes += $creditCard;
        if(!empty($billing)) {
            $creationAttributes['billingAddress'] = array(
                'firstName' => $smartpan_user->get('billing_first_name')->value,
                'lastName' => $smartpan_user->get('billing_last_name')->value,
                'company' => $smartpan_user->get('billing_company')->value,
                'streetAddress' => $smartpan_user->get('billing_street1')->value,
                'extendedAddress' => $smartpan_user->get('billing_street2')->value,
                'locality' => $smartpan_user->get('billing_city')->value,
                'postalCode' => $smartpan_user->get('billing_city')->value,
                'countryCodeAlpha2' => $smartpan_user->get('billing_country')->value
            );
        }
        $result = $braintreeCreditCard::create($creationAttributes);
        if($result->success) return $result->creditCard;
        return FALSE;
    }
    
    public function createBraintreeCustomer($smartpan_user)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeCustomer = $factory->get('Customer');
        $creationAttributes = array(
            'firstName' => $smartpan_user->get('first_name')->value,
            'lastName' => $smartpan_user->get('last_name')->value,
            'email' => $smartpan_user->getDrupalUser()->get('mail')->value
        );
        $result = $braintreeCustomer::create($creationAttributes);
        if($result->success) return $result->customer;
        return FALSE;
    }
    
    public function deleteBraintreeSubscription($subscriptionId)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeSubscription = $factory->get('Subscription');
        $result = $braintreeSubscription::cancel($subscriptionId);
        if($result->success) return $result->subscription;
        return FALSE;
    }
    
    public function updateBraintreeSubscription($subscriptionId, $values)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeSubscription = $factory->get('Subscription');
        $result = $braintreeSubscription::update($subscriptionId, $values);
        if($result->success) return $result->subscription;
        return FALSE;
    }
    
    public function deleteBraintreeCard($customerId, $paymentToken)
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeCustomer = $factory->get('Customer');
        $collection = $braintreeCustomer::search(array(
            \Braintree_CustomerSearch::id()->is($customerId),
            \Braintree_CustomerSearch::paymentMethodToken()->is($paymentToken)
        ));
        $customerResult = $collection->current();
        if(!empty($customerResult)) {
            $braintreeCreditCard = $factory->get('CreditCard');
            $result = $braintreeCreditCard::delete($paymentToken);
            if($result->success && $result->isDefault()) return $result->success;
        }
        return FALSE;
    }
    
    public function findBraintreeCreditCard($customerId, $paymentToken) 
    {
        $factory = \Drupal::service('payment.gateway');
        $braintreeCustomer = $factory->get('Customer');
        $collection = $braintreeCustomer::search(array(
            \Braintree_CustomerSearch::id()->is($customerId),
            \Braintree_CustomerSearch::paymentMethodToken()->is($paymentToken)
        ));
        $customerResult = $collection->firstItem();
        if(!empty($customerResult)) {
            $braintreeCreditCard = $factory->get('CreditCard');
            $result = $braintreeCreditCard::find($paymentToken);
            if(!$result->isExpired() && $result->isDefault()) return $result;
        }
        return FALSE;
    }
}

?>
