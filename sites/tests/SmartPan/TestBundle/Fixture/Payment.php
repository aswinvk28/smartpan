<?php

/**
 * Description of Payment
 *
 * @author aswinvk28
 */

namespace SmartPan\Tests\TestBundle\Fixture;

use Drupal\Core\Controller\ControllerBase;

class Payment extends ControllerBase
{
    const TEST_CARD = 4111111111111111;
    
    public function get()
    {
        
    }
    
    public function createOrder($smartpan_user, $subscription, $plan)
    {
        $order = entity_create('smartpan_order', array(
            'subscription_id' => $subscription->id(),
            'user_id' => $smartpan_user->id(),
            'product' => $plan->id(),
            'currency' => 'USD',
            'payment_method' => 'card',
            'order_total' => $plan->get('price')->value,
        ));
        if($order->save()) {
            return $order;
        }
        return FALSE;
    }
    
    public function createPaymentReceipt($smartpan_user, $order, $account)
    {
        $paymentReceipt = entity_create('smartpan_payment', array(
            'order_id' => $order->id(),
            'user_id' => $smartpan_user->id(),
            'account_id' => $account->id(),
            'status' => 1,
            'amount' => $order->get('order_total')->value,
            'method' => 'card'
        ));
        if($paymentReceipt->save()) {
            return $paymentReceipt;
        }
        return FALSE;
    }
}
