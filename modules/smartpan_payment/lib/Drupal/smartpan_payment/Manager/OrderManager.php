<?php

/**
 * Description of OrderManager
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_payment\Manager;

use Drupal\Core\Controller\ControllerBase;

class OrderManager extends ControllerBase
{
    private $orderStorage;
    
    public function __construct($storageController) {
        $this->orderStorage = $storageController;
    }
    
    public static function create(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        return new static($container->get('entity.manager')->getStorageController('smartpan_order'));
    }
    
    public function pushBraintreeSubscription($order, $smartpanUser, $subscription)
    {
        $orderIds = $this->orderStorage->fetchActiveOrderForBraintreeSubscription($smartpanUser, $subscription);
        
        if($order->get('status')->value == 0) {
            if(in_array($order->id(), $orderIds)) {
                $smartpanUser->set('subscription_id', NULL);
                if(!$smartpanUser->save()) {
                    throw new \Exception('User not saved');
                }
            }
        }
    }
}

?>
