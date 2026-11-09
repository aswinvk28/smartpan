<?php

/**
 * Description of AccountMemberLoginForm
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Form;

use Drupal\user\Form\UserLoginForm;
use Drupal\Core\TypedData\DataDefinition;

class AccountMemberLoginForm extends UserLoginForm
{
    public function buildForm(array $form, array &$form_state)
    {
        $form = parent::buildForm($form, $form_state);
        $form['#theme'] = 'login_container';
        return $form;
    }
    
    /**
     * 
     * @param array $form
     * @param array $form_state
     */
    public function submitForm(array &$form, array &$form_state)
    {
        parent::submitForm($form, $form_state);
        
        $form_state['redirect_route'] = array(
            'route_name' => 'smartpan_account.dashboard'
        );
    }
    
    /**
     * 
     * @param array $form
     * @param array $form_state
     */
    public function validateName(array &$form, array &$form_state)
    {
        parent::validateName($form, $form_state);
        
        $definition = DataDefinition::create('string')
        ->addConstraint('Email', array('checkHost' => true));
        
        $typed_data = \Drupal::typedDataManager()->create($definition, $form_state['values']['name']);
        $violations = $typed_data->validate();
        
        if($violations->count() > 0 && strlen($form_state['values']['name']) < 58) {
            $this->setFormError('name', $form_state, $violations[0]->getMessage());
        }
    }
}

?>
