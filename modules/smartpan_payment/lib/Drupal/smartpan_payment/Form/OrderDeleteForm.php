<?php

/**
 * Description of OrderDeleteForm
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Cache\Cache;
use Drupal\smartpan_payment\Controller\OrderController;

class OrderDeleteForm extends ContentEntityConfirmFormBase
{
    public function getFormId() {
        return $this->entity->getEntityTypeId() . '_confirm_delete';
    }
    
    public function getQuestion() {
        return $this->t('Are you sure you want to delete the order %title?', array('%title' => $this->entity->id()));
    }
    
    public function getCancelRoute() {
        return array(
            'route_name' => 'payment.order_overview'
        );
    }
    
    public function getDescription() {
        return $this->t('Deleting an order entity will make the payment receipt inactive.');
    }
    
    public function submit(array $form, array &$form_state) {
        try {
            $orderController = new OrderController();
            
            $status = $orderController->delete($this->entity);
            
            if($status === FALSE) throw new \Exception('Order not deleted');
            
            $form_state['redirect_route']['route_name'] = 'smartpan_payment.order_overview';
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}

?>
