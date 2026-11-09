<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\user_subscription\Form;

use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Description of SubscriptionFormController
 *
 * @author root
 */
class SubscriptionFormController extends ContentEntityFormController
{
    private $subscriptionTypes;
    private $planTitles;
    private $subscriptionBundles;
    
    public function __construct(EntityManagerInterface $entity_manager)
    {
        parent::__construct($entity_manager);
        $this->subscriptionTypes = $this->entityManager->getStorageController('braintree_plan')->planOptions('type');
        $this->planTitles = $this->entityManager->getStorageController('plan')->planFieldOptions('title');
        $this->subscriptionBundles = $this->entityManager->getStorageController('braintree_plan')->planValues('type');
    }
    
    public function form(array $form, array &$form_state) {
        $subscription = $this->entity;
        
        if(empty($subscription)) {
            drupal_set_message('No User Subscription for this ID', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }

        $smartpan_user = entity_load('smartpan_user', $subscription->get('user_id')->value);
        $drupal_user = entity_load('user', $smartpan_user->get('user_id')->value);
        
        $form['user_name'] = array(
            '#title' => 'User',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled'
            ),
            '#value' => !is_null($subscription->get('user_id')->value) ? $drupal_user->get('name')->value : 'No user Subscribed'
        );
        
        if(is_null($subscription->get('user_id')->value)) {
            drupal_set_message('No User Subscription', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        $form['plan_id'] = array(
            '#title' => 'The Plan',
            '#type' => 'select',
            '#empty_option' => 'None selected',
            '#empty_value' => '',
            '#options' => $this->planTitles,
            '#default_value' => !is_null($subscription->get('plan_id')->value) ? $subscription->get('plan_id')->value : '',
            '#required' => TRUE
        );
        
        if(is_null($subscription->get('plan_id')->value)) {
            drupal_set_message('No Plans Associated with the Subscription', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        $form['type'] = array(
            '#title' => 'Subscription Type',
            '#type' => 'select',
            '#empty_option' => 'None selected',
            '#empty_value' => '',
            '#options' => $this->subscriptionTypes,
            '#default_value' => !is_null($subscription->get('type')->value) ? $subscription->get('type')->value : ''
        );
        
        $form['bundle'] = array(
            '#title' => 'Subscription Bundle',
            '#type' => 'select',
            '#empty_option' => 'None selected',
            '#empty_value' => '',
            '#options' => $this->subscriptionBundles,
            '#default_value' => !is_null($subscription->bundle()) ? $subscription->bundle() : ''
        );
        
        $form['status'] = array(
            '#title' => 'Subscription Status',
            '#type' => 'checkbox',
            '#default_value' => !is_null($subscription->get('status')->value) ? $subscription->get('status')->value : 0
        );
        
        $form['validity'] = array(
            '#title' => 'Subscription Validity',
            '#type' => 'date',
            '#default_value' => !is_null($subscription->get('validity')->value) ? $subscription->get('validity')->value : '',
            '#required' => TRUE,
            '#theme' => 'date'
        );
        
        if(is_null($subscription->get('validity')->value)) {
            drupal_set_message('No Date of Validity Set', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        $form['timezone'] = array(
            '#title' => 'Timezone For Subscription Validity',
            '#type' => 'select',
            '#default_value' => 'UTC',
            '#options' => system_time_zones(true),
            '#required' => TRUE
        );
        
        $form['renew'] = array(
            '#title' => 'Subscripion Renew',
            '#type' => 'checkbox',
            '#default_value' => !is_null($subscription->get('renew')->value) ? $subscription->get('renew')->value : 0
        );
        
        if(is_null($subscription->get('renew')->value)) {
            drupal_set_message('No Creation Timestamp for Subscription', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }
        
        return parent::form($form, $form_state);
    }
    
    public function validate(array $form, array &$form_state) {
        parent::validate($form, $form_state);
        
        $subscription = $this->entity;
        $user_id = $subscription->get('user_id');
        if(empty($user_id)) {
            drupal_set_message('Invalid User ID / User ID Not set', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_redner($status_messages);
        }
        $plan_id = $subscription->get('plan_id');
        if(empty($plan_id)) {
            $this->setFormError('plan_id', $form_state, 'Invalid Plan ID / Plan ID Not set');
        }
        $created = $subscription->get('created');
        if(empty($created) || $created < REQUEST_TIME) {
            $this->setFormError('created', $form_state, 'Invalid Creation Timestamp');
        }
        
        if($form_state['values']['status'] != 0 && $form_state['values']['status'] != 1) {
            $this->setFormError('status', $form_state, 'Invalid value for the status of the subscription');
        }
        
        if($form_state['values']['renew'] != 0 && $form_state['values']['renew'] != 1) {
            $this->setFormError('renew', $form_state, 'Invalid value for the auto-renewal of the subscription');
        }
    }
    
    public function save(array $form, array &$form_state) {
        try {
            $this->entity->setValidityWithTimezone($form_state['values']['timezone']);
            if(!$this->entity->save()) {
                throw new \Exception('Subscription Not Saved');
            }
        } catch(Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

?>
