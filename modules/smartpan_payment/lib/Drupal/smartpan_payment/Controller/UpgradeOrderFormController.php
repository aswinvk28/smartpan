<?php

/**
 * Description of UpgradeOrderFormController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

class UpgradeOrderFormController extends FormBase implements ContainerInjectionInterface
{
    private $futurePlan;
    private $currentPlan;
    
    private $entity;
    
    private $formToken;
    
    private $includeCard;
    private $data;
    
    public function getFormId()
    {
        return 'order_transaction_form';
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function prepareTokenAndData()
    {
        $this->formToken = $this->getRequest()->attributes->get('form_token');
        
        $this->data = \Drupal::service('smartpan_account.form_user_token')->getTokenData($this->formToken, 'plan_upgrade_form');
    }
    
    public function prepareEntity()
    {
        return $this->setCurrentUser();
    }
    
    public function init()
    {
        return $this->prepareEntity();
    }
    
    public function setCurrentUser()
    {
        $this->entity = UserController::getCurrentActiveSmartpanUser();
        if(empty($this->entity)) {
            drupal_set_message('No Registration', 'error');
            return FALSE;
        }
        return $this->buildCurrentPlan();
    }
    
    public function buildCurrentPlan()
    {
        $planId = $this->entity->getActiveSubscription()->get('plan_id')->value;
        if(empty($planId)) {
            drupal_set_message('No Valid Current Plan', 'error');
            return FALSE;
        }
        $this->currentPlan = entity_load('plan', $planId);
        return TRUE;
    }
    
    public function buildFuturePlan($plan)
    {
        $plans = \Drupal::entityManager()->getStorageController('plan')->loadPaidPlans($plan);
        if(empty($plans)) {
            drupal_set_message('No Valid Plans', 'error');
            return FALSE;
        }
        $this->futurePlan = entity_load('plan', current($plans));
        if(empty($this->futurePlan)) {
            drupal_set_message('No Valid Plans', 'error');
            return FALSE;
        }
        return TRUE;
    }
    
    private function buildEntity()
    {
        if($this->init()) {
            $this->prepareTokenAndData();
            return $this->buildFuturePlan($this->data['plan']);
        }
        return FALSE;
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        if(!$this->buildEntity()) {
            $render = array('#theme' => 'status_messages');
            return $render;
        }
        
        $form['plan'] = array(
            '#type' => 'value',
        );
        
        $hasCustomerId = $this->entity->get('customer_id')->value;
        $hasToken = $this->entity->get('payment_token')->value;
        $this->includeCard = empty($hasCustomerId) || empty($hasToken);
        if(!$this->includeCard) {
            $form['payment']['payment_info'] = array(
                '#type' => 'html_tag',
                '#tag' => 'div',
                '#value' => 'Confirm Transaction',
                '#attributes' => array(
                    'class' => array('form-item', 'label', 'label-info')
                ),
                '#prefix' => t("Use the saved credit card to confirm the transaction. To specify another card, untick 'Use Saved Card'")
            );
        }
        $form['payment']['include_card'] = array(
            '#type' => 'checkbox',
            '#default_value' => !$this->includeCard,
            '#title' => 'Use Saved Card'
        );
        $form['payment']['include_card']['#attributes']['id'] = 'include-card-form-display';
        $form['payment'] = payment_card_details_add_fields($form, $form_state, 1, FALSE);
        $form['payment']['#type'] = 'container';
        $form['payment']['#states'] = array(
            'visible' => array(
                '#include-card-form-display' => array('checked' => TRUE)
            )
        );
        
        $account = \Drupal::service('braintree_account')->getAccount();
        
        $cse_key = $account['cse_key'];
        
        $form['payment']['braintree'] = array(
            '#type' => 'html_tag',
            '#value' => 'drupalSettings.braintree = {"cse" : "'. $cse_key .'"}',
            '#tag' => 'script',
            '#attributes' => array(
                'type' => 'text/javascript'
            )
        );
        
        drupal_set_title('Pay For ' . $this->futurePlan->planPublicTitle());
        
        $form['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => t('Proceed to Confirmation'),
                '#type' => 'submit',
                '#button_type' => 'primary'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        if(!$this->buildEntity()) {
            $this->setFormError('plan', $form_state, l('Select a Plan', $this->url('smartpan_payment.change_plan')));
        } elseif($this->currentPlan->id() == $this->futurePlan->id()) {
            $this->setFormError('plan', $form_state, 'Cannot upgrade to the Current Plan');
        }
        
        $hasCustomerId = $this->entity->get('customer_id')->value;
        $hasToken = $this->entity->get('payment_token')->value;
        $this->includeCard = empty($hasCustomerId) || empty($hasToken) || empty($form_state['values']['include_card']);
        if($this->includeCard) {
            payment_card_details_validate_fields($form, $form_state);
        }
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        $publicToken = \Drupal::service('smartpan_account.form_user_token')->generatePublicToken($form_state['values']['form_token']);
        
        if($this->includeCard) {
            $payment = array(
                'card_scheme' => $form_state['values']['card_scheme'],
                'credit_card_number' => $form_state['values']['credit_card_number'],
                'credit_card_name' => $form_state['values']['credit_card_name'],
                'expiration_month' => $form_state['values']['expiration_month'],
                'expiration_year' => $form_state['values']['expiration_year'],
                'credit_card_cvv' => $form_state['values']['credit_card_cvv']
            );
        } else {
            $payment = array();
        }
        
        \Drupal::service('smartpan_account.form_user_token')->setFormExpiry(1800)->setTokenData($form_state['values']['form_token'], array(
            'payment' => $payment,
        ), 'order_confirm_form')->finaliseData();
        
        $form_state['redirect_route'] = array(
            'route_name' => 'smartpan_payment.confirm_payment',
            'route_parameters' => array(
                'form_token' => $this->formToken,
                'confirm_form_token' => $publicToken,
            )
        );
    }
}

?>
