<?php

/**
 * Description of CreditCardAddForm
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Form\FormBase;
use Drupal\smartpan_payment\Manager\BraintreeAccountManager;
use SmartPan\Bundles\AccountBundle\Controller\UserController;
use Drupal\smartpan_payment\Controller\BraintreeActionController;

class CreditCardAddForm extends FormBase
{
    public function getFormId()
    {
        return 'card_add_form';
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $smartpan_user = UserController::getCurrentActiveSmartpanUser();
        
        $customerId = $smartpan_user->get('customer_id')->value;
        $token = $smartpan_user->get('payment_token')->value;
        if(!empty($customerId) && !empty($token)) {
            drupal_set_message('Card Exists. Saving card will delete the existing card', 'status');
            $form['card'] = payment_card_details_ajax_field($smartpan_user);
            $form['card']['#type'] = 'container';
            $form['card']['#container_type'] = 'no_form';
        } else {
            drupal_set_message('Card Does not Exist', 'status');
        }
        
        $form['payment'] = payment_card_details_add_fields($form, $form_state, 1, FALSE);
        $form['payment']['#type'] = 'container';
        $form['payment']['#container_type'] = 'no_form';
        
        $form['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => 'Add and Save Card',
                '#type' => 'submit',
                '#button_type' => 'success'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        payment_card_details_validate_fields($form, $form_state);
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        $smartpanUser = UserController::getCurrentActiveSmartpanUser();
        $subscriptionId = $smartpanUser->get('subscription_id')->value;
        $accountController = new BraintreeAccountManager($smartpanUser, new BraintreeActionController());
        $creditCard = array(
            'number' => $form_state['values']['credit_card_number'],
            'expirationMonth' => $form_state['values']['expiration_month'],
            'expirationYear' => $form_state['values']['expiration_year'],
            'cardholderName' => $form_state['values']['credit_card_name'],
            'cvv' => $form_state['values']['credit_card_cvv']
        );
        $transaction = db_transaction();
        try {
            $billing = array();
            $accountController->saveCard($creditCard, $billing);
            if(!empty($resultCard)) {
                $resultSubscription = $accountController->saveSubscription();
                if(!empty($resultSubscription) && !empty($resultSubscription->transactions)) {
                    $resultTransaction = $resultSubscription->transactions[0];
                    $subscriptionId = $resultSubscription->subscription->id;
                }
            }
            drupal_set_message('Card Saved', 'status');
        } catch(\Exception $e) {
            $transaction->rollback();
            drupal_set_message($e->getMessage(), 'error');
        }
    }
}

?>
