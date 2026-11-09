<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\product_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Description of PaymentController
 *
 * @author aswinvk28
 */
class PlanController extends ControllerBase
{
    private $storage;
    private $storageSetup;
    
    private $vocabulary = array();
    
    private $planTypes = array();
    
    private $planDurations = array();
    
    private $planSaved = array();
    
    private $planDB;
    
    private $subscriptionController;
    
    public function __construct()
    {
        $this->storage = $this->entityManager()->getStorageController('plan');
        $this->storageSetup = $this->entityManager()->getStorageController('braintree_plan');
        if($this->moduleHandler()->moduleExists('user_subscription')) {
            $this->subscriptionController = new \Drupal\user_subscription\Controller\SubscriptionController();
        }
    }
    
    public function initialiseFromBraintree($braintreePlans)
    {
        if(empty($braintreePlans)) return FALSE;
        
        $this->updateEntries($braintreePlans);
        
        $this->planSaved = entity_load_multiple('braintree_plan', NULL, TRUE);
        
        if(empty($this->planSaved)) {
            drupal_set_message("The Braintree Plans are not saved", 'warning');
            $response = $this->redirect('smartpan_account.braintree_plan');
            return $response;
        }
        
        foreach($this->planSaved as $key => $braintreePlan) {
            if(empty($braintreePlan->type)) {
                drupal_set_message("Update the plan type for the Braintree Plan - $key", 'warning');
                $response = $this->redirect('smartpan_account.braintree_plan');
                return $response;
            }
        }
        
        return $this->sync($braintreePlans);
    }
    
    public function getPlanFromDatabase() 
    {
        if(is_null($this->planDB)) {
            $this->planDB = $this->storage->loadMultiple();
        }
        return $this->planDB;
    }
    
    public function getPublishedPlanFromDatabase()
    {
        static $planDB;
        if(!isset($planDB)) {
            $planDB = $this->getPlanFromDatabase();
            if(!empty($planDB)) {
                foreach($planDB as $planId => $plan) {
                    if($plan->get('status')->value != 1) {
                        unset($planDB[$planId]);
                    }
                }
            }
        }
        return $planDB;
    }
    
    public function setTrialPlansOnPaid() 
    {
        $planDB = $this->getPublishedPlanFromDatabase();
        $paidPlanIds = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans();
        $plans = entity_load_multiple('plan', $paidPlanIds);
        $return = array();
        foreach($plans as $planId => $plan) {
            $plans[$planId]->freeTrial = false;
            if(array_key_exists($planId, $planDB)) {
                $planDB[$planId]->freeTrial = false;
            }
            if(!empty($planDB[$planId])) {
                $trialPlanId = $this->searchTrialPlanDB(array(
                    'plan_machine_name' => $plan->get('plan_machine_name')->value,
                    'price' => 0.00
                ));
                if(!empty($trialPlanId)) {
                    $plans[$planId]->freeTrial = $planId;
                    if(array_key_exists($planId, $planDB)) {
                        $planDB[$planId]->freeTrial = $planId;
                        $return[$planId] = $planDB[$planId];
                    }
                }
            }
        }
        return $return;
    }
    
    public function getPaidOnlyPlans()
    {
        if(empty($this->planDB)) return NULL;
        $plans = array();
        foreach($this->planDB as $planId => $plan) {
            if(empty($plan->freeTrial)) {
                $plans[$planId] = $plan;
            }
        }
        return $plans;
    }
    
    public function getPaidWithTrialPlans()
    {
        if(empty($this->planDB)) return NULL;
        $plans = array();
        foreach($this->planDB as $planId => $plan) {
            if(!empty($plan->freeTrial)) {
                $plans[$planId] = $plan;
            }
        }
        return $plans;
    }
    
    protected function sync($braintreePlans)
    {
        $this->planTypes = $this->storageSetup->planOptions('type', true);
        $this->planDurations = $this->storageSetup->planOptions('duration', true);
        
        $plans = $this->getPlanFromDatabase();
        
        $syncedPlans = array();
        
        foreach($braintreePlans as $key => $plan) {
            if(!empty($plan->price) && $plan->price > 0) {
                $planEntityId = $this->searchPaidPlanDB(array(
                    'plan_machine_name' => $plan->id
                ));
                $planStorage = $this->syncPaidIndividualPlan($plan, $planEntityId);
                if(!empty($planStorage)) {
                    $syncedPlans[] = array($planStorage->get('plan_machine_name')->value, $planStorage->get('title')->value);
                    if(!array_key_exists($planStorage->id(), $plans)) {
                        $plans[$planStorage->id()] = $planStorage;
                    }
                }
            }
            if($plan->trialPeriod) {
                $planEntityId = $this->searchTrialPlanDB(array(
                    'plan_machine_name' => $plan->id,
                    'price' => 0.00
                ));
                $planStorage = $this->syncTrialIndividualPlan($plan, $planEntityId);
                if(!empty($planStorage)) {
                    $syncedPlans[] = array($planStorage->get('plan_machine_name')->value, $planStorage->get('title')->value);
                    if(!array_key_exists($planStorage->id(), $plans)) {
                        $plans[$planStorage->id()] = $planStorage;
                    }
                }
            }
        }
        
        // Delete plans where machine name exist in database but does not exist in Braintree
        $braintreePlanMachineNames = array();
        if(!empty($syncedPlans)) {
            $braintreePlanMachineNames = array_map(function($element) {
                return $element[0];
            }, $syncedPlans);
        }
        $this->handleDeletionOfDatabasePlans($plans, $braintreePlanMachineNames);
        
        return $syncedPlans;
    }
    
    public function searchHighlightedPlanDB($values)
    {
        $plans = $this->getPublishedPlanFromDatabase();
        if(!empty($plans)) {
            $return = array();
            foreach($plans as $planId => $plan) {
                if((float) $plan->get('price')->value == (float) $values['price']
                    && $plan->get('type')->value == $values['type']
                    && $plan->get('highlight')->value == 1) {
                    $return[$planId] = $plan;
                }
            }
            return $return;
        }
        return NULL;
    }
    
    public function searchPlanTypePlanDB($values)
    {
        $plans = $this->getPublishedPlanFromDatabase();
        if(!empty($plans)) {
            $return = array();
            foreach($plans as $planId => $plan) {
                if($plan->get('type')->value == $values['type']) {
                    $return[$planId] = $plan;
                }
            }
            return $return;
        }
        return NULL;
    }
    
    public function searchPlanMachineNameTrialPlanDB($plans)
    {
        $planDBs = $this->getPublishedPlanFromDatabase();
        if(!empty($plans) && !empty($planDBs)) {
            $planTrials = array();
            foreach($planDBs as $planDBId => $planDB) {
                foreach($plans as $planId => $plan) {
                    if(!is_null($planTrialId = $this->searchTrialPlanDB(
                        array(
                            'plan_machine_name' => $plan->get('plan_machine_name')->value,
                            'price' => 0.00
                        ), $planDBs)
                    )) {
                        $planTrials[$planTrialId] = $plan;
                    }
                }
            }
            return $planTrials;
        }
        return NULL;
    }
    
    public function searchTrialPlanDB($values, $plans = NULL)
    {
        if(empty($plans)) {
            $plans = $this->getPlanFromDatabase();
        }
        if(!empty($plans)) {
            foreach($plans as $planId => $plan) {
                if($plan->get('plan_machine_name')->value == $values['plan_machine_name'] && (float) $plan->get('price')->value == (float) $values['price']) {
                    return $planId;
                }
            }
        }
        return NULL;
    }
    
    public function searchPaidPlanDB($values)
    {
        if(empty($plans)) {
            $plans = $this->getPlanFromDatabase();
        }
        if(!empty($plans)) {
            foreach($plans as $planId => $plan) {
                if($plan->get('plan_machine_name')->value == $values['plan_machine_name'] && $plan->get('price')->value > 0) {
                    return $planId;
                }
            }
        }
        return NULL;
    }
    
    protected function syncTrialIndividualPlan($plan, $planEntityId = NULL)
    {
        if($plan->trialPeriod) {
            $plans = $this->getPlanFromDatabase();
            $planStorage = entity_create('plan', array(
                'plan_machine_name' => $plan->id,
                'title' => $plan->name,
                'price' => 0.00,
                'type' => $this->handleTrialPlanType('free-trial'),
                'duration' => $this->handlePlanDurationUnit($plan->trialDurationUnit),
                'description' => $plan->description,
                'validity' => $this->handleTrialDuration($plan)
            ));
            if(!empty($planEntityId)) {
                $planStorage->set('id', $planEntityId);
                $planStorage->set('uuid', $plans[$planEntityId]->get('uuid')->value);
                $planStorage->set('status', $plans[$planEntityId]->get('status')->value);
            }
            
            $savedStatus = $planStorage->save();
            if(empty($savedStatus)) return FALSE;
            return $planStorage;
        }
        return FALSE;
    }
    
    protected function syncPaidIndividualPlan($plan, $planEntityId = NULL)
    {
        if(!empty($plan->price) && $plan->price > 0) {
            $planConfig = \Drupal::config('smartpan_account.braintree_plan.' . $plan->id);
            $plans = $this->getPlanFromDatabase();
            $planStorage = entity_create('plan', array(
                'plan_machine_name' => $plan->id,
                'title' => $plan->name,
                'price' => $plan->price,
                'type' => $this->handlePlanType($planConfig),
                'duration' => $this->handlePlanDurationUnit('month'),
                'description' => $plan->description,
                'validity' => $this->handleBillingCycle($plan)
            ));
            if(!empty($planEntityId)) {
                $planStorage->set('id', $planEntityId);
                $planStorage->set('uuid', $plans[$planEntityId]->get('uuid')->value);
                $planStorage->set('status', $plans[$planEntityId]->get('status')->value);
            }
            
            $savedStatus = $planStorage->save();
            if(empty($savedStatus)) return FALSE;
            return $planStorage;
        }
        return FALSE;
    }
    
    public function createTaxonomy($vocab, $name)
    {
        $taxonomy_term = entity_create('taxonomy_term', array(
            'vid' => $vocab,
            'name' => $name,
            'weight' => 0
        ));
        
        if(!$taxonomy_term->save()) {
            return FALSE;
        }
        
        return $taxonomy_term;
    }
    
    public function handleTaxonomy($needle, &$haystack, $vocab)
    {
        if(empty($haystack) || !in_array($needle, $haystack)) {
            $term = $this->createTaxonomy($vocab, $needle);
            if(!is_array($haystack)) $haystack = array();
            if(empty($term)) {
                drupal_set_message("Taxonomy term for '$needle' not saved", 'error');
            } else {
                $haystack[$term->id()] = $term->get('name')->value;
            }
        }
    }
    
    private function handleDeletionOfDatabasePlans($plans, $braintreePlanMachineNames)
    {
        foreach($plans as $planEntityId => $plan) {
            if(!in_array($plan->get('plan_machine_name')->value, $braintreePlanMachineNames)) {
                $plans[$planEntityId]->delete();
            }
        }
    }
    
    public function handleBraintreePlans($toCreate)
    {
        if(!empty($toCreate)) {
            foreach($toCreate as $machineName) {
                $braintreePlan = entity_create('braintree_plan', array(
                    'machineName' => $machineName
                ));
                if(!$braintreePlan->save()) {
                    drupal_set_message("Braintree Config for '$machineName' not saved", 'error');
                }
            }
        }
    }
    
    public function deleteBraintreePlans($toDelete)
    {
        if(!empty($toDelete)) {
            foreach($toDelete as $machineName) {
                $braintreePlan = entity_create('braintree_plan', array(
                    'machineName' => $machineName
                ));
                $braintreePlan->delete();
            }
            drupal_set_message("Braintree Plans Deleted", 'status');
        }
    }
    
    public function updateEntries($braintreePlans)
    {
        $this->planTypes = $this->storageSetup->planOptions('type');
        $this->planDurations = $this->storageSetup->planOptions('duration');
        
        $query = \Drupal::entityQuery('braintree_plan');
        $planSaved = $query->execute();
        $machineNames = array_values($planSaved);
        $braintreeMachineNames = array();
        $this->handleTaxonomy('month', $this->planDurations, $this->storageSetup->planVocabulary('duration'));
        
        foreach($braintreePlans as $key => $plan) {
            $braintreeMachineNames[] = $plan->id;
        }
        
        $toDelete = array_diff($machineNames, $braintreeMachineNames);
        $toCreate = array_diff($braintreeMachineNames, $machineNames);
        
        $this->deleteBraintreePlans($toDelete);
        $this->handleBraintreePlans($toCreate);
        
        foreach($braintreePlans as $key => $plan) {
            $this->handleTaxonomy($plan->trialDurationUnit, $this->planDurations, $this->storageSetup->planVocabulary('duration'));
            if(!empty($plan->trialPeriod)) {
                $this->handleTaxonomy('free-trial', $this->planTypes, $this->storageSetup->planVocabulary('type'));
            }
        }
    }
    
    public function handleBillingCycle($plan)
    {
        if(!empty($plan->numberOfBillingCycles) && is_int($plan->numberOfBillingCycles)) {
            return $plan->numberOfBillingCycles;
        }
        return 0;
    }
    
    public function handlePlanDurationUnit($needle)
    {
        $key = array_search($needle, $this->planDurations);
        if($key === FALSE) return NULL;
        return $key;
    }
    
    public function handleTrialDuration($plan)
    {
        return $plan->trialDuration;
    }
    
    public function handleTrialPlanType($needle)
    {
        $key = array_search($needle, $this->planTypes);
        if($key === FALSE) return NULL;
        return $key;
    }
    
    public function handlePlanType($planConfig)
    {
        return $planConfig->get('type');
    }
    
    public function getAllPublishedPlansFromPlanType($planType)
    {
        return entity_load_multiple_by_properties('plan', array(
            'type' => $planType,
            'status' => 1
        ));
    }
    
    public function delete($entity)
    {
        try {

            $entity->delete();

            // @todo Move to storage controller http://drupal.org/node/1988712
            drupal_set_message(
                $this->t('Deleted plan with title: %name, with plan_id: %plan_id, with plan_machine_name: %plan_machine_name', 
                    array('%name' => $entity->label(), '%plan_id' => $entity->id(), '%plan_machine_name' => $entity->get('plan_machine_name')->value))
            );
            watchdog('smartpan', 'Deleted plan %name.', array('%name' => $entity->label()), WATCHDOG_NOTICE);
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
}

?>
