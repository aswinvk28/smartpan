<?php

/**
 * Description of Environment
 *
 * @author aswinvk28
 */

namespace SmartPan\Tests\TestBundle\Mock;

use Drupal\Tests\UnitTestCase;
use Drupal\user_subscription\Entity\Subscription;
use Drupal\product_subscription\Entity\Plan;

class Environment extends UnitTestCase
{
    public function createPlan()
    {
        $this->plan = $this->getMockBuilder('Drupal\product_subscription\Entity\Plan')
            ->disableOriginalConstructor()
            ->setMethods(array('id'))
            ->getMock();
        $this->plan->expects($this->any())
            ->method('id')
            ->will($this->returnValue(1));
    }
    
    public function createSubscription($plan)
    {
        $subscription = $this->getMockBuilder('Drupal\user_subscription\Entity\Subscription')
          ->disableOriginalConstructor()
          ->setMethods(array('getValidity', 'getPlan', 'id', 'setValidityWithTimezone'))
          ->getMock();
        
        $subscription->expects($this->any())
          ->method('id')
          ->will($this->returnValue(1));
        
        $subscription->expects($this->any())
          ->method('getPlan')
          ->will($this->returnValue($plan));
        
        return $subscription;
    }
    
    public function createPaymentReceipt()
    {
        $payment = $this->getMockBuilder('Drupal\smartpan_payment\Entity\PaymentReceipt')
            ->disableOriginalConstructor()
            ->setMethods(array('get', 'id'))
            ->getMock();
        
        $payment->expects($this->any())
            ->method('id')
            ->will($this->returnValue(1));
        
        return $payment;
    }
}
