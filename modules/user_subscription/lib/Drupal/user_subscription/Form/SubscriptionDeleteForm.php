<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\user_subscription\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Cache\Cache;
use Drupal\user_subscription\Controller\SubscriptionController;

/**
 * Description of SubscriptionDeleteForm
 *
 * @author aswinvk28
 */
class SubscriptionDeleteForm extends ContentEntityConfirmFormBase
{
    public function getFormId() {
        return $this->entity->getEntityTypeId() . '_confirm_delete';
    }
    
    public function getQuestion() {
        return $this->t('Are you sure you want to delete the subscription %title?', array('%title' => $this->entity->id()));
    }
    
    public function getCancelRoute() {
        return array(
            'route_name' => 'smartpan.subscription_overview'
        );
    }
    
    public function getDescription() {
        return $this->t('Deleting a subscription entity will permenantly delete the orders under the subscription.');
    }
    
    public function submit(array $form, array &$form_state) {
        try {
            $subscriptionController = new SubscriptionController;
            
            $status = $subscriptionController->hardDelete($this->entity);
            
            if($status === FALSE) throw new \Exception('Subscription not deleted');
            
            $form_state['redirect_route']['route_name'] = 'smartpan.subscription_overview';
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

?>
