<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\smartpan_payment;

use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * PaymentStorageController deals with storage of payments
 *
 * @author aswinvk28
 */
class PaymentStorageController extends FieldableDatabaseStorageController
{
    public function fetchRecentPayment($order)
    {
        $entityQuery = \Drupal::entityQuery($this->entityTypeId);
        $entityQuery->condition('status', 1);
        $entityQuery->condition('order_id', $order->id());
        $entityQuery->sort('received', 'DESC');
        $entityQuery->range(0, 1);
        return $entityQuery->execute();
    }
    
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
}

?>
