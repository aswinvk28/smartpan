<?php

/**
 * Description of BraintreeAccountSiteInformationFormTest
 *
 * @author aswinvk28
 */

namespace Drupal\braintree\Tests\Form;

use Drupal\simpletest\WebTestBase;

class BraintreeAccountSiteInformationFormTest extends WebTestBase
{
    protected $admin_user;
    
    public static $modules = array('braintree');
    
    public function setUp()
    {
        parent::setUp();
        $this->admin_user = $this->drupalCreateUser(array(
            'access administration pages',
            'administer modules',
            'administer site configuration'
        ));
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Braintree Account Name Save test',
            'description' => 'Unit test of braintree account Name save into Braintree Account Config.',
            'group' => 'Braintree'
        );
    }
    
    public function checkBraintreeAccountNameSave()
    {
        $account_name = 'smartpan_sandbox';
        $dbAccountName = \Drupal::config('braintree.account')->get('name');
        $this->assertEqual($account_name, $dbAccountName, 'Braintree Account Name Saved');
    }
    
    public function testBraintreeAccountNameSave()
    {
        $account_name = 'smartpan_sandbox';
        $this->drupalLogin($this->admin_user);
        $this->drupalPostForm('admin/config/system/site-information', array(
            'site_name' => 'Smartpan Test Environment',
            'site_mail' => 'aswinvk28@gmail.com',
            'account_name' => $account_name
        ), t('Save configuration'));
        
        $this->checkBraintreeAccountNameSave($account_name);
    }
}

?>
