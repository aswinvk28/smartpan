<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\account_settings\Form;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

/**
 * Description of BillingDetailsEditForm
 *
 * @author root
 */
class BillingDetailsEditForm extends FormBase
{
    private $smartpanUser;
    
    public function __construct()
    {
        $this->prepareEntity();
    }
    
    private function prepareEntity() 
    {
        $this->smartpanUser = UserController::getCurrentActiveSmartpanUser();
    }
    
    public function getFormId()
    {
        return 'billing_details_form';
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $form['billing_details'] = payment_billing_details_add_fields($this->smartpanUser);
        $form['billing_details']['#type'] = 'container';
        $form['billing_details']['#title'] = 'Personal Details';
        
        $form['billing_details']['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => 'Submit',
                '#type' => 'submit',
                '#button_type' => 'primary'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state) {
        payment_billing_details_validate_fields($form, $form_state);
    }
    
    public function submitForm(array &$form, array &$form_state) {
        $this->smartpanUser->set('billing_first_name', $form_state['values']['billing_first_name']);
        $this->smartpanUser->set('billing_last_name', $form_state['values']['billing_last_name']);
        $this->smartpanUser->set('billing_email', $form_state['values']['billing_email']);
        $this->smartpanUser->set('billing_phone', $form_state['values']['billing_phone']);
        $this->smartpanUser->set('billing_country', $form_state['values']['billing_country']);
        $this->smartpanUser->set('billing_company', $form_state['values']['billing_company']);
        $this->smartpanUser->set('billing_street1', $form_state['values']['billing_street1']);
        $this->smartpanUser->set('billing_street2', $form_state['values']['billing_street2']);
        $this->smartpanUser->set('billing_city', $form_state['values']['billing_city']);
        $this->smartpanUser->set('billing_postal_code', $form_state['values']['billing_postal_code']);
        
        $transaction = db_transaction();
        try {
            if(!$this->smartpanuser->save()) {
                throw new \Exception('Billing Details Not Saved');
            }
        } catch(\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}

?>
