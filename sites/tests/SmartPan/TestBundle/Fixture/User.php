<?php

/**
 * Description of User
 *
 * @author aswinvk28
 */

namespace SmartPan\Tests\TestBundle\Fixture;

use SmartPan\Tests\TestBundle\WebTest;
use SmartPan\Tests\TestBundle\Fixture\Environment;
use Drupal\user_subscription\Access\SmartpanAccountAccessCheck;

class User extends WebTest
{
    private $environment;
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->environment = Environment::create(\Drupal::getContainer());
    }
    
    public function createAccount($userName, $firstName, $lastName)
    {
        $this->environment->createRoles();
        $this->environment->createVocab();
        
        $plan = $this->environment->createPlan();
        
        $this->drupalPostForm('account/create/free-trial', array(
            'plan' => $plan->id(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $userName
        ), t('Save'));
        $member_user = user_load_by_name($userName);
        $this->assertFalse($member_user == false, 'Member created');
        $smartpan_user = Environment::getSmartpanUserFromDrupalUser($member_user);
        $this->assertFalse($smartpan_user == false, 'Smartpan Member loaded');
        if(!empty($smartpan_user)) {
            $this->assertEqual($smartpan_user->get('first_name')->value, $firstName, 'Smartpan User First Name Matches');
            $this->assertEqual($smartpan_user->get('last_name')->value, $lastName, 'Smartpan User Last Name Matches');
        }
        $smartpan_user->setDrupaluser($member_user);
        return $smartpan_user;
    }
    
    public function register($account)
    {
        $_SESSION['pass_reset_' . $account->id()] = 'login';
        
        $this->drupalPostForm('user/'.$account->id().'/edit?pass-reset-token=login', array(
            'pass[pass1]' => 'password',
            'pass[pass2]' => 'password'
        ), t('Save'));
        
        $accessCheck = new SmartpanAccountAccessCheck();
        $this->assertTrue($accessCheck->checkIsMember($account), 'Created Member is a Member');
    }
    
    public function upgradePlan($plan_machine_name)
    {
        $this->drupalPostForm('account/upgrade', array(
            'plan' => $plan_machine_name
        ), t('Proceed to Payment'));
        
        $form_state_1 = \Drupal::formBuilder()->getFormStateDefaults();
        $form_state_1['rebuild'] = TRUE;
        $planUpgradeForm = \Drupal::formBuilder()->rebuildForm('plan_upgrade_form', $form_state_1);
        
        $this->assertNotNull($form_state_1['values']['form_token'], 'Form Token Exists in Form State');
        $this->assertNotNull($planUpgradeForm['#token'], 'Form Token exists in form');
        
        return $planUpgradeForm;
    }
    
    public function confirmOrder($planUpgradeForm)
    {
        $this->drupalPostForm('account/payment/'.$planUpgradeForm['#token'], array(), 
            t('Proceed to Confirmation'));
        
        $form_state_2 = \Drupal::formBuilder()->getFormStateDefaults();
        $form_state_2['rebuild'] = TRUE;
        $orderConfirmationForm = \Drupal::formBuilder()->rebuildForm('order_transaction_form', $form_state_2);
        
        $this->assertNotNull($form_state_2['values']['form_token'], 'Form Token Exists in Form State');
        $this->assertNotNull($orderConfirmationForm['#token'], 'Form Token exists in form');
        
        return $orderConfirmationForm;
    }
    
    public function confirmPayment($planUpgradeForm, $orderConfirmationForm)
    {
        $this->drupalPostForm('account/payment/confirm/'.$planUpgradeForm['#token'].'/'.$orderConfirmationForm['#token'], array(), 
            t('Confirm Payment'));
        
        $form_state_3 = \Drupal::formBuilder()->getFormStateDefaults();
        $form_state_3['rebuild'] = TRUE;
        $paymentConfirmationForm = \Drupal::formBuilder()->rebuildForm('order_transaction_confirm_form', $form_state_3);
        
        $this->assertNotNull($form_state_3['values']['form_token'], 'Form Token Exists in Form State');
        $this->assertNotNull($paymentConfirmationForm['#token'], 'Form Token exists in form');
    }
}
