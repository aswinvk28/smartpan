<?php

/**
 * Description of BraintreePlansListController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListController;
use Drupal\Core\Entity\EntityInterface;

class BraintreePlansListController extends ConfigEntityListController 
{
    /**
     * 
     * @return type
     */
    public function load() {
        return $this->storage->loadMultiple();
    }
    
    public function buildRow(EntityInterface $entity) {
        if(!empty($entity->type)) {
            $term = entity_load('taxonomy_term', $entity->type);
        }
        if(!empty($term)) {
            $planType = $term->get('name')->value;
        } else {
            $planType = 'No Type Assigned';
        }
        $row = array(
            'machine_name' => $entity->id(),
            'plan_type' => $planType
        );
        $row += parent::buildRow($entity);
        return $row;
    }
    
    public function buildHeader() {
        $row = array(
            'machine_name' => $this->t('Plan Machine Name'),
            'plan_type' => $this->t('Plan Type')
        );
        $row += parent::buildHeader();
        return $row;
    }
    
    /**
     * 
     * @param \SmartPan\Bundles\AccountBundle\Controller\EntityInterface $entity
     * @return type
     */
    public function getOperations(EntityInterface $entity) {
        $operations = parent::getOperations($entity);
        if (\Drupal::currentUser()->hasPermission('administer plans') && $entity->hasLinkTemplate('edit-form')) {
            $operations['edit'] = array(
                'title' => $this->t('Edit'),
                'weight' => 10,
            ) + $entity->urlInfo('edit-form');
        }
        return $operations;
    }
    
    public function render() {
        $build = parent::render();
        $build['#empty'] = $this->t('No plans available in braintree.' );
        $build['#prefix'] = $this->t('Sync from Braintree here <a href="@link">Sync Braintree Plans</a>.', array('@link' => url('admin/plan/setup', array('base path' => TRUE))));
        return $build;
    }
}

?>
