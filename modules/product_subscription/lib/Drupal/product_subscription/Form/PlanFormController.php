<?php

/**
 * Description of PlanEntityFormController
 *
 * @author aswinvk28
 */

namespace Drupal\product_subscription\Form;

use Drupal\Core\Entity\ContentEntityFormController;

class PlanFormController extends ContentEntityFormController
{
    /**
     * 
     * @param array $form
     * @param array $form_state
     * @return type
     */
    public function form(array $form, array &$form_state) {
        $plan = $this->entity;
        
        if(empty($plan)) {
            drupal_set_message('Plan Entity Not Initialised', 'error');
            $status_messages = array('#theme' => 'status_messages');
            return drupal_render($status_messages);
        }

        $form = array();
        
        $form['title'] = array(
            '#type' => 'html_tag',
            '#tag' => 'h3',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('title')->value) ? $plan->get('title')->value : ''
        );
        
        $form['type'] = array(
            '#prefix' => '<label class="label">Type:</label>',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('type')->value) && !empty($plan->get('type')->value) 
            && !is_null($planType = entity_load('taxonomy_term', $plan->get('type')->value)) ? 
            $planType->get('name')->value : ''
        );
        
        $form['duration'] = array(
            '#prefix' => '<label class="label">Duration</label>',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('duration')->value) && !empty($plan->get('duration')->value) 
            && !is_null($planDuration = entity_load('taxonomy_term', $plan->get('duration')->value)) ? 
            $planDuration->get('name')->value : ''
        );
        
        $form['currency'] = array(
            '#prefix' => '<label class="label">Currency</label>',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('currency')->value) ? $plan->get('currency')->value : ''
        );
        
        $form['price'] = array(
            '#prefix' => '<label class="label">Price</label>',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('price')->value) ? $plan->get('price')->value : ''
        );
        
        $form['status'] = array(
            '#title' => 'Plan Status',
            '#type' => 'checkbox',
            '#default_value' => !is_null($plan->get('status')->value) ? $plan->get('status')->value : 0
        );
        
        $form['highlight'] = array(
            '#title' => 'Plan Highlight',
            '#type' => 'checkbox',
            '#default_value' => !is_null($plan->get('highlight')->value) ? $plan->get('highlight')->value : 0
        );
        
        $form['description'] = array(
            '#prefix' => '<label class="label">Description:</label>',
            '#type' => 'html_tag',
            '#tag' => 'div',
            '#attributes' => array(
                'disabled' => 'disabled',
                'class' => 'form-item'
            ),
            '#value' => !is_null($plan->get('description')->value) ? $plan->get('description')->value : ''
        );
        
        return parent::form($form, $form_state);
    }
    
    public function save(array $form, array &$form_state) {
        $transaction = db_transaction();
        try {
            if(!$this->entity->save()) {
                throw new \Exception('Plan not saved');
            }
        } catch(\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
}

?>
