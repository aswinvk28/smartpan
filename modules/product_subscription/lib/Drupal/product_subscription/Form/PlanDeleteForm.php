<?php

/**
 * Description of PlanDeleteForm
 *
 * @author aswinvk28
 */

namespace Drupal\product_subscription\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Cache\Cache;
use Drupal\product_subscription\Controller\PlanController;

class PlanDeleteForm extends ContentEntityConfirmFormBase
{
    public function getFormId() {
        return $this->entity->getEntityTypeId() . '_confirm_delete';
    }
    
    public function getQuestion() {
        return $this->t('Are you sure you want to delete the plan %title?', array('%title' => $this->entity->label()));
    }
    
    public function getCancelRoute() {
        return array(
            'route_name' => 'product_subscription.plan_overview'
        );
    }
    
    public function getDescription() {
        return $this->t('Deleting a plan entity will make the subscriptions under the plan inactive.');
    }
    
    public function submit(array $form, array &$form_state) {
        try {
            $planController = new PlanController;
            
            $status = $planController->delete($this->entity);
            
            if($status === FALSE) throw new \Exception('Plan not deleted');

            $form_state['redirect_route'] = $this->getCancelRoute();
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
        }
    }
}

?>
