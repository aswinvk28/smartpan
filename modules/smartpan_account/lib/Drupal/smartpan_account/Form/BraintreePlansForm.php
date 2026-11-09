<?php

/**
 * Description of BraintreePlansForm
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_account\Form;

use Drupal\Core\Entity\EntityFormController;

class BraintreePlansForm extends EntityFormController
{
    private $planTypes;
    
    public function __construct()
    {
        $this->planTypes = \Drupal::entityManager()->getStorageController('braintree_plan')->planOptions('type');
        $roles = entity_load_multiple('user_role', array(SMARTPAN_BASIC_RID, SMARTPAN_NORMAL_RID, SMARTPAN_PREMIUM_RID));
        foreach($roles as $roleId => $role) {
            $this->roles[$roleId] = $role->label;
        }
        $vocabulary = \Drupal::entityManager()->getStorageController('braintree_plan')->planVocabulary('type');
        $freeTrialterm = taxonomy_term_load_multiple_by_name('free-trial', $vocabulary);
        if(!empty($freeTrialterm) && isset($this->planTypes[current($freeTrialterm)->get('tid')->value])) {
            unset($this->planTypes[current($freeTrialterm)->id()]);
        }
    }
    
    public function form(array $form, array &$form_state)
    {
        $entity = $this->entity;
        
        $form['machineName'] = array(
            '#prefix' => '<label class="label">'.$this->t('Machine Name').'</label>',
            '#type' => 'html_tag',
            '#value' => !is_null($entity->id()) ? $entity->id() : '',
            '#tag' => 'h3',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            )
        );
        
        $form['type'] = array(
            '#type' => 'select',
            '#title' => $this->t('Plan Category'),
            '#default_value' => !is_null($entity->getType()) ? $entity->getType() : '',
            '#options' => $this->planTypes,
            '#empty_value' => '',
            '#empty_option' => 'None Selected'
        );
        
        $form['role'] = array(
            '#type' => 'select',
            '#title' => $this->t('Role Assigned for this plan'),
            '#default_value' => !is_null($entity->getRole()) ? $entity->getRole() : '',
            '#options' => $this->roles,
            '#empty_value' => '',
            '#empty_option' => 'None Selected'
        );
        
        $form = parent::form($form, $form_state);
        
        return $form;
    }
    
    public function save(array $form, array &$form_state)
    {
        try {
            if(!$this->entity->save()) {
                throw new \Exception('Settings not saved');
            }
        } catch (\Exception $e) {
            drupal_get_message($e->getMessage(), 'error');
        }
    }
}

?>
