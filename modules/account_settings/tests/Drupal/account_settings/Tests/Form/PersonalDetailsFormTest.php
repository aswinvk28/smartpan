<?php

/**
 * Description of PersonalDetailsFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\account_settings\Tests\Form;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;
use SmartPan\Tests\TestBundle\Fixture\Environment;

class PersonalDetailsFormTest extends WebTest
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
            'name' => 'Smartpan Account Personal Details Edit test',
            'description' => 'Unit test for editing personal details.',
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
    
    public function providerTestPersonalDetailsSubmitForm()
    {
        return array(
            array(
                'first_name' => 'Personal First Name',
                'last_name' => 'Personal Last Name'
            )
        );
    }
    
    public function testPersonalDetailsSubmitForm($submit)
    {
        $this->drupalPostForm('/account', $submit, t('Submit'), array(), array(), 'user-details-form');
        
        $drupalUser = $this->user->getDrupalUser();
        $this->user = Environment::getSmartpanUserFromDrupalUser($drupalUser);
        
        $this->assertEqual($this->user->get('first_name')->value, $submit['first_name'], 'Personal First Name Matches');
        $this->assertEqual($this->user->get('last_name')->value, $submit['last_name'], 'Personal Last Name Matches');
    }
}
