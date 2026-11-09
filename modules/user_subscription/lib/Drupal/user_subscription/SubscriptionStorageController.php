<?php

/**
 * SubscriptionStorageController deals with storage of subscriptions
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription;

use Drupal\Core\Entity\FieldableDatabaseStorageController;

class SubscriptionStorageController extends FieldableDatabaseStorageController
{
    public function loadMultipleForTaxonomyOptions()
    {
        $entities = $this->loadMultiple();
        $options = array();
        if(!empty($entities)) {
            foreach($entities as $entityId => $entity) {
                $options[$entityId] = taxonomy_term_load($entity->getSubscriptionType())->get('name')->value;
            }
        }
        return $options;
    }
    
    public function _addForeignKeys()
    {
        $this->database->query('ALTER TABLE ' . $this->entityType->getBaseTable() . '');
    }
    
    public function createHistory()
    {
        
    }
    
    public function deleteHistory($time = REQUEST_TIME)
    {
        
    }
}

?>
