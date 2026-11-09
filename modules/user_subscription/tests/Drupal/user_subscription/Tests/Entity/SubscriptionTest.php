<?php

/**
 * Description of SubscriptionTest
 *
 * @author aswinvk28
 */

namespace Drupal\user_subscription\Tests\Entity;

use Drupal\simpletest\DrupalUnitTestBase;
use SmartPan\Tests\TestBundle\Fixture\Environment as EnvironmentFixture;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use SmartPan\Tests\TestBundle\Mock\Environment as EnvironmentMock;

class SubscriptionTest extends DrupalUnitTestBase
{
    private $subscription;
    private $plan;
    
    private $userFixture;
    private $smartpan_user;
    
    public static function getInfo()
    {
        return array(
            'name' => 'Smartpan Subscription Test',
            'description' => 'Unit test for Smartpan Subscription Entity.',
            'group' => 'Smartpan Subscription'
        );
    }
    
    public function setUp()
    {
        $this->environment = new EnvironmentFixture();
        $this->mock = new EnvironmentMock();
        $this->userFixture = new UserFixture();
        $this->plan = $this->environment->createPlan('basic');
        $this->smartpan_user = $this->userFixture->createAccount(
            'aswinvk28@gmail.com', 
            'Test First Name', 
            'Test Last Name'
        );
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        $this->subscription = $this->environment->createSubscription($this->smartpan_user, $this->plan);
    }
    
    public function testSetValidity()
    {
        $this->subscription->set('validity', REQUEST_TIME);
        
        $this->subscription->setValidity();
        
        $this->assertTrue(
            $this->subscription->getValidity()->getTimestamp() >= REQUEST_TIME + 28*24*60*60 && 
            $this->subscription->getValidity()->getTimestamp() < REQUEST_TIME + 32*24*60*60, 
            'Validity for Free Trial is set properly for pre-set Validity'
        );
        
        $this->subscription->set('validity', '1970-01-01 00:00:00');
        
        $this->subscription->setValidity();
        
        $this->assertTrue(
            $this->subscription->getValidity()->getTimestamp() >= REQUEST_TIME + 28*24*60*60 && 
            $this->subscription->getValidity()->getTimestamp() < REQUEST_TIME + 32*24*60*60, 
            'Validity for Free Trial is set properly for empty validity'
        );
    }
    
    public function testSetValidityForPayment()
    {
        $this->subscription->set('validity', REQUEST_TIME);
        
        $status = new \stdClass();
        $status->value = 1;
        $paymentReceipt = $this->mock->createPaymentReceipt();
        $paymentReceipt->expects($this->mock->any())
            ->method('get')
            ->with('status')
            ->will($this->returnValue($status));
        
        $this->subscription->setValidityForPayment($paymentReceipt);
        
        $this->assertTrue(
            $this->subscription->getValidity()->getTimestamp() >= REQUEST_TIME + 28*24*60*60 && 
            $this->subscription->getValidity()->getTimestamp() < REQUEST_TIME + 32*24*60*60, 
            'Validity for Payment is set properly'
        );
    }
}
