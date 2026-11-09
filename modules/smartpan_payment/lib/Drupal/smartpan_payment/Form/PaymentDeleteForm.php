<?php

/**
 * Description of PaymentDeleteForm
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Cache\Cache;

class PaymentDeleteForm extends ContentEntityConfirmFormBase
{
    public function getFormId() {
        return $this->entity->getEntityTypeId() . '_confirm_delete';
    }
    
    public function getQuestion() {
        return $this->t('Are you sure you want to delete the payment %title?', array('%title' => $this->entity->id()));
    }
    
    public function getCancelRoute() {
        return array(
            'route_name' => 'payment.order_overview'
        );
    }
    
    public function getDescription() {
        return $this->t('Deleting a payment receipt will permanently delete it.');
    }
    
    public function submit(array $form, array &$form_state) {
        try {
            $status = $this->entity->delete();
            
            if($status === FALSE) throw new \Exception('Payment not deleted');
            
            $form_state['redirect_route']['route_name'] = 'smartpan_payment.payment_overview';
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
