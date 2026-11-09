<?php

/**
 * Description of SubscriptionController
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Controller;

use Drupal\Core\Controller\ControllerBase;

class SubscriptionController extends ControllerBase
{
    private $orderController;
    public $storage;
    
    public function __construct()
    {
        $this->storage = $this->entityManager()->getStorageController('subscription');
        if($this->moduleHandler()->moduleExists('smartpan_payment')) {
            $this->orderController = new \Drupal\smartpan_payment\Controller\OrderController();
        }
    }
    
    public function delete($entity)
    {
        try {
            if(!empty($this->orderController)) {
                $orders = $this->orderController->storage->loadByProperties(array(
                    'subscription_id' => $entity->id()
                ));

                if(!empty($orders)) {
                    $this->orderController->storage->softDelete($orders);
                    drupal_set_message($this->t('The orders under this subscription have been deleted.'));
                }
            }

            $entity->delete();

            // @todo Move to storage controller http://drupal.org/node/1988712
            $smartpan_user = entity_load('smartpan_user', $entity->getUserReference());
            $drupal_user = entity_load('user', $smartpan_user->getDrupalUserReference());
            drupal_set_message($this->t('Deleted subscription for the user %user_name.', array(
                '%user_name' => $drupal_user->get('name')->value
            )));
            watchdog('smartpan', 'Deleted subscription for the user %user_name.', array(
                '%user_name' => $drupal_user->get('name')->value
            ), WATCHDOG_NOTICE);
        } catch(\Exception $e) {
            return FALSE;
        }
    }
    
    public function softDelete($entity)
    {
        try {
            if(!empty($this->orderController)) {
                $orders = $this->orderController->storage->loadByProperties(array(
                    'subscription_id' => $entity->id()
                ));

                if(!empty($orders)) {
                    foreach($orders as $order) {
                        $this->orderController->softDelete($order);
                    }
                }
            }
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
    
    public function hardDelete($entity)
    {
        try {
            if(!empty($this->orderController)) {
                $orders = $this->orderController->storage->loadByProperties(array(
                    'subscription_id' => $entity->id()
                ));

                if(!empty($orders)) {
                    foreach($orders as $order) {
                        $this->orderController->hardDelete($order);
                    }
                    $this->orderController->storage->delete($orders);
                }
            }
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
}

?>
