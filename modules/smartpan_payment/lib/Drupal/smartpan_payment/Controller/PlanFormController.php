<?php

/**
 * Description of PlanFormController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Component\Utility\Crypt;

class PlanFormController extends FormBase implements ContainerInjectionInterface
{
    private $smartpanUser;
    private $currentPlan;
    
    private $plans;
    
    private $entity;
    
    private $includeBilling;
    
    public function getFormId()
    {
        return 'plan_upgrade_form';
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function getSmartpanUser()
    {
        return $this->smartpanUser;
    }
    
    public function init(&$form_state)
    {
        $form_state['controller'] = $this;
        return $this->setCurrentUser() && $this->prepareEntity();
    }
    
    public function setCurrentUser()
    {
        $this->smartpanUser = UserController::getCurrentActiveSmartpanUser();
        if(empty($this->smartpanUser)) {
            drupal_set_message('No Registration', 'error');
            return FALSE;
        }
        return $this->buildCurrentPlan();
    }
    
    public function buildCurrentPlan()
    {
        $this->currentPlan = $this->smartpanUser->getActiveSubscription()->getPlan();
        $planId = $this->currentPlan->id();
        if(empty($planId)) {
            drupal_set_message('No Valid Current Plan', 'error');
            return FALSE;
        }
        return TRUE;
    }
    
    public function prepareEntity()
    {
        $this->entity = entity_create('plan', array());
        $plan_machine_name = $this->getRequest()->attributes->get('plan_machine_name', false);
        if(empty($plan_machine_name)) {
            return $this->setPlanOptions();
        } else {
            $planIds = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans($plan_machine_name);
            if(!empty($planIds)) {
                $this->entity = entity_load('plan', current($planIds));
                return $this->setPlanOptions($planIds);
            }
        }
    }
    
    public function setPlanOptions($planIds = NULL)
    {
        if(empty($planIds)) {
            $planIds = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans();
        }
        if(!empty($planIds)) {
            if(!empty($this->currentPlan)) {
                $key = array_search($this->currentPlan->id(), $planIds);
                if($key !== FALSE) unset($planIds[$key]);
                $planOptions = array();
                foreach($planIds as $planId) {
                    $entity = entity_load('plan', $planId);
                    $planOptions[$entity->get('plan_machine_name')->value] = 
                            $entity->planPublicTitle();
                }
                $this->plans = $planOptions;
                return TRUE;
            }
        }
        if(empty($this->plans)) {
            drupal_set_message('Environment Not set up', 'error');
        }
        return FALSE;
    }
    
    public function buildFuturePlan(&$form_state)
    {
        $plan_machine_name = $this->getRequest()->attributes->get('plan_machine_name', false);
        if(!empty($plan_machine_name)) {
            $form_state['values']['plan'] = $plan_machine_name;
        }
        
        if(!array_key_exists($form_state['values']['plan'], $this->plans)) {
            $this->setFormError('plan', $form_state, 'Invalid Plan');
            return FALSE;
        } else {
            $plans = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans($form_state['values']['plan']);
            $entity = entity_load('plan', key($plans));
            if(empty($entity)) {
                $this->setFormError('plan', $form_state, 'Invalid Plan');
            }
            $this->entity = $entity;
        }
        return TRUE;
    }
    
    public function buildEntity(&$form_state)
    {
        $this->init($form_state);
        $this->buildFuturePlan($form_state);
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        if(!$this->init($form_state)) {
            $render = array('#theme' => 'status_messages');
            return $render;
        }
        
        drupal_add_library('system', 'drupal.states');
        
        $entityId = $this->entity->id();
        if(!empty($entityId)) {
            $form['plan_tag'] = array(
                '#type' => 'html_tag',
                '#title' => 'Plan Selected',
                '#value' => $this->entity->planPublicTitle(),
                '#theme' => 'ajax_field',
                '#tag' => 'span'
            );
            if($entityId == $this->currentPlan->id()) {
                drupal_set_message('Cannot upgrade to current Plan', 'error');
                $render = array('#theme' => 'status_messages');
                return $render;
            }
            $form['plan'] = array(
                '#type' => 'value'
            );
        } else {
            $form['plan'] = array(
                '#type' => 'select',
                '#title' => 'Choose a plan',
                '#options' => $this->plans,
                '#default_value' => '',
                '#required' => TRUE
            );
        }
        
        $form['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => t('Proceed to Payment'),
                '#type' => 'submit',
                '#button_type' => 'primary'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        $this->buildEntity($form_state);
        
        if($this->currentPlan->id() == $this->entity->id()) {
            $this->setFormError('plan', $form_state, 'Cannot upgrade to the Current Plan');
        }
        
        if(empty($form_state['values']['plan'])) {
            $this->setFormError('plan', $form_state, $this->t('Select a Plan'));
        }
        
        $this->includeBilling = !empty($form_state['values']['include_billing'])
            && !empty($form_state['values']['billing_first_name'])
                && !empty($form_state['values']['billing_email']);
        
        if($this->includeBilling) {
            payment_billing_details_validate_fields($form, $form_state);
        }
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        $publicToken = \Drupal::service('smartpan_account.form_user_token')->generatePublicToken($form_state['values']['form_token']);
        
        if(!$this->includeBilling) {
            $billing = array();
        } else {
            $billing = array(
                'billing_first_name' => $form_state['values']['billing_first_name'],
                'billing_last_name' => $form_state['values']['billing_last_name'],
                'billing_email' => $form_state['values']['billing_email'],
                'billing_phone' => $form_state['values']['billing_phone'],
                'billing_company' => $form_state['values']['billing_company'],
                'billing_country' => $form_state['values']['billing_country'],
                'billing_street1' => $form_state['values']['billing_street1'],
                'billing_street2' => $form_state['values']['billing_street2'],
                'billing_city' => $form_state['values']['billing_city'],
                'billing_postal_code' => $form_state['values']['billing_postal_code'],
            );
        }
        
        \Drupal::service('smartpan_account.form_user_token')->setFormExpiry(2400)->setTokenData($form_state['values']['form_token'], array(
            'plan' => $form_state['values']['plan'],
            'billing' => $billing,
        ), 'plan_upgrade_form')->finaliseData();
        
        $form_state['redirect_route'] = array(
            'route_name' => 'smartpan_payment.create_payment',
            'route_parameters' => array(
                'form_token' => $publicToken
            )
        );
    }
}

?>
