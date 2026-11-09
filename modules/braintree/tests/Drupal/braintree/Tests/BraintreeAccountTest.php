<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Drupal\braintree\Tests;

use Drupal\simpletest\DrupalUnitTestBase;
use Drupal\Core\Config\Config;

class BraintreeAccountTest extends DrupalUnitTestBase
{
    public static function getInfo()
    {
        return array(
            'name' => 'Braintree Account Service and Management test',
            'description' => 'Unit test of braintree account feature.',
            'group' => 'Braintree'
        );
    }
    
    public function setUp()
    {
        self::$modules = array('braintree');
        parent::setUp();
    }
    
    public function testAccountNameSave()
    {
        $account_name = 'smartpan_sandbox';
        
        $config = \Drupal::config('braintree.account');
        $this->assertTrue($config instanceof Config, 'Braintree Account Name Saved');
        
        \Drupal::config('braintree.account')
        ->set('name', $account_name)
        ->save();
        
        $dbAccountName = \Drupal::config('braintree.account')->get('name');
        $this->assertEqual($account_name, $dbAccountName, 'Braintree Account Name Matches');
    }
    
    public function testActiveAccount()
    {
        $account_keys = array(
            'account_name', 'environment', 'merchant_id',
            'public_key', 'private_key', 'cse_key'
        );
        $account_name = 'smartpan_sandbox';
        
        $braintreeAccount = \Drupal::service('braintree_account');
        
        \Drupal::config('braintree.account')
        ->set('name', $account_name)
        ->save();
        $dbAccountName = \Drupal::config('braintree.account')->get('name');
        $this->assertEqual($account_name, $dbAccountName, 'Braintree Account Name Matches');
        
        $this->assertNotNull($braintreeAccount, 'Braintree Account Service Not Null');
        
        $activeAccount = $braintreeAccount->getAccount();
        $this->assertNotNull($activeAccount, 'Braintree Active Account Not Null');
        
        foreach($account_keys as $key) {
            $this->assertTrue(array_key_exists($key, $activeAccount), "Braintree Account has $key");
        }
        $this->assertEqual($account_name, $activeAccount['account_name'], 'Braintree Account Name Matches');
    }
}

?>
