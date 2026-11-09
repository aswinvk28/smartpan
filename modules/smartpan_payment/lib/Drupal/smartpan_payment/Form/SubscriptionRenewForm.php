<?php

/**
 * Description of SubscriptionRenewForm
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Component\Utility\Crypt;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\smartpan_payment\Controller\OrderController;
use Drupal\smartpan_payment\Manager\BraintreeAccountManager;

class SubscriptionRenewForm extends FormBase
{
    private $token;
    
    public function getFormId()
    {
        return 'subscription_renew_form';
    }
    
    public function prepareToken()
    {
        $token_param = \Drupal::keyValueExpirable('form-token-salt')->get($this->getRequest()->attributes->get('form_token'));
        if(empty($this->token)) {
            $this->token = Crypt::hmacBase64($this->getRequest()->attributes->get('form_token'), settings()->get('drupal_form_token_salt'));
            if($this->token !== $token_param) {
                drupal_set_message('Not Authorised', 'error');
            }
        }
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $this->prepareToken();
        if(empty($this->token)) {
            drupal_set_message('Not Authorised', 'error');
            return $form;
        }
        $form['token'] = array(
            '#type' => 'value',
            '#value' => $this->token
        );
        
        $form['renew'] = payment_card_details_add_fields($form, $form_state, 1, FALSE);
        
        $form['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => 'Renew Subscription',
                '#type' => 'submit',
                '#button_type' => 'success'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        $this->prepareToken();
        if(empty($form_state['values']['token'])) {
            $this->setFormError('token', 'Not Authorized');
        }
        payment_card_details_validate_fields($form, $form_state);
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        $smartpanUser = UserController::getCurrentActiveSmartpanUser();
        $braintreeController = new BraintreeAccountManager($smartpanUser, new OrderController());
        
        $creditCard = array(
            'number' => $form_state['values']['credit_card_number'],
            'expirationMonth' => $form_state['values']['expiration_month'],
            'expirationYear' => $form_state['values']['expiration_year'],
            'cardholderName' => $form_state['values']['credit_card_name'],
            'cvv' => $form_state['values']['credit_card_cvv']
        );
        
        $paymentReceipt = entity_create('smartpan_payment', array());
        $transaction = db_transaction();
        try {
            $resultTransaction = $braintreeController->renewSubscription($creditCard);
            $plan = NULL;
            if(!empty($resultTransaction->_attributes) && $resultTransaction->_attributes['status'] === 'authorized') {
                $plan = $smartpanUser->getActiveSubscription()->getPlan();
                $braintreeController->finalisePayment(
                    $smartpanUser->getActiveSubscription(), 
                    $plan, $paymentReceipt
                );
                $order = $paymentReceipt->getOrder();
                $order->set('type', 'renew');
                if(!$order->save()) {
                    throw new \Exception('Order not saved');
                }
                $timezone = $smartpanUser->getDrupalUser()->get('timezone')->value;
                $currentSubscription = $smartpanUser->getActiveSubscription();
                $currentSubscription->setValidityForPayment($paymentReceipt, $timezone);
                if(!$currentSubscription->save()) {
                    throw new \Exception('Subscription not saved');
                }
            } else {
                throw new \Exception('Transaction Failed');
            }
            drupal_set_message('Transaction successfully made', 'status');
            if(!empty($plan)) {
                $planPublicTitle = $plan->planPublicTitle();
                drupal_set_message("Renewed the plan: $planPublicTitle", 'status');
            }
            $form_state['redirect_route']['route_name'] = 'smartpan.dashboard';
        } catch(\Exception $e) {
            $transaction->rollback();
            drupal_set_message($e->getMessage(), 'error');
        }
    }
}

?>
