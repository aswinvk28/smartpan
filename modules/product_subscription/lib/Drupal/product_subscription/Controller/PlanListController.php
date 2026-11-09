<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\product_subscription\Controller;

use Drupal\Core\Entity\EntityListController;
use Drupal\Core\Entity\EntityInterface;

/**
 * Description of PlanListController
 *
 * @author aswinvk28
 */
class PlanListController extends EntityListController 
{
    /**
     * 
     * @return type
     */
    public function load() {
        return $this->storage->loadMultiple();
    }
    
    public function buildRow(EntityInterface $entity) {
        $term = entity_load('taxonomy_term', $entity->get('type')->value);
        if(!empty($term)) {
            $planType = $term->get('name')->value;
        } else {
            $planType = 'No Type Assigned';
        }
        $row = array(
            'id' => $entity->id(),
            'plan_title' => $entity->get('title')->value,
            'machine_name' => $entity->get('plan_machine_name')->value,
            'plan_type' => $planType,
            'plan_highlight' => $entity->get('highlight')->value
        );
        $row += parent::buildRow($entity);
        return $row;
    }
    
    public function buildHeader() {
        $row = array(
            'id' => $this->t('Plan ID'),
            'plan_title' => $this->t('Plan Title'),
            'machine_name' => $this->t('Machine Name'),
            'plan_type' => $this->t('Plan Type'),
            'plan_highlight' => $this->t('Plan Highlight')
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
        if(isset($operations['edit'])) {
            unset($operations['edit']);
        }
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
        $build['#prefix'] = $this->t('Sync from Braintree here <a href="@link">Sync Braintree Plans</a> Plan Types can be assigned at <a href="@href">Manage Braintree Plans</a>.', array('@href' => url('admin/braintree/plan', array('base path' => TRUE)), '@link' => url('admin/plan/setup', array('base path' => TRUE))));
        return $build;
    }
}

?>
