<?php

/**
 * Description of BillingDetailsEditFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\account_settings\Tests\Form;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use SmartPan\Tests\TestBundle\Fixture\Environment;

class BillingDetailsEditFormTest extends WebTest
{
    private $userFixture;
    private $user;
    
    public static $modules = array(
        'smartpan_account', 'product_subscription', 
        'user_subscription', 'account_settings'
    );
    
    public static function getInfo()
    {
        return array(
            'name' => 'Smartpan Account Billing Edit test',
            'description' => 'Unit test for editing billing details.',
            'group' => 'Smartpan Account'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        $this->userFixture = new UserFixture();
        $this->user = $this->userFixture->createAccount('aswinvk28@gmail.com', 'First Name', 'Last Name');
        $this->smartpanLogin($this->user);
    }
    
    public function providerTestBillingDetailsSubmitForm()
    {
        return array(
            array(
                'billing_first_name' => 'Billing First Name',
                'billing_last_name' => 'Billing Last Name',
                'billing_email' => 'aswinvk28@gmail.com',
                'billing_phone' => '07858008436',
                'billing_company' => 'Billing Company',
                'billing_country' => 'UK',
                'billing_street1' => 'Street 1, 10-987 23/098',
                'billing_street2' => 'Street 2, 10-987 23/098',
                'billing_city' => 'City CityName',
                'billing_postal_code' => 'TW165BH'
            )
        );
    }
    
    public function testBillingDetailsSubmitForm($submit)
    {
        $this->drupalPostForm('/account', $submit, t('Submit'), array(), array(), 'billing-details-form');
        
        $drupalUser = $this->user->getDrupalUser();
        $this->user = Environment::getSmartpanUserFromDrupalUser($drupalUser);
        
        $this->assertEqual($this->user->get('billing_first_name')->value, $submit['billing_first_name'], 'Billing First Name Matches');
        $this->assertEqual($this->user->get('billing_last_name')->value, $submit['billing_last_name'], 'Billing Last Name Matches');
        $this->assertEqual($this->user->get('billing_email')->value, $submit['billing_email'], 'Billing Email Matches');
        $this->assertEqual($this->user->get('billing_phone')->value, $submit['billing_phone'], 'Billing Phone Matches');
        $this->assertEqual($this->user->get('billing_company')->value, $submit['billing_company'], 'Billing Company Matches');
        $this->assertEqual($this->user->get('billing_country')->value, $submit['billing_country'], 'Billing Country Matches');
        $this->assertEqual($this->user->get('billing_street1')->value, $submit['billing_street1'], 'Billing Street 1 Matches');
        $this->assertEqual($this->user->get('billing_street2')->value, $submit['billing_street2'], 'Billing Street 2 Matches');
        $this->assertEqual($this->user->get('billing_city')->value, $submit['billing_city'], 'Billing City Matches');
        $this->assertEqual($this->user->get('billing_postal_code')->value, $submit['billing_postal_code'], 'Billing Post Code Matches');
    }
}
