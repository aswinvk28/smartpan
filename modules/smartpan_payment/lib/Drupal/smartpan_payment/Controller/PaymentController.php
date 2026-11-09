<?php

/**
 * Description of PaymentController
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends ControllerBase
{
    public $storage;
    
    public function __construct()
    {
        $this->storage = $this->entityManager()->getStorageController('smartpan_payment');
    }
    
    public function softDelete($entity)
    {
        try {
            $entity->set('status', 0);
            $savedStatus = $this->storage->save($entity);
            if(empty($savedStatus)) {
                throw new \Exception('Payment could not be made inactive');
            }
            return $savedStatus;
        } catch(\Exception $e) {
            drupal_set_message($e->getMessage(), 'error');
            return FALSE;
        }
    }
    
    public function createPaymentReceipt($order, $drupalUser, $method, $comment = '')
    {
        $paymentReceipt = entity_create('smartpan_payment', array(
            'order_id' => $order->id(),
            'uid' => $drupalUser->id(),
            'received' => time(),
            'status' => 1,
            'amount' => $order->getSubscription()->getPlan()->get('price')->value,
            'method' => $method,
            'comment' => $comment
        ));
        $savedStatus = $paymentReceipt->save();
        if(empty($savedStatus)) return FALSE;
        return $paymentReceipt;
    }
}

?>
