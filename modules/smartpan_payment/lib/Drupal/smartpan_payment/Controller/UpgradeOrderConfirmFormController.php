<?php

/**
 * Description of UpgradeOrderConfirmFormController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\smartpan_payment\Controller\BraintreeActionController;
use Drupal\smartpan_payment\Manager\BraintreeAccountManager;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\user_subscription\Controller\SubscriptionController;

class UpgradeOrderConfirmFormController extends ConfirmFormBase implements ContainerInjectionInterface
{
    private $currentPlan;
    private $futurePlan;
    private $currentSubscription;
    private $paymentReceipt;
    
    private $formToken;
    private $confirmFormToken;
    
    private $entity;
    private $formData;
    private $confirmFormData;
    
    public function getFormId()
    {
        return 'order_transaction_confirm_form';
    }
    
    private function prepareEntity()
    {
        return $this->setCurrentUser();
    }
    
    public function prepareTokenAndData()
    {
        $this->formToken = $this->getRequest()->get('form_token');
        $this->confirmFormToken = $this->getRequest()->get('confirm_form_token');
        
        $this->formData = \Drupal::service('smartpan_account.form_user_token')->getTokenData($this->formToken, 'plan_upgrade_form');
        $this->confirmFormData = \Drupal::service('smartpan_account.form_user_token')->getTokenData($this->confirmFormToken, 'order_confirm_form');
    }
    
    public function init()
    {
        return $this->prepareEntity();
    }
    
    private function setCurrentUser()
    {
        $this->entity = UserController::getCurrentActiveSmartpanUser();
        if(empty($this->entity)) {
            drupal_set_message('No Registration', 'error');
            return FALSE;
        }
        return $this->buildCurrentPlan();
    }
    
    private function buildEntity()
    {
        if($this->init()) {
            $this->prepareTokenAndData();
            $this->buildPaymentReceipt();
            return $this->buildFuturePlan($this->formData['plan']) && $this->buildSubscription();
        }
        return FALSE;
    }
    
    private function buildCurrentPlan()
    {
        $this->currentPlan = $this->entity->getActiveSubscription()->getPlan();
        $planId = $this->currentPlan->id();
        if(empty($planId)) {
            drupal_set_message('No Valid Current Plan', 'error');
            return FALSE;
        }
        return TRUE;
    }
    
    private function buildFuturePlan($plan)
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
    
    private function buildSubscription()
    {
        $this->currentSubscription = clone $this->entity->getActiveSubscription();
        $this->currentSubscription->set('plan_id', $this->futurePlan->id());
        $this->currentSubscription->set('type', $this->futurePlan->get('type')->value);
        $planTypeTerm = entity_load('taxonomy_term', $this->futurePlan->get('type')->value);
        $this->currentSubscription->set('bundle', $planTypeTerm->get('name')->value);
        return TRUE;
    }
    
    private function buildPaymentReceipt()
    {
        $this->paymentReceipt = entity_create('smartpan_payment', array(
            'user_id' => $this->entity->id()
        ));
    }
    
    public function buildSmartpanUser($form_data)
    {
        $this->entity->set('billing_first_name', $form_data['billing_first_name']);
        $this->entity->set('billing_last_name', $form_data['billing_last_name']);
        $this->entity->set('billing_email', $form_data['billing_email']);
        $this->entity->set('billing_phone', $form_data['billing_phone']);
        $this->entity->set('billing_company', $form_data['billing_company']);
        $this->entity->set('billing_country', $form_data['billing_country']);
        $this->entity->set('billing_street1', $form_data['billing_street1']);
        $this->entity->set('billing_street2', $form_data['billing_street2']);
        $this->entity->set('billing_city', $form_data['billing_city']);
        $this->entity->set('billing_postal_code', $form_data['billing_postal_code']);
    }
    
    private function buildRows()
    {
        $rows = array();
        if(empty($this->currentPlan) || empty($this->futurePlan) || empty($this->currentSubscription)) {
            return $rows;
        }
        $durationTerm = entity_load('taxonomy_term', $this->futurePlan->get('duration')->value);
        if(empty($durationTerm)) throw new \Exception('Environment not set up');
        $rows[] = array(
            'id' => $this->entity->getDrupalUser()->getUsername(),
            'plan_title' => $this->futurePlan->get('title')->value,
            'plan_price' => $this->futurePlan->planPriceMapping(),
            'plan_duration' => $this->futurePlan->planValidityMapping()
        );
        return $rows;
    }
    
    private function buildHeader()
    {
        $row = array(
            'id' => $this->t('User Name'),
            'plan_title' => $this->t('Plan Title'),
            'plan_price' => $this->t('Plan Price'),
            'plan_duration' => $this->t('Plan Duration')
        );
        return $row;
    }
    
    public function getDescription()
    {
        $table = array(
            '#theme' => 'table',
            '#header' => $this->buildHeader(),
            '#attributes' => array(
                'class' => array('table', 'btn', 'disabled')
            ),
            '#title' => $this->getTitle(),
            '#rows' => $this->buildRows(),
            '#empty' => $this->t('An error has occured and there are no Payments selected.'),
        );
        return drupal_render($table);
    }
    
    public function getCancelRoute()
    {
        return array(
            'route_name' => 'smartpan_payment.change_plan'
        );
    }
    
    public function getConfirmText()
    {
        return t('Confirm Payment');
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        if(!$this->buildEntity()) {
            $render = array('#theme' => 'status_messages');
            return $render;
        }
        
        $form = parent::buildForm($form, $form_state);
        
        $form['plan'] = array(
            '#type' => 'value',
        );
        
        $form['actions']['submit']['#button_type'] = 'primary';
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        if(!$this->buildEntity()) {
            $this->setFormError('plan', $form_state, 'Error with form submission');
        }
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        $this->save($form, $form_state);
    }
    
    public function save(array $form, array &$form_state)
    {
        $transaction = \Drupal::database()->startTransaction();
        
        $billing = !empty($form_state['include_billing']) && !empty($form_state['values']['billing_first_name'])
                && !empty($form_state['values']['billing_email']);
        
        $hasCustomerId = $this->entity->get('customer_id')->value;
        $hasToken = $this->entity->get('payment_token')->value;
        $subscriptionId = $this->entity->get('subscription_id')->value;
        
        if(!empty($this->confirmFormData['payment'])) {
            $includeCard = empty($hasCustomerId) || empty($hasToken);
        } else {
            $includeCard = false;
        }
        
        $saveCard = empty($hasCustomerId) || empty($hasToken);
        
        try {
            if($billing) {
                if(!empty($this->formData['billing'])) {
                    $this->buildSmartpanUser($this->formData['billing']);
                    if(!$this->entity->save()) {
                        throw new \Exception('User not saved');
                    }
                }
            }
            $orderController = new BraintreeActionController();
            
            $resultTransaction = NULL;
            $creditCard = array();
            $accountController = new BraintreeAccountManager($this->entity, $orderController);
            if($saveCard || $includeCard) {
                $creditCard = array(
                    'number' => $this->confirmFormData['payment']['credit_card_number'],
                    'expirationMonth' => $this->confirmFormData['payment']['expiration_month'],
                    'expirationYear' => $this->confirmFormData['payment']['expiration_year'],
                    'cardholderName' => $this->confirmFormData['payment']['credit_card_name'],
                    'cvv' => $this->confirmFormData['payment']['credit_card_cvv']
                );
            }
            if($saveCard) {
                $resultCard = $accountController->saveCard($creditCard, $billing);
                if(!empty($resultCard)) {
                    $resultSubscription = $accountController->saveSubscription();
                    if(!empty($resultSubscription) && !empty($resultSubscription->transactions)) {
                        $resultTransaction = $resultSubscription->transactions[0];
                        $subscriptionId = $resultSubscription->id;
                    }
                }
            }
            if(empty($resultTransaction)) {
                $resultTransaction = $orderController->createBraintreeTransaction(
                    $this->entity, $this->futurePlan, $creditCard, $billing
                );
                if(empty($resultTransaction)) {
                    throw new \Exception('Transaction Failed');
                }
            }
            if(!empty($resultTransaction->_attributes) && $resultTransaction->_attributes['status'] === 'authorized') {
                if(!empty($subscriptionId) && !empty($resultSubscription)) {
                    $this->currentSubscription->set('status', 
                    smartpan_map_braintree_subscription_status($resultSubscription->subscription->status));
                } else {
                    $this->currentSubscription->set('status', \Drupal\user_subscription\Entity\Subscription::ACTIVE);
                }
                $accountController->finalisePayment(
                    $this->currentSubscription, $this->futurePlan, 
                    $this->paymentReceipt, $subscriptionId
                );
                $order = $this->paymentReceipt->getOrder();
                $orderType = $this->currentPlan->isUpgrade($this->futurePlan) ? 'upgrade' : 'downgrade';
                $order->set('type', $orderType);
                if(!$order->save()) {
                    throw new \Exception('Order not saved');
                }
                $timezone = $this->entity->getDrupalUser()->getTimeZone();
                $this->currentSubscription->setValidityForPayment($this->paymentReceipt, $timezone);
                if(!$this->currentSubscription->save()) {
                    throw new \Exception('Subscription not saved');
                }
                $this->entity->updateRoleForPayment($this->futurePlan)->save();
            } else {
                throw new \Exception('Transaction Failed');
            }
            
            $planPublicTitle = $this->futurePlan->planPublicTitle();
            
            drupal_set_message('Transaction successfully made', 'status');
            drupal_set_message("Upgraded to $planPublicTitle", 'status');
            
            $form_state['redirect_route']['route_name'] = 'smartpan_account.dashboard';
        } catch (\Exception $e) {
            $transaction->rollback();
            drupal_set_message($e->getMessage(), 'error');
        }
    }
    
    public function getQuestion() 
    {
        return $this->t('Confirm Payment for %title?', array('%title' => $this->futurePlan->get('title')->value));
    }
    
    public function getTitle()
    {
        return 'Payment Details';
    }
}

?>
