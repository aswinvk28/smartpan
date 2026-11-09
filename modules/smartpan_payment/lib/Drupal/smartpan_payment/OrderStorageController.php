<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\smartpan_payment;

use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * OrderStorageController deals with storage of orders
 *
 * @author aswinvk28
 */
class OrderStorageController extends FieldableDatabaseStorageController
{
    public function buildOrderQuery($smartpanUser, $subscription)
    {
        $entityQuery = \Drupal::entityQuery($this->entityTypeId);
        $entityQuery->condition('user_id', $smartpanUser->id());
        $entityQuery->condition('subscription_id', $subscription->id());
        $entityQuery->sort('created', 'DESC');
        $entityQuery->range(0, 1);
        return $entityQuery;
    }
    
    public function buildActiveOrderQuery($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildOrderQuery($smartpanUser, $subscription);
        $entityQuery->condition('status', 1);
        return $entityQuery;
    }
    
    public function fetchActiveOrderForBraintreeSubscription($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildActiveOrderQuery($smartpanUser, $subscription);
        $entityQuery->condition('braintree_subscription_id', NULL, '!=');
        return $entityQuery->execute();
    }
    
    public function fetchLastActiveOrderForRenewal($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildActiveOrderQuery($smartpanUser, $subscription);
        $entityQuery->condition('type', 'renew', '=');
        return $entityQuery->execute();
    }
    
    public function fetchLastActiveOrderForUpgradation($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildActiveOrderQuery($smartpanUser, $subscription);
        $entityQuery->condition('type', 'upgrade', '=');
        return $entityQuery->execute();
    }
    
    public function fetchLastActiveOrderForRegularTransaction($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildActiveOrderQuery($smartpanUser, $subscription);
        $entityQuery->condition('type', 'regular', '=');
        return $entityQuery->execute();
    }
    
    public function fetchLastActiveOrder($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildActiveOrderQuery($smartpanUser, $subscription);
        return $entityQuery->execute();
    }
    
    public function fetchLastCreatedOrder($smartpanUser, $subscription)
    {
        $entityQuery = $this->buildOrderQuery($smartpanUser, $subscription);
        return $entityQuery->execute();
    }
    
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
}

?>
