<?php

/**
 * Description of OrderController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Controller\ControllerBase;

class OrderController extends ControllerBase
{
    private $paymentController;
    public $storage;
    
    public function __construct()
    {
        $this->storage = $this->entityManager()->getStorageController('smartpan_order');
        $this->paymentController = new PaymentController();
    }
    
    public function delete($entity)
    {
        try {
            $payments = $this->paymentController->storage->loadByProperties(array(
                'order_id' => $entity->id(),
                'status' => 1
            ));

            if(!empty($payments)) {
                $count = count($payments);
                $increment = 0;
                foreach($payments as $entityId => $payment) {
                    if($this->paymentController->storage->softDelete($payment)) {
                        $increment++;
                    }
                }
                if($increment != $count) {
                    drupal_set_message($this->t("Some of the $increment out of $count payments could not be made inactive"));
                }
                drupal_set_message($this->t('The payments under this plan has been made inactive.'));
            }
            
            $entity->delete();

            // @todo Move to storage controller http://drupal.org/node/1988712
            drupal_set_message($this->t('Deleted order %name.', array('%name' => $this->entity->id())));
            watchdog('smartpan', 'Deleted order %name.', array('%name' => $this->entity->id()), WATCHDOG_NOTICE);
            $form_state['redirect_route']['route_name'] = 'payment.order_overview';
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
    
    public function softDelete($entity)
    {
        try {
            $payments = $this->paymentController->storage->loadByProperties(array(
                'order_id' => $entity->id(),
                'status' => 1
            ));

            if(!empty($payments)) {
                $count = count($payments);
                $increment = 0;
                foreach($payments as $entityId => $payment) {
                    if($this->paymentController->softDelete($payment)) {
                        $increment++;
                    }
                }
                if($increment != $count) {
                    drupal_set_message($this->t("Some of the $increment out of $count payments could not be made inactive"));
                }
                drupal_set_message($this->t('The payments under this plan has been made inactive.'));
            }
            
            $entity->set('status', 0);
            
            if(!$entity->save()) {
                throw new \Exception('Order could not be deleted');
            }

            // @todo Move to storage controller http://drupal.org/node/1988712
            drupal_set_message($this->t('Deleted order %name.', array('%name' => $this->entity->id())));
            watchdog('smartpan', 'Deleted order %name.', array('%name' => $this->entity->id()), WATCHDOG_NOTICE);
            $form_state['redirect_route']['route_name'] = 'payment.order_overview';
            Cache::invalidateTags(array('content' => TRUE));
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
    
    public function hardDelete($entity)
    {
        try {
            $payments = $this->paymentController->storage->loadByProperties(array(
                'order_id' => $entity->id(),
                'status' => 1
            ));

            if(!empty($payments)) {
                $this->paymentController->storage->delete($payments);
            }
            
            // @todo Move to storage controller http://drupal.org/node/1988712
            drupal_set_message($this->t('Deleted payments for the order belonging to %name.', array('%name' => $entity->id())));
            watchdog('smartpan', 'Deleted order %name.', array('%name' => $entity->id()), WATCHDOG_NOTICE);
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
}

?>
