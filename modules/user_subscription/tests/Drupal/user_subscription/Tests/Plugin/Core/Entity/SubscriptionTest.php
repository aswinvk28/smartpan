<?php

/**
 * Description of SubscriptionTest
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Tests\Plugin\Core\Entity;

use Drupal\Tests\UnitTestCase;
use SmartPan\Tests\TestBundle\Mock\Environment;
use Drupal\user_subscription\Entity\Subscription;

class SubscriptionTest extends UnitTestCase
{
    const VALIDITY = '2015-10-01 12:00:00';
    
    private $subscription;
    
    public static function getInfo()
    {
        return array(
            'name' => 'Smartpan Subscription Object',
            'description' => 'Tests the Smartpan Subscription Object.',
            'group' => 'Smartpan Subscription'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        $this->environment = new Environment();
        $plan = $this->environment->createPlan();
        $this->subscription = $this->environment->createSubscription($plan);
        $this->createSubscription();
        
        $this->subscriptionEntity = new Subscription(array(
            'validity' => static::VALIDITY,
            'plan_id' => $plan->id(),
            'type' => 0,
            'user_id' => 0,
            'status' => 1
        ), 'subscription');
    }
    
    public function createSubscription()
    {
        $this->subscriptionEntity->setValidityWithTimezone('Europe/London');
        $this->subscription->expects($this->any())
          ->method('getValidity')
          ->will($this->returnValue($this->subscriptionEntity->getValidity()));
        return $this->subscription;
    }
    
    public function testSetValidityWithTimezone()
    {
        $this->assertEquals('2015-10-01 11:00:00', $this->subscription->getValidity(), 'Validity is set according to UTC');
    }
}
