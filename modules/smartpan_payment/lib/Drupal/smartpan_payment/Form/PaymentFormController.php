<?php

/**
 * Description of PaymentFormController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Entity\ContentEntityFormController;
use Drupal\Core\Entity\EntityManagerInterface;

class PaymentFormController extends ContentEntityFormController
{
    /**
     * 
     * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
     */
    public function __construct(EntityManagerInterface $entity_manager) {
        parent::__construct($entity_manager);
        $this->paymentMethodOptions = payment_load_multiple_payment_method();
    }
    
    public function form(array $form, array &$form_state) {
        $payment = $this->entity;
        
        if(is_null($this->entity->id())) {
            drupal_set_message('Environment not set up');
            return $form;
        }
        
        $smartpanUser = entity_load('smartpan_user', $payment->getOrder()->get('user_id')->value);
        $drupalUser = entity_load('user', $smartpanUser->get('user_id')->value);
        $braintreeSettings = entity_load('braintree_settings', $payment->get('uid')->value);
        $form['user_name'] = array(
            '#title' => 'User Paid',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => !is_null($payment->get('order_id')->value) ? $drupalUser->getUserName() : 'No user Subscribed'
        );
        $form['account_name'] = array(
            '#title' => 'Braintree Account',
            '#type' => 'html_tag',
            '#tag' => 'span',
            '#value' => !is_null($payment->get('uid')->value) ? $braintreeSettings->id() : 'No Braintree Account set up'
        );
        $form['status'] = array(
            '#title' => 'Order Status',
            '#type' => 'checkbox',
            '#default_value' => !is_null($payment->get('status')->value) ? $payment->get('status')->value : 0
        );
        $form['amount'] = array(
            '#title' => 'Payment Amount',
            '#type' => 'textfield',
            '#attributes' => array(
                'class' => '',
                'disabled' => 'disabled'
            ),
            '#default_value' => !is_null($payment->get('amount')->value) ? $payment->get('amount')->value : ''
        );
        $form['method'] = array(
            '#title' => 'Payment Method',
            '#type' => 'select',
            '#options' => $this->paymentMethodOptions,
            '#default_value' => !is_null($payment->get('method')->value) ? $payment->get('payment_method')->value : '',
            '#required' => TRUE
        );
        $form['data'] = array(
            '#type' => 'value'
        );
        $form['comment'] = array(
            '#type' => 'textarea',
            '#title' => 'Comment on the Payment',
            '#default_value' => !is_null($payment->get('comment')->value) ? $payment->get('comment')->value : '',
        );
        
        return parent::form($form, $form_state, $term);
    }
    
    public function validate(array $form, array &$form_state) {
        parent::validate($form, $form_state);
    }
    
    public function save(array $form, array &$form_state) {
        try {
            if(!$this->entity->save()) {
                throw new \Exception('Plan Not Saved');
            }
        } catch(Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

?>
