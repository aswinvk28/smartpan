<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\account_settings\Form;

use Drupal\Core\Form\FormBase;
use SmartPan\Bundles\AccountBundle\Controller\UserController;

/**
 * Description of PersonalDetailsEditForm
 *
 * @author aswinvk28
 */
class PersonalDetailsEditForm extends FormBase
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
        return 'user_details_form';
    }
    
    public function buildForm(array $form, array &$form_state)
    {
        $form['personal_details'] = array(
            '#type' => 'container',
            '#title' => 'Personal Details'
        );
        
        $form['personal_details'] += account_settings_smartpan_user_details_add_fields($this->smartpanUser);
        
        $form['personal_details']['actions'] = array(
            '#type' => 'actions',
            'submit' => array(
                '#value' => 'Submit',
                '#type' => 'submit',
                '#button_type' => 'primary'
            )
        );
        
        return $form;
    }
    
    public function validateForm(array &$form, array &$form_state)
    {
        
    }
    
    public function submitForm(array &$form, array &$form_state)
    {
        if(is_null($this->smartpanUser)) {
            $this->prepareEntity();
        }
        
        $this->smartpanUser->set('first_name', $form_state['values']['first_name']);
        $this->smartpanUser->set('last_name', $form_state['values']['last_name']);
        
        $transaction = db_transaction();
        try {
            if(!$this->smartpanUser->save()) {
                throw new \Exception('User Details not saved');
            }
        } catch(\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}

?>
