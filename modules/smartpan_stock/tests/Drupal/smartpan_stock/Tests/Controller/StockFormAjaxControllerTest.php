<?php

/**
 * Description of StockFormAjaxControllerTest
 *
 * @author aswinvk28
 */

namespace Drupal\smartpan_stock\Tests\Controller;

use Smartpan\Tests\TestBundle\WebTest;
use Smartpan\Tests\TestBundle\Fixture\User as UserFixture;

class StockFormAjaxControllerTest extends WebTest
{
    private $smartpan_user;
    
    public static $modules = array('smartpan_account', 'product_subscription', 'braintree', 'user_subscription', 'smartpan_payment');
    
    public function __construct($test_id = NULL)
    {
        parent::__construct($test_id);
        $this->userFixture = new UserFixture();
    }
    
    public static function getInfo()
    {
        return array(
            'name' => 'Fetch Stock Symbols for Autocomplete',
            'description' => 'Unit test of fetching stock symbols similar to the input.',
            'group' => 'Stock'
        );
    }
    
    public function setUp()
    {
        parent::setUp();
        
        $this->smartpan_user = $this->userFixture->createAccount();
        $this->userFixture->register($this->smartpan_user->getDrupalUser());
        
        $this->smartpanLogin($this->smartpan_user->getDrupalUser());
    }
    
    public function testGetSymbol()
    {
        $json = $this->drupalPost('symbol', 'application/json, application/vnd.drupal-ajax, text/json', array(
            'symbol' => 'YH'
        ));
        
        $result = Drupal\Component\Utility\Json::decode($json);
        
        $this->assertTrue(is_array($result), 'Result is Array');
        $current = current($result);
        $this->assertTrue(is_object($current), 'Current Result is Object');
        
        $this->assertTrue(property_exists($current, 'symbol'), '"symbol" property exists for Current');
        $this->assertEqual($current->symbol, 'YHOO', 'YHOO is available in DB');
        
        $json = $this->drupalPost('symbol', 'application/json, application/vnd.drupal-ajax, text/json', array(
            'symbol' => 'GOO'
        ));
        
        $result = Drupal\Component\Utility\Json::decode($json);
        
        $this->assertTrue(is_array($result), 'Result is Array');
        $current = current($result);
        $this->assertTrue(is_object($current), 'Current Result is Object');
        
        $this->assertTrue(property_exists($current, 'symbol'), '"symbol" property exists for Current');
        $this->assertEqual($current->symbol, 'GOOG', 'GOOG is available in DB');
        
        $json = $this->drupalPost('symbol', 'application/json, application/vnd.drupal-ajax, text/json', array(
            'symbol' => 'AA'
        ));
        
        $result = Drupal\Component\Utility\Json::decode($json);
        
        $this->assertTrue(is_array($result), 'Result is Array');
        $current = current($result);
        $this->assertTrue(is_object($current), 'Current Result is Object');
        
        $this->assertTrue(property_exists($current, 'symbol'), '"symbol" property exists for Current');
        $this->assertEqual($current->symbol, 'AAPL', 'AAPL is available in DB');
    }
}
