<?php

/**
 * Description of Environment
 *
 * @author aswinvk28
 */

namespace SmartPan\Tests\TestBundle\Fixture;

use Drupal\Core\Controller\ControllerBase;

class Environment extends ControllerBase
{
    public static function getSmartpanUserFromDrupalUser($drupalUser)
    {
        $smartpan_user = entity_load_multiple_by_properties('smartpan_user', array(
            'user_id' => $drupalUser->id()
        ));
        if(empty($smartpan_user)) return FALSE;
        $smartpanUser = current($smartpan_user);
        $smartpanUser->setDrupaluser($drupalUser);
        return $smartpanUser;
    }
    
    public function createPlan($type = 'free-trial', $plan_machine_name = 'test_plan_type')
    {
        $vid_type = \Drupal::entityManager()->getStorageController('braintree_plan')->planVocabulary('type');
        $planType = entity_create('taxonomy_term', array(
            'name' => $type,
            'vid' => $vid_type
        ));
        $planType->save();
        
        $vid_duration = \Drupal::entityManager()->getStorageController('braintree_plan')->planVocabulary('duration');
        $planDuration = entity_create('taxonomy_term', array(
            'name' => 'month',
            'vid' => $vid_duration
        ));
        $planDuration->save();
        
        $plan = entity_create('plan', array(
            'plan_machine_name' => $plan_machine_name,
            'title' => 'Test Plan',
            'price' => 0.00,
            'type' => $planType->id(),
            'duration' => $planDuration->id(),
            'status' => 1,
            'highlight' => 1,
            'validity' => 0
        ));
        if($plan->save()) {
            return $plan;
        }
        return NULL;
    }
    
    public function createSubscription($smartpan_user, $plan)
    {
        $vid_type = \Drupal::entityManager()->getStorageController('braintree_plan')->planVocabulary('type');
        $subscriptionType = entity_create('taxonomy_term', array(
            'name' => 'free-trial',
            'vid' => $vid_type
        ));
        $subscriptionType->save();
        
        $subscription = entity_create('subscription', array(
            'plan_id' => $plan->id(),
            'type' => $subscriptionType->id(),
            'user_id' => $smartpan_user->id(),
            'status' => 1
        ));
        if($subscription->save()) {
            return $subscription;
        }
        return NULL;
    }
    
    public function createRoles()
    {
        $basic_role = entity_create('user_role', array(
            'id' => SMARTPAN_BASIC_RID,
            'label' => 'Smartpan Basic'
        ));
        $basic_role->save();
        
        $normal_role = entity_create('user_role', array(
            'id' => SMARTPAN_NORMAL_RID,
            'label' => 'Smartpan Normal'
        ));
        $normal_role->save();
        
        $premium_role = entity_create('user_role', array(
            'id' => SMARTPAN_PREMIUM_RID,
            'label' => 'Smartpan Premium'
        ));
        $premium_role->save();
    }
    
    public function createVocab()
    {
        $plan_type_vocabulary = entity_create('taxonomy_vocabulary', array(
            'vid' => 'plan_types',
            'name' => 'Test Plan Types'
        ));
        $plan_type_vocabulary->save();
        
        $plan_duration_vocabulary = entity_create('taxonomy_vocabulary', array(
            'vid' => 'plan_duration_types',
            'name' => 'Test Duration Types'
        ));
        $plan_duration_vocabulary->save();
    }
}

?>