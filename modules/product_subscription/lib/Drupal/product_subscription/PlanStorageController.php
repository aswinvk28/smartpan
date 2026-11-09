<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\product_subscription;

use Drupal\Core\Entity\FieldableDatabaseStorageController;

/**
 * PlanStorageController deals with storage of plans
 *
 * @author aswinvk28
 */
class PlanStorageController extends FieldableDatabaseStorageController
{
    public function loadMultipleForOptions($field = 'title', $entities = NULL)
    {
        if(empty($entities)) $entities = $this->loadMultiple();
        if(empty($entities)) return FALSE;
        $options = array();
        foreach($entities as $entityId => $entity) {
            $options[$entityId] = $entity->get($field)->value;
        }
        return $options;
    }
    
    public function planFieldOptions($field = 'title', $entities = NULL)
    {
        static $options;
        if(empty($options[$field])) {
            $options[$field] = $this->loadMultipleForOptions($field, $entities);
        }
        return $options[$field];
    }
    
    public function loadPaidPlans($plan_machine_name = '')
    {
        $vocabulary = \Drupal::entityManager()->getStorageController('braintree_plan')->planVocabulary('type');
        $term = taxonomy_term_load_multiple_by_name('free-trial', $vocabulary);
        if(empty($term)) return array();
        $term = current($term);
        $freeTrialTermId = $term->get('tid')->value;
        $entityQuery = \Drupal::entityQuery('plan');
        if(!empty($plan_machine_name)) {
            $entityQuery->condition('plan_machine_name', $plan_machine_name, '=');
        }
        $entityQuery->condition('type', $freeTrialTermId, '!=');
        $entityQuery->condition('price', 0.00, '!=');
        $entityQuery->condition('status', 1, '=');
        return $entityQuery->execute();
    }
    
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
}

?>
